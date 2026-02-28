<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'pin')) {
                $table->dropColumn('pin');
            }
            if (!Schema::hasColumn('students', 'student_id')) {
                // Add it as nullable first to avoid unique constraint error on existing empty rows
                $table->string('student_id', 19)->nullable()->after('id');
            }
        });

        // Populate existing students with temporary unique IDs if any
        $students = DB::table('students')->whereNull('student_id')->orWhere('student_id', '')->get();
        foreach ($students as $student) {
            $tempId = date('y') . str_pad($student->id, 8, '0', STR_PAD_LEFT);
            DB::table('students')->where('id', $student->id)->update(['student_id' => $tempId]);
        }

        Schema::table('students', function (Blueprint $table) {
            $table->string('student_id', 10)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'student_id')) {
                $table->dropColumn('student_id');
            }
            if (!Schema::hasColumn('students', 'pin')) {
                $table->string('pin')->nullable()->after('address');
            }
        });
    }
};
