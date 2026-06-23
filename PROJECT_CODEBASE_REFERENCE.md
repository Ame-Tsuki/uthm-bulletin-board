# UTHM Bulletin Board - Technical Codebase Reference & Architecture Notes

This document serves as a comprehensive reference guide for the UTHM Bulletin Board project. It outlines the core architecture, key domain concepts, advanced integrations (Google Calendar & Content Moderation), permission matrices, and algorithmic logic implemented across the application.

---

## 1. Project Overview & Tech Stack
The **UTHM Bulletin Board** is a collaborative digital portal designed for students, staff, and club administrators. It organizes campus communications through announcements, a synchronized calendar system, interactive community groups, and moderated Q&A.

- **Backend Framework**: Laravel (PHP)
- **Database**: SQLite/MySQL (utilizes Eloquent ORM & database migrations)
- **Frontend Layer**: Blade Templates, Tailwind CSS (Vanilla styling), Vanilla JavaScript
- **API Services**: Google Calendar API (v3), .NET Moderation API, and Local Microservice Moderation API (FastAPI)

---

## 2. Key Domain Capabilities

### 2.1 Announcements & Lifecycle
Announcements are created by users with varying roles and go through a moderated lifecycle:
- **Official vs. Unofficial**: 
  - *Official*: Represents formal university notices. Admins and Staff can publish them instantly. Students and Club Admins can draft them, but publishing submits them to a **verification queue** (`status = pending_verification`).
  - *Unofficial*: General notices that can be published instantly by any authenticated user.
- **Priority States**: `urgent`, `important`, `normal` (governs visual tags and calendar reminder defaults).
- **Featured Carousel**: Toggled either by administrators or by authors (Staff/Club Admins for their own posts) via `is_featured` and sorted using a priority order (`featured_order`) and timestamp (`featured_at`).

### 2.2 Community Hub
A mini-social network layer for interest groups, clubs, or academic societies:
- **Group Privacy**:
  - `public`: Users join instantly, incrementing the group's `member_count`.
  - `by_approval`: Generates a `GroupJoinRequest` in a pending state, triggering database notifications to group admins/creators for approval/rejection.
- **Conversations & Interactions**: Members can create posts, add comments, and like/unlike posts (utilizing a transactional toggle mechanism). Group admins can also **pin** important posts at the top of the feed.

### 2.3 Interactive Q&A (FAQ)
Enables direct communication on announcements:
- Users can post questions directly on an announcement page.
- Questions undergo automated content moderation.
- A notification is sent to the announcement author. Once the author replies, the answer is displayed publicly as part of the announcement's FAQ section.

---

## 3. Advanced Integrations & Algorithms

### 3.1 Google Calendar Integration
Implemented in [GoogleCalendarService](app/Services/GoogleCalendarService.php) and managed by [GoogleCalendarController](app/Http/Controllers/GoogleCalendarController.php).

#### A. OAuth 2.0 Web Server Flow
- Uses the official Google APIs Client Library (`Google\Client`, `Google\Service\Calendar`).
- Requests scopes: `https://www.googleapis.com/auth/calendar` and `https://www.googleapis.com/auth/calendar.events`.
- Automatically refreshes expired tokens using the stored `google_refresh_token` and updates `google_token` and `google_token_expires_at` in the database.

#### B. Dynamic Custom Calendar Provisioning
Instead of polluting a user's primary Google Calendar, the system dynamically targets a dedicated calendar:
1. When a user connects, the service checks if a `google_calendar_id` exists in the database.
2. If missing, it uses the Google API to create a new calendar named **"UTHM Bulletin Board"** with time zone `Asia/Kuala_Lumpur`.
3. The newly generated ID is saved back to the user's profile for future synchronization events.

#### C. Bi-directional Synchronization
- **Push (Local to Google)**: When a user creates/updates a local event (or adds an announcement to their calendar), `syncEvent()` maps fields (title, location, description) and calls `insert` or `update` on the Google API using the stored `google_event_id`.
- **Pull (Google to Local)**: `syncFromGoogle()` queries Google Calendar events updated within the last 30 days.
  - If a remote event status is `'cancelled'`, the corresponding local event is deleted.
  - Otherwise, it creates or updates the local event, mapping details and caching the `google_event_id`.

```
[Local Event Created/Updated] ---> syncEvent() ---> Google API (Insert/Update)
                                                        |
                                                        v
[Google Calendar Event Deleted] <--- syncFromGoogle() <--- Check Remote Status ('cancelled')
```

---

### 3.2 iCalendar (.ics) Export Engine
Implemented in [AnnouncementCalendarService](app/Services/AnnouncementCalendarService.php). It parses announcement metadata and converts it into a valid iCalendar payload:

1. **Date Resolution**:
   - `DTSTART` and `DTEND` are formatted based on whether the event is an all-day event (all-day check detects if times span `00:00:00` to `23:59:59` or if it's an expiry date).
   - If **all-day**, values are printed as `VALUE=DATE:YYYYMMDD`.
   - If **time-bound**, values are converted to UTC and appended with a `Z` offset.
2. **UID Generation**: A persistent unique identifier is built (`announcement-{id}@uthm-bulletin-board`) to ensure external calendar clients do not duplicate records on subsequent imports.
3. **Escaping Syntax**: Converts newlines to literal `\n` characters and escapes semicolons, commas, and backslashes in compliance with RFC 5545 specifications.

---

### 3.3 Content Moderation System (Dual-Layer Algorithm)
Managed by [ModerationService](app/Services/ModerationService.php) and utilized across announcements, questions, and replies.

```
       [Raw User Text Input]
                 |
                 v
     +-----------------------+
     |  Cultural Whitelist   | === (Term matched?) ===> [Allowed (Fail-Fast)]
     +-----------------------+
                 | No
                 v
     +-----------------------+
     | Blocked Words Filter  | === (Word matched?) ===> [Blocked & Logged]
     +-----------------------+
                 | No
                 v
     +-----------------------+
     | .NET Moderation API   |
     |   Toxicity Analysis   | === (Toxicity >= 0.85) ===> [Blocked & Warning Logged]
     +-----------------------+
                 |
                 v (Toxicity < 0.85)
             [Allowed] 
    *(0.60-0.85 flagged as borderline)*
```

#### Layer 1: Local Static Validation
- **Cultural Whitelist Check**: To minimize false positives, the algorithm scans for specific cultural terms (e.g., *gawai, hari gawai, dayak, sarawak, harvest festival, raya, deepavali*). If any are found, it skips further checks and returns an approved status.
- **Blocked Words Filter**: Compiles a standard library of English and Malay inappropriate words (*stupid, idiot, bodoh, gila, sial, biadab*).
  - A substring match immediately rejects the request.
  - Rejection triggers a warnings log containing structured metadata (IP address, user ID, word matched, and payload preview) for audits.

#### Layer 2: Nuanced AI Moderation API
- If static checks pass, the service fires an HTTP POST request to a .NET Moderation API (`MODERATION_API_URL`).
- **Toxicity Score Thresholding**:
  - The API returns a `toxicityScore` and matching rules.
  - If the score is **>= 0.85**, content is blocked, and an AI moderation warning log is written.
  - If the score is **between 0.60 and 0.85**, the content is marked as allowed, but logged as **borderline** to let moderators review it later.
  - If the external service is down, the system fails-open (allows content) but logs a connection error.

---

### 3.4 Local Microservice Moderation Client
Located in [LocalModService](app/Services/LocalModService.php), this client connects to `http://127.0.0.1:8002/analyze`. It passes text along with selected classifiers (`['toxicity', 'pii', 'spam']`) to inspect content for spam and personally identifiable information (PII) before flagging the entry.

---

## 4. Role-Based Permissions & Actions

The application enforces a strict role-based hierarchy:

| User Role | Announcements Capabilities | Community Hub Capabilities | Calendar Capabilities |
| :--- | :--- | :--- | :--- |
| **Admin** | Approve/Reject pending notices, Feature/Unfeature any post, Ban announcements, Create/Update users, Bypass moderation logs. | Create/Delete any group, Pin posts in any group, Remove members. | Full control, View all calendar events. |
| **Staff** | Publish official/unofficial posts immediately, Feature own published posts, Review & approve pending student posts. | Join groups, Create posts, comments, likes. | Add own events, Sync with Google & Outlook. |
| **Club Admin** | Draft official posts (requires staff verification), Publish unofficial posts, Feature own published posts. | Create own groups, Moderate members/join requests, Pin posts in own groups. | Add own events, Sync with Outlook/Google. |
| **Student** | Draft official posts (requires staff verification), Publish unofficial posts. | Join public/by_approval groups, Create posts, comments, likes. | Add personal events, Sync with Outlook/Google. |

---

## 5. Architectural Design Patterns & Skills

1. **MVC Pattern (Model-View-Controller)**: Keeps the separation between representation (Blade), data structure (Eloquent), and request orchestration (Controllers).
2. **Service Layer Pattern**: Integrations (Google Calendar APIs, ICS generation, and Content Moderation APIs) are abstracted into separate service classes. This isolates changes in third-party endpoints from controllers.
3. **Database Transaction Integrity**: Sensitive multi-query operations (such as group creation and approvals) are wrapped inside Laravel database transactions (`DB::beginTransaction()` / `DB::commit()` / `DB::rollback()`) to prevent data inconsistency during execution failures.
4. **Resilient Schema Fallbacks**: Controller queries check table columns dynamically (e.g. `Schema::hasColumn('announcements', 'is_official')`) to remain backwards-compatible during step-by-step database migration rollouts.
5. **Event-Driven Messaging & Notifications**: Dispatches community notifications (`CommunityNotification`) and announcement notifications (`AnnouncementNotification`) dynamically, targeting only relevant stakeholders (e.g., excluding the author, or only targeting moderation staff).
