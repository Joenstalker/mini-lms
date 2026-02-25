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
        Schema::table('books', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->mediumText('cover_image')->nullable()->after('published_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->dropColumn('cover_image');
        });
    }
};
