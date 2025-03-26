<?php

namespace App\Models;

use App\Models\User;
use App\Models\Course;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'stripe_session_id',
        'status',
        'amount',
        'currency',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec le cours
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Méthodes utilitaires
    public function isPaid()
    {
        return $this->status === 'completed';
    }

    public function markAsCompleted()
    {
        $this->update(['status' => 'completed']);
    }
}
