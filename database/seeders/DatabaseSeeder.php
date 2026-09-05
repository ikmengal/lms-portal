<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

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
            // Admin
            'view analytics',
            'manage messages',
            'manage users',
            'manage courses',
            'manage categories',
            'manage levels',
            'view reports',
            'manage roles',
            'manage settings',
            'view logs',
            // Instructor
            'manage own courses',
            'grade students',
            'view earnings',
            // Student
            'enroll courses',
            'view dashboard',
            'view learning',
            'view progress',
            'manage wishlist',
            'view activity',
            'view quiz history',
            'view assignment history',
            'view certificates',
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
        $admin->givePermissionTo([
            'view analytics', 'manage messages', 'manage users', 'manage courses',
            'manage categories', 'manage levels', 'view reports', 'manage roles', 'manage settings',
            'view dashboard', 'view logs',
        ]);
        $admin->syncPermissions($admin->permissions);

        $instructor = new Role();
        $instructor->name = 'instructor';
        $instructor->guard_name = $guard;
        $instructor->save();
        $instructor->givePermissionTo(['manage own courses', 'grade students', 'view earnings', 'view dashboard']);
        $instructor->syncPermissions($instructor->permissions);

        $student = new Role();
        $student->name = 'student';
        $student->guard_name = $guard;
        $student->save();
        $student->givePermissionTo([
            'enroll courses', 'view dashboard', 'view learning', 'view progress',
            'manage wishlist', 'view activity', 'view quiz history', 'view assignment history', 'view certificates',
        ]);
        $student->syncPermissions($student->permissions);

        // Create/update users
        $users = [
            ['admin@lmsportal.com', 'Admin User', 'admin'],
            ['instructor@lmsportal.com', 'Dr. Sarah Johnson', 'instructor'],
            ['instructor2@lmsportal.com', 'Michael Chen', 'instructor'],
            ['student@lmsportal.com', 'John Student', 'student'],
            ['emma.wilson@example.com', 'Emma Wilson', 'student'],
            ['david.kim@example.com', 'David Kim', 'student'],
            ['sofia.garcia@example.com', 'Sofia Garcia', 'student'],
            ['james.patel@example.com', 'James Patel', 'student'],
        ];

        foreach ($users as [$email, $name, $role]) {
            $user = User::updateOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => 'password', 'email_verified_at' => now()]
            );
            $user->syncRoles($role);
        }

        $this->call(LearningSeeder::class);
        $this->call(EnrollmentsSeeder::class);
        $this->call(QuizzesSeeder::class);
        $this->call(AssignmentsSeeder::class);
        $this->call(LearningActivitySeeder::class);
        $this->call(QuizActivitySeeder::class);
        $this->call(AssignmentActivitySeeder::class);
        $this->call(PaymentsSeeder::class);
        $this->call(LiveClassesSeeder::class);
        $this->call(GamificationSeeder::class);
        $this->call(GamificationActivitySeeder::class);
        $this->call(WishlistSeeder::class);
        $this->call(ContactMessagesSeeder::class);
        $this->call(NotificationsSeeder::class);
        $this->call(BlogSeeder::class);
        $this->call(SettingsSeeder::class);
    }
}