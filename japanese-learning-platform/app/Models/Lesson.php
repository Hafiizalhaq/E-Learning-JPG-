<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'title',
        'content',
        'video_url',
        'video_path',
        'material_path',
        'type',
        'duration_minutes',
        'order',
        'is_published',
    ];

    protected $casts = [
        'module_id' => 'integer',
        'duration_minutes' => 'integer',
        'order' => 'integer',
        'is_published' => 'boolean',
    ];

    /**
     * Get the module this lesson belongs to
     */
    public function module()
    {
        return $this->belongsTo(CourseModule::class, 'module_id');
    }

    /**
     * Get the quiz for this lesson
     */
    public function quiz()
    {
        return $this->hasOne(Quiz::class, 'lesson_id');
    }

    /**
     * Get the progress records for this lesson
     */
    public function progress()
    {
        return $this->hasMany(LessonProgress::class, 'lesson_id');
    }
}