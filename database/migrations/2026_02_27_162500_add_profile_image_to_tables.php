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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'profile_image')) {
                $table->longText('profile_image')->nullable()->after('email');
            }
        });

        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'profile_image')) {
                $table->longText('profile_image')->nullable()->after('address');
            }
        });

        Schema::table('authors', function (Blueprint $table) {
            if (!Schema::hasColumn('authors', 'profile_image')) {
                $table->longText('profile_image')->nullable()->after('bio');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'profile_image')) {
                $table->dropColumn('profile_image');
            }
        });

        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'profile_image')) {
                $table->dropColumn('profile_image');
            }
        });

        Schema::table('authors', function (Blueprint $table) {
            if (Schema::hasColumn('authors', 'profile_image')) {
                $table->dropColumn('profile_image');
            }
        });
    }
};
