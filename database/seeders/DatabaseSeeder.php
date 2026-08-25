<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Clear permission tables with foreign key check
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('model_has_roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $guard = 'web';

        // Create permissions
        $permissionNames = [
            'manage users',
            'manage courses',
            'manage categories',
            'enroll courses',
            'view dashboard',
            'manage own courses',
            'grade students',
            'view reports',
        ];

        foreach ($permissionNames as $name) {
            $p = new Permission();
            $p->name = $name;
            $p->guard_name = $guard;
            $p->save();
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $admin = new Role();
        $admin->name = 'admin';
        $admin->guard_name = $guard;
        $admin->save();
        $admin->givePermissionTo($permissionNames);

        $instructor = new Role();
        $instructor->name = 'instructor';
        $instructor->guard_name = $guard;
        $instructor->save();
        $instructor->givePermissionTo(['manage own courses', 'grade students', 'view dashboard']);

        $student = new Role();
        $student->name = 'student';
        $student->guard_name = $guard;
        $student->save();
        $student->givePermissionTo(['enroll courses', 'view dashboard']);

        // Create users
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@lmsportal.com',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);
        $adminUser->assignRole('admin');

        $instructorUser = User::create([
            'name' => 'Dr. Sarah Johnson',
            'email' => 'instructor@lmsportal.com',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);
        $instructorUser->assignRole('instructor');

        $studentUser = User::create([
            'name' => 'John Student',
            'email' => 'student@lmsportal.com',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);
        $studentUser->assignRole('student');

        $this->call(LearningSeeder::class);
        $this->call(QuizzesSeeder::class);
        $this->call(SettingsSeeder::class);
        $this->call(BlogSeeder::class);
    }
}
