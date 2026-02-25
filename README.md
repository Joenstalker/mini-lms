# MINI Library Management System

A modern, high-end Library Management System built with Laravel using the MVC architecture. This system features a stunning student-facing catalog, AJAX-driven administrator controls, and automated fine computation.

---

## 🚀 Getting Started

Follow these steps to get the project up and running on your local machine.

### 1. Prerequisites
Ensure you have the following installed:
- **PHP 8.2+**
- **Composer**
- **Node.js & NPM**
- **SQLite** (or any database of your choice)

### 2. Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/Joenstalker/mini-lms.git
   cd mini-lms
   ```

2. **Install PHP Dependencies**
   ```bash
   composer install
   ```

3. **Install Frontend Dependencies**
   ```bash
   npm install
   ```

4. **Environment Setup**
   Copy the example environment file and generate an application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Note: By default, the project is configured to use MySQL. Ensure your `.env` has `DB_CONNECTION=mysql`.*

5. **Run Migrations & Seeders**
   This will set up the database schema and populate it with sample books, authors, and students.
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Build Frontend Assets**
   ```bash
   npm run build
   ```

7. **Start the Application**
   ```bash
   php artisan serve
   ```
   Visit `http://localhost:8000` in your browser.

---

## 🔑 Default Credentials

### Administrator Login
- **URL**: `http://localhost:8000/login`
- **Email**: `test@example.com`
- **Password**: `password`

### Sample Student PINs
- Seeded students have a default PIN of `1234` for borrowing books.

---

## ✨ Features

- **Stunning Student Catalog**: A visually immersive, card-based interface with hero banners and real-time search.
- **AJAX CRUD**: Seamless book management for administrators without page reloads.
- **Smart Book Covers**: Automatic cover image assignment based on publishers during seeding.
- **Automated Fines**: Real-time fine calculation (₱10/day) for overdue books during the return process.
- **Fully Responsive**: Optimized for desktop, tablet, and mobile devices.

---

## 🛠 Tech Stack

- **Framework**: Laravel 11
- **Authentication**: Laravel Breeze
- **Styling**: Tailwind CSS & DaisyUI
- **Interactivity**: Alpine.js & SweetAlert2
- **Database**: Eloquent ORM (SQLite/MySQL/PostgreSQL)

---

## 📝 Activity Overview (MVC Assignment)

This project implements:
- **Models**: Proper relational design (Many-to-Many for Books-Authors, One-to-Many for Students-Transactions).
- **Controllers**: Clean logic for borrowing, returns, and inventory management.
- **Views**: High-end Blade templates with interactive Alpine.js components.
- **Business Logic**: Fine computation: `Fine = ₱10 × overdue_days × quantity`.

---

## 👤 Author
Developed as part of the Laravel MVC Architecture evaluation.
