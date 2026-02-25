<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name'     => 'Librarian Admin',
            'email'    => 'admin@library.com',
            'password' => Hash::make('password'),
        ]);

        // Authors
        $authors = [
            ['name' => 'Jose Rizal',         'bio' => 'National hero of the Philippines and prolific author.'],
            ['name' => 'Nick Joaquin',        'bio' => 'Award-winning Filipino writer known for his historical and literary works.'],
            ['name' => 'F. Sionil Jose',      'bio' => 'One of the most widely read Filipino authors in the English language.'],
            ['name' => 'Lualhati Bautista',   'bio' => 'Filipino novelist known for her socially conscious works.'],
        ];

        foreach ($authors as $a) {
            Author::create($a);
        }

        // Books
        $books = [
            ['title' => 'Noli Me Tangere',         'isbn' => '978-971-10-0612-4', 'author_id' => 1, 'description' => 'A social novel about the Philippines under Spanish rule.', 'quantity' => 5],
            ['title' => 'El Filibusterismo',        'isbn' => '978-971-10-0613-1', 'author_id' => 1, 'description' => 'The sequel to Noli Me Tangere.', 'quantity' => 4],
            ['title' => 'The Woman Who Had Two Navels', 'isbn' => '978-0-8248-2887-1', 'author_id' => 2, 'description' => 'A story of Philippine identity and history.', 'quantity' => 3],
            ['title' => 'Mass',                    'isbn' => null, 'author_id' => 3, 'description' => 'Part of the Rosales Saga.', 'quantity' => 2],
            ['title' => 'Bata, Bata, Pa\'ano Ka Ginawa?', 'isbn' => '978-971-27-1003-7', 'author_id' => 4, 'description' => 'A novel about Filipino women and societal expectations.', 'quantity' => 3],
        ];

        foreach ($books as $b) {
            Book::create(array_merge($b, ['available_quantity' => $b['quantity']]));
        }

        // Students
        $students = [
            ['name' => 'Juan dela Cruz',    'student_number' => '2024-0001', 'email' => 'juan@school.edu', 'phone' => '09171234567'],
            ['name' => 'Maria Santos',      'student_number' => '2024-0002', 'email' => 'maria@school.edu', 'phone' => '09181234567'],
            ['name' => 'Pedro Reyes',       'student_number' => '2024-0003', 'email' => 'pedro@school.edu', 'phone' => null],
            ['name' => 'Ana Garcia',        'student_number' => '2024-0004', 'email' => 'ana@school.edu',   'phone' => '09191234567'],
        ];

        foreach ($students as $s) {
            Student::create($s);
        }

        // Sample borrowings
        // Active borrowing
        Borrowing::create([
            'student_id'  => 1,
            'book_id'     => 1,
            'borrowed_at' => Carbon::today()->subDays(3),
            'due_date'    => Carbon::today()->addDays(11),
            'status'      => 'borrowed',
            'fine_amount' => 0,
        ]);
        Book::find(1)->decrement('available_quantity');

        // Returned borrowing
        Borrowing::create([
            'student_id'  => 2,
            'book_id'     => 2,
            'borrowed_at' => Carbon::today()->subDays(20),
            'due_date'    => Carbon::today()->subDays(6),
            'returned_at' => Carbon::today()->subDays(6),
            'status'      => 'returned',
            'fine_amount' => 0,
        ]);

        // Overdue borrowing
        Borrowing::create([
            'student_id'  => 3,
            'book_id'     => 3,
            'borrowed_at' => Carbon::today()->subDays(20),
            'due_date'    => Carbon::today()->subDays(6),
            'status'      => 'overdue',
            'fine_amount' => 0,
        ]);
        Book::find(3)->decrement('available_quantity');
    }
}
