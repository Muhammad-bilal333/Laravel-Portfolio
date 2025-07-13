<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsLabToCoursesTable extends Migration
{
    public function up(): void {
        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('is_lab')->default(false)->after('section');
        });
    }

    public function down(): void {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('is_lab');
        });
    }
} 