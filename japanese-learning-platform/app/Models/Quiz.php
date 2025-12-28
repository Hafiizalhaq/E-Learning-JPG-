<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'title',
        'description',
        'time_limit',
        'passing_score',
        'is_published',
    ];

    protected $casts = [
        'lesson_id' => 'integer',
        'time_limit' => 'integer',
        'passing_score' => 'integer',
        'is_published' => 'boolean',
    ];

    /**
     * Get the lesson this quiz belongs to
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    /**
     * Get the questions for this quiz
     */
    public function questions()
    {
        return $this->hasMany(QuizQuestion::class, 'quiz_id');
    }
}