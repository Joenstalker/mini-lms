<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    public function run(): void
    {
        $authors = [
            'David R. Thompson', 'Maria L. Cruz',
            'Robert K. Hayes', 'Linda M. Brooks',
            'Samuel T. Nguyen', 'Carlos M. Rivera',
            'Harold J. Simmons', 'Andrea P. Lopez',
            'Kevin L. Ramirez', 'John Paul Santos',
            'Melissa A. Grant', 'Henry T. Choi',
            'Patricia A. Williams', 'Grace D. Mendoza',
            'Teresa M. Villanueva', 'Angela R. Flores',
            'Michael J. Carter',
            'Ramon S. Delgado', 'Cristina B. Aquino',
            'Jonathan P. Reed',
            'Laura M. Bennett', 'Victor A. Gomez'
        ];

        foreach ($authors as $name) {
            Author::firstOrCreate(['name' => $name], [
                'bio' => "Expert researcher and author in their respective field."
            ]);
        }
    }
}
