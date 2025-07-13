<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('crrs', function (Blueprint $table) {
            $table->id();
            $table->string('course_code');
            $table->string('course_name');
            $table->string('pre_requisite')->nullable();
            $table->string('batch')->nullable();
            $table->string('section')->nullable();
            $table->integer('semester')->nullable();
            $table->boolean('is_lab')->default(false); // true for lab, false for theory
            $table->foreignId('lecturer_id')->constrained('users');
            $table->enum('type', ['theory', 'lab']);
            $table->integer('total_students');
            $table->json('grades_distribution')->nullable();
            $table->json('clos_achievement')->nullable();
            $table->json('instructor_comments')->nullable();
            $table->json('departmental_review')->nullable();
            $table->string('instructor_signature')->nullable();
            $table->string('instructor_name')->nullable();
            $table->text('suggestion_recommendation')->nullable();
            $table->string('sign')->nullable();
            $table->string('name')->nullable();
            $table->date('date')->nullable();
            $table->json('failed_plos')->nullable();
            $table->string('file_path')->nullable();
            $table->text('qec_comments')->nullable();
            $table->text('qec_observation')->nullable(); // Editable by QEC only
            $table->enum('status', ['pending', 'reviewed', 'sent_to_hod', 'needs_attention'])->default('pending');
            $table->timestamp('last_updated')->nullable();
            $table->timestamps();

            $table->foreign('course_code')
                  ->references('code')
                  ->on('courses')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('crrs');
    }
}; 