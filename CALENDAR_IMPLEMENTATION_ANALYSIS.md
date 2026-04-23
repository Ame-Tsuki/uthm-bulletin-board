# UTHM Bulletin Board - Calendar Implementation Analysis

## Executive Summary

The application has **functional user calendars** (student and staff) but **no admin calendar exists**. The admin calendar route is configured but the view is missing. This document provides a detailed comparison and implementation guide.

---

## 1. USER CALENDAR IMPLEMENTATION

### 1.1 Student Calendar
**File**: [resources/views/student/calendar.blade.php](resources/views/student/calendar.blade.php)  
**Route**: `GET /student/calendar` → `student.calendar`  
**Access**: Role-based middleware (`role:student`)

#### Components:
1. **Sidebar Navigation**
   - Collapsible sidebar (80px collapsed, 280px expanded)
   - User profile card
   - Navigation menu (Dashboard, Announcements, Calendar, Events, Settings)
   - Mobile-responsive toggle

2. **Top Navigation Bar**
   - Month/year display
   - View toggle buttons (Month, Week, Day)
   - Navigation controls (Previous, Today, Next)
   - Add Event button
   - Notifications bell
   - User menu dropdown

3. **Calendar Controls**
   - Event type filters (6 types: All, Lectures, Deadlines, Exams, Social Events, Workshops)
   - Color-coded event dots
   - Active filter highlighting

4. **Calendar Grid**
   - 7-column grid (Sun-Sat)
   - 6-week layout (42 cells)
   - Day cells show:
     - Day number
     - Previous/next month grayed out
     - Today highlighted with blue border and background
     - Event listings (max 3 visible, "+N more" overflow indicator)
     - Add event button (+) per day

5. **Sidebar Features**
   - **Upcoming Events This Week**
     - Event list with date, type, title
     - Clickable event entries
   
   - **Statistics Cards**
     - Lectures This Month count
     - Upcoming Deadlines count
     - Exams Scheduled count
   
   - **Quick Actions**
     - Add New Event button
     - Export Calendar button (download)
     - Print Schedule button
   
   - **Event Categories Widget**
     - Category breakdown with counts
     - Color indicators for each type
   
   - **Sync Calendar**
     - Google Calendar integration button
     - Outlook calendar integration button

6. **Event Modal (Create/Edit)**
   - Event title (required)
   - Start date, end date (date pickers)
   - Start time, end time (time inputs)
   - Event type dropdown (required)
   - Location input
   - Description textarea
   - Reminder checkbox

#### Data Flow:
1. **Route**: Passes `user`, `events`, `academicYear`, `currentMonth`, `currentYear` to view
2. **API Endpoints Used**:
   - `GET /api/events?year={year}&month={month}` - Load month's events
   - `GET /api/events/upcoming` - Get upcoming events for the week
   - `GET /api/events/statistics` - Get event statistics
3. **Event Storage**: JavaScript fetches from `/api/events` endpoint (EventController)

#### Key JavaScript Classes:
```javascript
class Calendar {
  - init()                    // Initialize calendar
  - loadEvents()             // Fetch events from API
  - renderCalendar()         // Render month view
  - createDayCell()          // Create individual day cell
  - getEventClass()          // Return color class for event type
  - setupEventListeners()    // Setup navigation and filters
  - toggleView()             // Switch between month/week/day views
  - filterEvents()           // Filter by event type
  - renderUpcomingEvents()   // Render upcoming events widget
  - updateStatistics()       // Update stat cards
}
```

#### Styling:
- **Colors**:
  - Lectures: Blue (#0056a6)
  - Deadlines: Red (#dc3545)
  - Exams: Purple (#6f42c1)
  - Social: Green (#6ea342)
  - Workshops: Yellow (#ffc107)
  - Today: Light blue background (#e6f0fa) with blue border
  - Other month: Light gray (#f8f9fa)

- **Spacing**: Tailwind CSS classes (padding, margins, gaps)
- **Responsive**: Mobile-first with breakpoints at md (768px), lg (1024px)

---

### 1.2 Staff Calendar
**File**: [resources/views/staff/calendar.blade.php](resources/views/staff/calendar.blade.php)  
**Route**: `GET /staff/calendar` → `staff.calendar`  
**Access**: Role-based middleware (`role:staff`)

#### Difference from Student Calendar:
- **IDENTICAL STRUCTURE** - Staff calendar appears to be a complete duplicate
- Same components, same JavaScript, same styling
- Same route parameter: just passes `user`

#### Notable: Staff calendar passes fewer variables:
- Student: `user`, `events`, `academicYear`, `currentMonth`, `currentYear`
- Staff: `user` only

---

### 1.3 Club Admin Calendar (MISSING)
**File**: DOES NOT EXIST  
**Route**: `GET /club/calendar` → tries to load `club.calendar` view  
**Result**: Would throw view-not-found error

**Current Status**:
- Route defined but view missing
- Club admin dashboard exists but no calendar

---

## 2. ADMIN CALENDAR IMPLEMENTATION

### 2.1 Current Status: NOT IMPLEMENTED

**File**: [resources/views/admin/admin.blade.php](resources/views/admin/admin.blade.php) ❌  
**Route**: `GET /admin/calendar` → tries to load `admin.calendar` view ❌  
**Actual Result**: Route configured but view doesn't exist

### 2.2 Admin Dashboard (Fallback)
The admin currently has a comprehensive dashboard at `GET /admin/dashboard`:

**File**: [resources/views/admin/admin.blade.php](resources/views/admin/admin.blade.php)  
**Components**:
1. **Sidebar Navigation**
   - Admin panel header with gradient background
   - Navigation links:
     - Dashboard (active)
     - User Management (with unverified count badge)
     - Posts & Content
     - Moderation (with "12" pending badge)
     - Analytics
     - System Settings
     - Notifications
   - Admin profile card

2. **Top Navigation Bar**
   - Mobile menu toggle
   - Dashboard title "Dashboard Overview"
   - Notification bell (3 pending)
   - User dropdown menu

3. **Statistics Cards (6 cards)**
   - Total Users (gradient: blue)
   - Active Students (gradient: green)
   - Staff Members (gradient: orange)
   - Pending Verification (gradient: purple)
   - Total Announcements (gradient: red)
   - Total Events (gradient: indigo)

4. **Quick Actions**
   - Verify Users
   - Moderate Content
   - View Analytics

5. **Recent Users Table**
   - User name, email, role badge, status, action buttons

6. **System Status Panel**
   - Database status
   - Mail server status
   - Storage usage
   - API services status
   - Recent activity log

### 2.3 Missing: Admin Calendar Features
The admin calendar is NOT implemented. Needed features:
- [ ] Event management view
- [ ] Announcements timeline/calendar
- [ ] User activity calendar
- [ ] System events timeline
- [ ] Moderation events (reports, flags)
- [ ] Recurring events management
- [ ] Calendar sync controls

---

## 3. DETAILED COMPARISON TABLE

| Feature | Student Calendar | Staff Calendar | Admin Calendar |
|---------|-----------------|-----------------|-----------------|
| **File Location** | resources/views/student/calendar.blade.php | resources/views/staff/calendar.blade.php | ❌ MISSING |
| **Route** | /student/calendar | /staff/calendar | /admin/calendar (broken) |
| **Access Control** | role:student | role:staff | role:admin |
| **Month View** | ✅ Full grid | ✅ Full grid | ❌ None |
| **Week View** | ✅ Button (not implemented) | ✅ Button (not implemented) | ❌ None |
| **Day View** | ✅ Button (not implemented) | ✅ Button (not implemented) | ❌ None |
| **Event Types** | 6 types: lecture, deadline, exam, social, workshop, other | Same as student | ❌ None |
| **Event Filtering** | ✅ By type (6 buttons) | ✅ By type (6 buttons) | ❌ None |
| **Upcoming Events Widget** | ✅ This week's events sidebar | ✅ This week's events sidebar | ❌ None |
| **Statistics** | ✅ Lectures, Deadlines, Exams counts | ✅ Lectures, Deadlines, Exams counts | ✅ Different stats (users, announcements, events) |
| **Quick Actions** | Add, Export, Print | Add, Export, Print | Verify, Moderate, Analytics |
| **Event Modal** | ✅ Full form (title, dates, times, type, location, description) | ✅ Same form | ❌ None |
| **Sync Features** | ✅ Google & Outlook | ✅ Google & Outlook | ❌ None |
| **Event Categories Widget** | ✅ 5 categories with counts | ✅ 5 categories with counts | ❌ None |
| **API Endpoints Used** | /api/events, /api/events/upcoming, /api/events/statistics | /api/events, /api/events/upcoming, /api/events/statistics | ❌ None |
| **User Data Passed** | user, events, academicYear, currentMonth, currentYear | user | user |
| **Sidebar Style** | Collapsible (80px/280px) | Collapsible (80px/280px) | Dark theme (264px fixed) |
| **Responsive** | ✅ Mobile-first | ✅ Mobile-first | ✅ Mobile-first |
| **Color Scheme** | UTHM blue/custom theme | UTHM blue/custom theme | Gray/gradient colors |

---

## 4. API ENDPOINTS

### 4.1 Event API Endpoints (from routes/web.php)

```php
// All routes require auth + verified middleware
GET    /api/events                    // EventController@index
POST   /api/events                    // EventController@store
PUT    /api/events/{event}            // EventController@update
DELETE /api/events/{event}            // EventController@destroy
GET    /api/events/upcoming           // EventController@getUpcomingEvents
GET    /api/events/statistics         // EventController@getStatistics
```

### 4.2 Admin Event Management (from routes/admin.php)

```php
// Admin-only routes
GET    /admin/events/list             // AdminController@getEvents
DELETE /admin/events/{id}             // AdminController@deleteEvent
```

### 4.3 EventController Methods
Located in `app/Http/Controllers/EventController.php`

**Public Methods**:
- `index()` - List events by year/month (with optional filtering)
- `store()` - Create new event
- `update()` - Update existing event
- `destroy()` - Delete event
- `getUpcomingEvents()` - Get events for current week
- `getStatistics()` - Get event counts by type

---

## 5. EVENT MODEL & DATABASE

### 5.1 Event Model
**File**: `app/Models/Event.php`

**Fillable Fields**:
```php
'title',
'description',
'start_date',
'end_date',
'start_time',
'end_time',
'location',
'type',
'all_day',
'is_recurring',
'recurrence_pattern',
'color',
'user_id'
```

**Casts**:
```php
'start_date' => 'date',
'end_date' => 'date',
'start_time' => 'datetime:H:i',
'end_time' => 'datetime:H:i',
'all_day' => 'boolean',
'is_recurring' => 'boolean'
```

**Relationship**:
```php
public function user(): BelongsTo
```

### 5.2 Database Table Structure
**Table**: `events`

**Columns** (from migrations):
- id (PK)
- title (string)
- description (text, nullable)
- start_date (date)
- end_date (date, nullable)
- start_time (time, nullable)
- end_time (time, nullable)
- location (string, nullable)
- type (enum: lecture, deadline, exam, social, workshop, other)
- all_day (boolean, default: false)
- is_recurring (boolean, default: false)
- recurrence_pattern (string, nullable)
- color (string, nullable)
- user_id (FK → users.id)
- created_at, updated_at (timestamps)

---

## 6. KEY DIFFERENCES: User vs Admin

### User Calendar Purpose:
- Personal schedule management
- Track lectures, deadlines, exams
- View social events and workshops
- Add personal events

### Admin Calendar Should Be (PROPOSED):
- System-wide event overview
- Moderation timeline (reports, flags, actions)
- User verification status changes
- Announcement publication timeline
- Content moderation events
- System maintenance events
- Statistical trending

### Structural Differences:

| Aspect | User Calendar | Proposed Admin Calendar |
|--------|---------------|------------------------|
| **Data Source** | User's personal events | System events + moderation + announcements |
| **View Scope** | User's own events | All users' announcements, reports, system events |
| **Filtering** | By event type | By status (pending, resolved, approved, rejected) |
| **Actions** | Add, edit, delete own events | Approve, reject, resolve, delete, warn users |
| **Sidebar Info** | Next week's events, stats | Pending items, recent activity, system status |
| **Color Coding** | Event types | Event status (pending=yellow, approved=green, rejected=red) |

---

## 7. IMPLEMENTATION GUIDE: Creating Admin Calendar

### Step 1: Create Admin Calendar View
**File to Create**: `resources/views/admin/calendar.blade.php`

**Base Structure** (can be copied from student calendar):
```blade
<!DOCTYPE html>
<html>
<head>
    <!-- Same Tailwind + Font Awesome as student calendar -->
    <!-- Same CSS variables for colors -->
</head>
<body>
    <!-- Same sidebar layout (but admin styling) -->
    <!-- Same calendar grid structure -->
    <!-- Modified sidebar with admin-specific widgets -->
</body>
</html>
```

### Step 2: Adjust for Admin Data
Replace the JavaScript event loading with admin-specific endpoints:

```javascript
// Instead of: GET /api/events?year=...&month=...
// Use: GET /admin/events/list (already exists in admin routes)

// Instead of personal event types
// Use: status types (pending, approved, rejected, resolved)

// Add admin-specific filters:
// - Announcements by status
// - Reports by type
// - User verifications
// - Moderation actions
```

### Step 3: Add Admin-Specific Widgets
- **Pending Items**: Announcements awaiting approval
- **Recent Actions**: Admin actions timeline
- **System Events**: Maintenance, backups, errors
- **Moderation Queue**: Reports, flags, user warnings

### Step 4: Create New Admin API Endpoint (Optional)
Add to `EventController` if needed:
```php
public function getAdminEvents(Request $request) {
    // Return announcements, reports, and system events
    // Filter by date range
    // Include status information
}
```

Or use existing endpoints:
- `GET /admin/announcements/list`
- `GET /admin/events/list`
- (Need to create reports endpoint if reports table exists)

### Step 5: Adjust Route
The route already exists:
```php
Route::get('/admin/calendar', function () {
    $user = auth()->user();
    return view('admin.calendar', compact('user'));
})->name('admin.calendar');
```

Just ensure the view file exists once created.

---

## 8. FILES TO COPY/MODIFY

### To Create Admin Calendar:

1. **Copy**: `resources/views/student/calendar.blade.php`
   → **To**: `resources/views/admin/calendar.blade.php`

2. **Modify**: Calendar grid styling
   - Change event colors to status-based colors
   - Change sidebar widgets to admin-specific content
   - Update JavaScript to use `/admin/events/list` instead of `/api/events`

3. **Update**: Sidebar navigation
   - Change title to "Admin Calendar"
   - Change color scheme to admin theme (dark sidebar matching admin.blade.php)
   - Adjust links to admin routes

4. **JavaScript Changes**:
   - Replace `loadEvents()` to fetch from admin endpoints
   - Modify `filterEvents()` to filter by status instead of type
   - Update event type colors (status colors instead)

---

## 9. SHARED UTILITIES & COMPONENTS

### Shared CSS Classes (Tailwind):
- `.sidebar-collapsed` / `.sidebar-expanded` - Sidebar width control
- `.calendar-day` - Day cell styling
- `.calendar-transition` - Animation
- `.event-dot` - Event indicator dots
- `.event-lecture`, `.event-deadline`, etc. - Event type colors

### Shared Color Definitions:
```javascript
'uthm-blue': '#0056a6',
'uthm-blue-light': '#e6f0fa',
'uthm-green': '#6ea342',
'uthm-yellow': '#ffc107',
'uthm-red': '#dc3545',
'uthm-purple': '#6f42c1',
```

### Shared Functions (Could Be Extracted):
- Date formatting
- Event class assignment
- Sidebar toggle logic
- View mode switching

---

## 10. SUMMARY & RECOMMENDATIONS

### Current State:
✅ **WORKING**: Student and Staff calendars are fully functional
❌ **MISSING**: Admin calendar view (route exists, view doesn't)
⚠️ **INCOMPLETE**: Club admin calendar (view doesn't exist)

### Recommendations:

1. **SHORT TERM** (Create Admin Calendar):
   - Copy student calendar view to `resources/views/admin/calendar.blade.php`
   - Modify sidebar styling to match admin.blade.php dark theme
   - Update JavaScript to use admin API endpoints
   - Test calendar rendering with admin data

2. **MEDIUM TERM** (Enhance Admin Calendar):
   - Add moderation event timeline (from reports table)
   - Add announcement status timeline
   - Add user verification status timeline
   - Create admin-specific JavaScript class
   - Add status-based filtering (pending, approved, resolved, etc.)

3. **LONG TERM** (Optimize):
   - Extract shared calendar functionality into reusable component
   - Create base Calendar class with event type/color mapping
   - Create admin-specific Calendar subclass
   - Add calendar export/import for admin
   - Add calendar sharing controls
   - Implement full week/day views (currently buttons only)

### Which View to Replicate:
**Copy student calendar** because:
- ✅ Fully functional
- ✅ Responsive design works well
- ✅ JavaScript class is well-structured
- ✅ Event modal works correctly
- ✅ API endpoints are consistent

---

## 11. FILE PATHS REFERENCE

### Views:
```
resources/views/
├── student/calendar.blade.php        ✅ WORKING
├── staff/calendar.blade.php          ✅ WORKING
├── club/calendar.blade.php           ❌ MISSING
├── admin/calendar.blade.php          ❌ MISSING
└── admin/admin.blade.php             ✅ Dashboard (not calendar)
```

### Controllers:
```
app/Http/Controllers/
├── EventController.php               ✅ API endpoints
└── Admin/AdminController.php         ✅ Admin endpoints
```

### Models:
```
app/Models/
└── Event.php                         ✅ Event model
```

### Routes:
```
routes/
├── web.php                           ✅ Calendar routes + API
└── admin.php                         ✅ Admin routes
```

---

## 12. CURRENT ROUTE CONFIGURATION

```php
// User calendars (working)
GET /student/calendar  → view('student.calendar')      ✅
GET /staff/calendar    → view('staff.calendar')        ✅
GET /club/calendar     → view('club.calendar')         ❌ View missing

// Admin calendar (broken)
GET /admin/calendar    → view('admin.calendar')        ❌ View missing

// General redirect
GET /calendar          → Redirects to role-specific calendar

// API endpoints (all working)
GET    /api/events?year=...&month=...
GET    /api/events/upcoming
GET    /api/events/statistics
POST   /api/events
PUT    /api/events/{id}
DELETE /api/events/{id}
```

---

## Summary Table: Implementation Status

| Component | Status | Location | Notes |
|-----------|--------|----------|-------|
| Student Calendar | ✅ Complete | resources/views/student/calendar.blade.php | Fully functional with all features |
| Staff Calendar | ✅ Complete | resources/views/staff/calendar.blade.php | Identical to student calendar |
| Club Calendar | ❌ Missing | - | Need to create view |
| Admin Calendar | ❌ Missing | - | Need to create view; route configured |
| Event API | ✅ Complete | routes/web.php, EventController | All CRUD operations |
| Admin API | ✅ Partial | routes/admin.php, AdminController | Only list and delete; no create/update |
| Event Model | ✅ Complete | app/Models/Event.php | All fields and relationships |
| User Calendars' JavaScript | ✅ Complete | Inline in .blade files | Full Calendar class with CRUD |

---

*Last Updated: 2026-04-23*
*Analysis Scope: UTHM Bulletin Board Calendar Implementation*
