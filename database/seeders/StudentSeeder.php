<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            ['name' => 'John Doe', 'email' => 'john@example.com', 'phone' => '09123456789', 'address' => 'Manila, Philippines'],
            ['name' => 'Jane Smith', 'email' => 'jane@example.com', 'phone' => '09234567890', 'address' => 'Quezon City, Philippines'],
            ['name' => 'Mark Wilson', 'email' => 'mark@example.com', 'phone' => '09345678901', 'address' => 'Cebu City, Philippines'],
            ['name' => 'Sarah Johnson', 'email' => 'sarah@example.com', 'phone' => '09456789012', 'address' => 'Davao City, Philippines'],
            ['name' => 'Chris Evans', 'email' => 'chris@example.com', 'phone' => '09567890123', 'address' => 'Makati City, Philippines'],
            ['name' => 'Emily Blunt', 'email' => 'emily@example.com', 'phone' => '09678901234', 'address' => 'Pasig City, Philippines'],
            ['name' => 'Tony Stark', 'email' => 'tony@example.com', 'phone' => '09789012345', 'address' => 'Taguig City, Philippines'],
            ['name' => 'Steve Rogers', 'email' => 'steve@example.com', 'phone' => '09890123456', 'address' => 'Baguio City, Philippines'],
            ['name' => 'Natasha Romanoff', 'email' => 'natasha@example.com', 'phone' => '09901234567', 'address' => 'Mandaluyong City, Philippines'],
            ['name' => 'Bruce Banner', 'email' => 'bruce@example.com', 'phone' => '09012345678', 'address' => 'Iloilo City, Philippines'],
        ];

        foreach ($students as $student) {
            // Generate a random 6-digit PIN
            $student['pin'] = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            
            Student::updateOrCreate(['email' => $student['email']], $student);
        }
    }
}
