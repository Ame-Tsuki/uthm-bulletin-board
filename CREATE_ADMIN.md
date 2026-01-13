# How to Login as Admin

## Method 1: Using Database Seeder (Recommended)

1. **Run the database seeder** to create an admin user:
   ```bash
   php artisan db:seed
   ```

2. **Login credentials**:
   - **Email/UTHM ID**: `admin@uthm.edu.my` or `ADMIN001`
   - **Password**: `password123`

3. **Login at**: `http://127.0.0.1:8000/login`

4. After login, you'll be automatically redirected to the admin dashboard at `/admin/dashboard`

## Method 2: Create Admin via Tinker

If you prefer to create the admin user manually:

```bash
php artisan tinker
```

Then run:
```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'uthm_id' => 'ADMIN001',
    'name' => 'Admin User',
    'email' => 'admin@uthm.edu.my',
    'password' => Hash::make('password123'),
    'role' => 'admin',
    'is_verified' => true,
    'email_verified_at' => now(),
]);
```

## Method 3: Update Existing User to Admin

If you already have a user account and want to make it an admin:

```bash
php artisan tinker
```

Then run:
```php
use App\Models\User;

$user = User::where('email', 'your-email@example.com')->first();
$user->role = 'admin';
$user->is_verified = true;
$user->save();
```

## Important Notes

- Admin routes require:
  - Authentication (`auth` middleware)
  - Email verification (`verified` middleware)
  - Admin role (`role:admin` middleware)

- Make sure your admin user has:
  - `role` = `'admin'`
  - `is_verified` = `true`
  - `email_verified_at` is set (not null)

- After login, admin users are automatically redirected to `/admin/dashboard`

## Admin Routes

- Dashboard: `/admin/dashboard`
- User Management: `/admin/users`
- Statistics: `/admin/statistics`
- Recent Activity: `/admin/recent-activity`
