<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Author;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        // Maps publisher name to the exact image filename in public/build/images/
        $coverImages = [
            'Vladiner Kvint'     => 'Vladimir Kvint.png',
            'Wiley'              => 'Wiley.png',
            'Wiley & Sons'       => 'Wiley and Sons.png',
            'H. B. Harrison'     => 'H. B. Harison.png',
            'Alexandre Malavasi' => 'Alexandre Malavasi.png',
        ];

        $books = [
            [
                'title' => 'Strategic Management in Emerging Markets',
                'publisher' => 'Vladiner Kvint',
                'published_year' => 2021,
                'authors' => ['David R. Thompson', 'Maria L. Cruz'],
                'description' => 'A comprehensive guide to strategic planning, competitive analysis, and business sustainability in emerging economies. Includes real-world case studies from Southeast Asia and global markets.',
            ],
            [
                'title' => 'Principles of Financial Accounting 3rd edition',
                'publisher' => 'Wiley',
                'published_year' => 2020,
                'authors' => ['Robert K. Hayes', 'Linda M. Brooks'],
                'description' => 'Introduces core accounting concepts including financial statements, bookkeeping cycles, and ethical financial reporting practices for business students.',
            ],
            [
                'title' => 'Fundamentals of Electrical Engineering',
                'publisher' => 'Wiley & Sons',
                'published_year' => 2019,
                'authors' => ['Samuel T. Nguyen', 'Carlos M. Rivera'],
                'description' => 'Covers circuit analysis, electrical systems, and practical applications in modern industries. Includes problem-solving exercises and laboratory integration.',
            ],
            [
                'title' => 'Structural Analysis and Design',
                'publisher' => 'H. B. Harrison',
                'published_year' => 2022,
                'authors' => ['Harold J. Simmons', 'Andrea P. Lopez'],
                'description' => 'Explains principles of structural mechanics, load analysis, and safe building design for civil engineering students.',
            ],
            [
                'title' => 'Modern Full-Stack Web Development with ASP.NET Core',
                'publisher' => 'Alexandre Malavasi',
                'published_year' => 2023,
                'authors' => ['Kevin L. Ramirez', 'John Paul Santos'],
                'description' => 'Focuses on full-stack web development using modern technologies, MVC architecture, and secure coding practices.',
            ],
            [
                'title' => 'Introduction to Cybersecurity and Network Defense',
                'publisher' => 'Packt Publishing',
                'published_year' => 2022,
                'authors' => ['Melissa A. Grant', 'Henry T. Choi'],
                'description' => 'Explores cybersecurity fundamentals, threat detection, cryptography basics, and defensive strategies for IT professionals.',
            ],
            [
                'title' => 'Fundamentals of Nursing Practice',
                'publisher' => 'Elsevier',
                'published_year' => 2021,
                'authors' => ['Patricia A. Williams', 'Grace D. Mendoza'],
                'description' => 'Provides foundational nursing theories, patient care procedures, infection control practices, and ethical standards in healthcare.',
            ],
            [
                'title' => 'Community Health Nursing in the Philippines',
                'publisher' => 'Rex Book Store',
                'published_year' => 2020,
                'authors' => ['Teresa M. Villanueva', 'Angela R. Flores'],
                'description' => 'Discusses public health systems, community-based healthcare, and nursing roles in Philippine local government units.',
            ],
            [
                'title' => 'A History of Southeast Asia',
                'publisher' => 'Oxford University Press',
                'published_year' => 2018,
                'authors' => ['Michael J. Carter'],
                'description' => 'A historical overview of Southeast Asian civilizations, colonial periods, independence movements, and modern political developments.',
            ],
            [
                'title' => 'Philippine Political and Cultural History',
                'publisher' => 'University of the Philippines Press',
                'published_year' => 2021,
                'authors' => ['Ramon S. Delgado', 'Cristina B. Aquino'],
                'description' => 'Examines the political evolution, cultural identity, and social transformations of the Philippines from pre-colonial times to the present.',
            ],
            [
                'title' => 'Ethics in the Modern World',
                'publisher' => 'Routledge',
                'published_year' => 2019,
                'authors' => ['Jonathan P. Reed'],
                'description' => 'Discusses moral philosophy, ethical decision-making, and contemporary ethical issues in business, science, and society.',
            ],
            [
                'title' => 'Introduction to Research Methods',
                'publisher' => 'Sage Publications',
                'published_year' => 2022,
                'authors' => ['Laura M. Bennett', 'Victor A. Gomez'],
                'description' => 'Covers qualitative and quantitative research design, data collection methods, statistical interpretation, and academic writing standards.',
            ],
        ];

        foreach ($books as $bookData) {
            $authors = $bookData['authors'];
            unset($bookData['authors']);
            
            // Set default stock
            $bookData['total_quantity'] = 10;
            $bookData['available_quantity'] = 10;

            // Assign cover image based on publisher, or use the default
            $imageName = $coverImages[$bookData['publisher']] ?? 'default-book-cover.png';
            $bookData['cover_image'] = asset("images/{$imageName}");

            $book = Book::firstOrCreate(['title' => $bookData['title']], $bookData);

            $authorIds = [];
            foreach ($authors as $authorName) {
                $author = Author::where('name', $authorName)->first();
                if ($author) {
                    $authorIds[] = $author->id;
                }
            }
            
            if (!empty($authorIds)) {
                $book->authors()->sync($authorIds);
            }
        }
    }
}
