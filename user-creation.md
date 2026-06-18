# User Creation Guide

How to create new admin, teacher, and student accounts. All accounts use the password `Password123!`.

---

## Prerequisites

- Access to the project via terminal (`php artisan`)
- Laravel Tinker or the Filament admin panel (`/admin`)
- The `admin` role must already exist in the database

All passwords below use bcrypt via the `'password' => 'hashed'` cast on the `User` model. When creating via Filament, Laravel hashes automatically. When using Tinker, always `bcrypt()` explicitly.

---

## 1. Create Admin

The `admin` role must exist before assigning it. If you haven't already, create it:

```bash
php artisan tinker --execute="Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);"
```

### Option A: Via Tinker (CLI)

```bash
php artisan tinker
```

```php
use App\Models\User;
use Spatie\Permission\Models\Role;

$user = User::create([
    'name'     => 'Admin Baru',
    'email'    => 'admin@example.com',
    'password' => bcrypt('Password123!'),
]);

$user->assignRole('admin');
```

### Option B: Via Filament Panel

1. Go to `/admin/users/create`
2. Fill in **Name**, **Email address**, **Password** (`Password123!`)
3. Click **Create**
4. Open Tinker and assign the role:

```bash
php artisan tinker --execute="App\Models\User::where('email', 'admin@example.com')->first()->assignRole('admin');"
```

---

## 2. Create Teacher

The `teacher` role is seeded automatically by `RolesAndStudentSeeder`. Run it first if needed:

```bash
php artisan db:seed --class=RolesAndStudentSeeder
```

### Option A: Via Tinker (CLI)

```bash
php artisan tinker
```

```php
use App\Models\User;

$user = User::create([
    'name'     => 'Guru Baru',
    'email'    => 'guru@example.com',
    'password' => bcrypt('Password123!'),
]);

$user->assignRole('teacher');
```

### Option B: Via Filament Panel

1. Go to `/admin/users/create`
2. Fill in **Name**, **Email address**, **Password** (`Password123!`)
3. Click **Create**
4. Assign the teacher role:

```bash
php artisan tinker --execute="App\Models\User::where('email', 'guru@example.com')->first()->assignRole('teacher');"
```

---

## 3. Create Student

The `student` role is seeded automatically by `RolesAndStudentSeeder`. Run it first if needed:

```bash
php artisan db:seed --class=RolesAndStudentSeeder
```

A student requires a **Class** assignment (`class_id`).

### Option A: Via Tinker (CLI)

```bash
php artisan tinker
```

```php
use App\Models\User;

$user = User::create([
    'name'     => 'Siswa Baru',
    'email'    => 'siswa@example.com',
    'password' => bcrypt('Password123!'),
    'class_id' => 1, // Replace with actual class ID
]);

$user->assignRole('student');
```

> Check available classes: `App\Models\SchoolClass::all(['id', 'name'])`

### Option B: Via Filament Panel

1. Go to `/admin/users/create`
2. Fill in **Name**, **Email address**, **Password** (`Password123!`)
3. Select the student's class in the **Class** dropdown
4. Click **Create**
5. Assign the student role:

```bash
php artisan tinker --execute="App\Models\User::where('email', 'siswa@example.com')->first()->assignRole('student');"
```

---

## Role Access Summary

| Role    | Filament panel (`/admin`) | Student dashboard (`/student`) |
|---------|---------------------------|--------------------------------|
| admin   | Yes                       | No                             |
| teacher | Yes                       | No                             |
| student | No                        | Yes                            |

---

## Quick Reference

```bash
# Assign role to existing user
php artisan tinker --execute="App\Models\User::where('email', 'user@example.com')->first()->assignRole('teacher');"

# Sync (replace) all roles
php artisan tinker --execute="App\Models\User::where('email', 'user@example.com')->first()->syncRoles(['teacher']);"

# Remove all roles
php artisan tinker --execute="App\Models\User::where('email', 'user@example.com')->first()->syncRoles([]);"

# List user roles
php artisan tinker --execute="App\Models\User::where('email', 'user@example.com')->first()->getRoleNames();"
```
