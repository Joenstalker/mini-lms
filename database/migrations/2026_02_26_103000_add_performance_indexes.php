<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add index on borrow_transactions for status queries
        Schema::table('borrow_transactions', function (Blueprint $table) {
            $table->index('status', 'idx_borrow_transactions_status');
            $table->index(['student_id', 'status'], 'idx_borrow_transactions_student_status');
            $table->index(['book_id', 'status'], 'idx_borrow_transactions_book_status');
            $table->index(['due_date', 'status'], 'idx_borrow_transactions_due_status');
        });

        // Add index on books for searching
        Schema::table('books', function (Blueprint $table) {
            $table->index('title', 'idx_books_title');
            $table->index(['available_quantity', 'total_quantity'], 'idx_books_quantity');
        });

        // Add index on students for searching
        Schema::table('students', function (Blueprint $table) {
            $table->index('email', 'idx_students_email');
            $table->index('pin', 'idx_students_pin');
        });

        // Add index on book_author for many-to-many queries
        Schema::table('book_author', function (Blueprint $table) {
            $table->index(['book_id', 'author_id'], 'idx_book_author_book_author');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrow_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_borrow_transactions_status');
            $table->dropIndex('idx_borrow_transactions_student_status');
            $table->dropIndex('idx_borrow_transactions_book_status');
            $table->dropIndex('idx_borrow_transactions_due_status');
        });

        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex('idx_books_title');
            $table->dropIndex('idx_books_quantity');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('idx_students_email');
            $table->dropIndex('idx_students_pin');
        });

        Schema::table('book_author', function (Blueprint $table) {
            $table->dropIndex('idx_book_author_book_author');
        });
    }
};
