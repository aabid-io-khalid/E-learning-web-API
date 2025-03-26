<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\User;
use App\Models\Video;
use App\Models\Payment;
use App\Models\Category;
use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name', 'description', 'duration', 'level', 'status', 'category_id', 'sub_category_id','user_id','price'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'payments')
                   ->using(Payment::class)
                   ->withPivot(['status', 'amount']);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function students()
{
    return $this->belongsToMany(User::class, 'enrollments');
}
}
