<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsRecommendedToCourseRegistrations extends Migration
{
    public function up()
    {
        Schema::table('course_registrations', function (Blueprint $table) {
            $table->boolean('is_recommended')->default(false);
        });
    }

    public function down()
    {
        Schema::table('course_registrations', function (Blueprint $table) {
            $table->dropColumn('is_recommended');
        });
    }
} 