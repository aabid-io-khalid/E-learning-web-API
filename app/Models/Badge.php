<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image_url',
        'type', // 'student' or 'mentor'
        'condition_type', // 'courses_completed', 'students_count', etc.
        'condition_value',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps();
    }
}
