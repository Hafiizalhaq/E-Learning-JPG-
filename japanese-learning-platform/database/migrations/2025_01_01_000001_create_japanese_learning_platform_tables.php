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
        // Create roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // admin, instructor, student
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Create users table (extending default users table)
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->default(3); // Default to student
            $table->foreign('role_id')->references('id')->on('roles');
        });

        // Create instructor_profiles table
        Schema::create('instructor_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->text('bio')->nullable();
            $table->string('specialization')->nullable(); // e.g., JLPT N5-N1, Kanji expert, etc.
            $table->string('certification')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Create student_profiles table
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('jlpt_level', ['none', 'N5', 'N4', 'N3', 'N2', 'N1'])->default('none');
            $table->text('learning_goals')->nullable();
            $table->date('start_date')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Create course_categories table
        Schema::create('course_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create courses table
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instructor_id');
            $table->unsignedBigInteger('category_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('short_description');
            $table->enum('jlpt_level', ['N5', 'N4', 'N3', 'N2', 'N1']);
            $table->integer('duration_hours')->nullable(); // Estimated duration in hours
            $table->decimal('price', 10, 2)->default(0.00);
            $table->boolean('is_published')->default(false);
            $table->boolean('is_free')->default(false);
            $table->string('thumbnail_image')->nullable();
            $table->timestamps();
            
            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('course_categories')->onDelete('cascade');
        });

        // Create course_modules table
        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(1);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });

        // Create lessons table
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id');
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('video_url')->nullable(); // URL to video file
            $table->string('video_path')->nullable(); // Path to video file
            $table->string('material_path')->nullable(); // Path to PDF/materials
            $table->enum('type', ['video', 'reading', 'practice', 'quiz'])->default('video');
            $table->integer('duration_minutes')->nullable(); // Duration in minutes
            $table->integer('order')->default(1);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            
            $table->foreign('module_id')->references('id')->on('course_modules')->onDelete('cascade');
        });

        // Create quizzes table
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('time_limit')->nullable(); // Time limit in minutes
            $table->integer('passing_score')->default(70); // Percentage needed to pass
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
        });

        // Create quiz_questions table
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quiz_id');
            $table->text('question');
            $table->enum('type', ['multiple_choice', 'true_false', 'fill_blank'])->default('multiple_choice');
            $table->json('options')->nullable(); // Store options as JSON for multiple choice
            $table->text('correct_answer');
            $table->text('explanation')->nullable(); // Explanation for the correct answer
            $table->integer('points')->default(1);
            $table->integer('order')->default(1);
            $table->timestamps();
            
            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
        });

        // Create quiz_answers table
        Schema::create('quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attempt_id');
            $table->unsignedBigInteger('question_id');
            $table->text('answer');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });

        // Create enrollments table
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('course_id');
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->float('progress_percentage', 5, 2)->default(0.00);
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->unique(['user_id', 'course_id']); // Prevent duplicate enrollments
        });

        // Create lesson_progress table
        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('lesson_id');
            $table->unsignedBigInteger('enrollment_id');
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            $table->foreign('enrollment_id')->references('id')->on('enrollments')->onDelete('cascade');
            $table->unique(['user_id', 'lesson_id']); // One progress record per user-lesson pair
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_progress');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('quiz_answers');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('course_modules');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('course_categories');
        Schema::dropIfExists('student_profiles');
        Schema::dropIfExists('instructor_profiles');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
        
        Schema::dropIfExists('roles');
    }
};