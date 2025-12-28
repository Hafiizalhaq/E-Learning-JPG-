<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the role of the user
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role->name === 'admin';
    }

    /**
     * Check if user is instructor
     */
    public function isInstructor()
    {
        return $this->role->name === 'instructor';
    }

    /**
     * Check if user is student
     */
    public function isStudent()
    {
        return $this->role->name === 'student';
    }

    /**
     * Get the instructor profile
     */
    public function instructorProfile()
    {
        return $this->hasOne(InstructorProfile::class, 'user_id');
    }

    /**
     * Get the student profile
     */
    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class, 'user_id');
    }

    /**
     * Get the courses created by this user (if instructor)
     */
    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    /**
     * Get the enrollments of this user
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'user_id');
    }

    /**
     * Get the lesson progress of this user
     */
    public function lessonProgress()
    {
        return $this->hasMany(LessonProgress::class, 'user_id');
    }
}
