<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            ['name' => 'John Doe', 'email' => '2301102312@student.buksu.edu.ph', 'student_id' => '2301102312', 'phone' => '09123456780', 'address' => 'Manila, Philippines', 'profile_image' => 'images/John Doe.png'],
            ['name' => 'Jane Smith', 'email' => '2201102313@student.buksu.edu.ph', 'student_id' => '2201102313', 'phone' => '09123456781', 'address' => 'Quezon City, Philippines', 'profile_image' => 'images/Jane Smith.png'],
            ['name' => 'Mark Wilson', 'email' => '2401102314@student.buksu.edu.ph', 'student_id' => '2401102314', 'phone' => '09123456782', 'address' => 'Cebu City, Philippines', 'profile_image' => 'images/Mark Wilson.png'],
            ['name' => 'Sarah Johnson', 'email' => '2501102315@student.buksu.edu.ph', 'student_id' => '2501102315', 'phone' => '09123456783', 'address' => 'Davao City, Philippines', 'profile_image' => 'images/Sarah Johnson.png'],
            ['name' => 'Chris Evans', 'email' => '2330110231@student.buksu.edu.ph', 'student_id' => '2330110231', 'phone' => '09123456784', 'address' => 'Makati City, Philippines', 'profile_image' => 'images/Chris Evans.png'],
            ['name' => 'Emily Blunt', 'email' => '2401102317@student.buksu.edu.ph', 'student_id' => '2401102317', 'phone' => '09123456785', 'address' => 'Pasig City, Philippines', 'profile_image' => 'images/Emily Blunt.png'],
            ['name' => 'Tony Stark', 'email' => '2501102318@student.buksu.edu.ph', 'student_id' => '2501102318', 'phone' => '09123456786', 'address' => 'Taguig City, Philippines', 'profile_image' => 'images/Tony Stark.png'],
            ['name' => 'Steve Rogers', 'email' => '2201102319@student.buksu.edu.ph', 'student_id' => '2201102319', 'phone' => '09123456787', 'address' => 'Baguio City, Philippines', 'profile_image' => 'images/Steve Rogers.png'],
            ['name' => 'Natasha Romanoff', 'email' => '2101102320@student.buksu.edu.ph', 'student_id' => '2101102320', 'phone' => '09123456788', 'address' => 'Mandaluyong City, Philippines', 'profile_image' => 'images/Natasha Romanoff.png'],
            ['name' => 'Bruce Banner', 'email' => '2501102321@student.buksu.edu.ph', 'student_id' => '2501102321', 'phone' => '09123456789', 'address' => 'Iloilo City, Philippines', 'profile_image' => 'images/Bruce Banner.png'],
        ];

        foreach ($students as $student) {
            Student::updateOrCreate(['email' => $student['email']], $student);
        }
    }
}
