<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'fullName' => 'Admin Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('admin');

        $teacher = User::create([
            'fullName' => 'Teacher Teacher',
            'email' => 'teacher@gmail.com',
            'bio' => 'Teacher bio',
            'title' => 'Teacher title',
            'password' => bcrypt('password'),
        ]);
        $teacher->assignRole('teacher');

        $student = User::create([
            'fullName' => 'Student Student',
            'email' => 'student@gmail.com',
            'password' => bcrypt('password'),
        ]);
        $student->assignRole('student');
    }
}
