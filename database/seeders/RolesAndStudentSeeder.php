<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndStudentSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);

        // Create a demo class
        $class = SchoolClass::firstOrCreate(
            ['name' => 'XII IPA 1'],
            ['grade' => 'XII']
        );

        // Demo student
        $student = User::firstOrCreate(
            ['email' => 'student@sentosa.com'],
            [
                'name'     => 'Siswa Test',
                'password' => bcrypt('password'),
                'class_id' => $class->id,
                'email_verified_at' => now(),
            ]
        );
        $student->syncRoles([$studentRole]);

        // Give admin user teacher role for testing
        $admin = User::where('email', 'admin@sentosa.com')->first();
        if ($admin) {
            $admin->syncRoles([$teacherRole]);
        }
    }
}
