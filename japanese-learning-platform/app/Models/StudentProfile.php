<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jlpt_level',
        'learning_goals',
        'start_date',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'start_date' => 'date',
    ];

    /**
     * Get the user that owns the student profile
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the enrollments of this student
     */
    public function enrollments()
    {
        return $this->hasManyThrough(Enrollment::class, User::class, 'id', 'user_id', 'user_id', 'id');
    }
}