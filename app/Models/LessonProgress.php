<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LessonProgress extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'lesson_id', 'is_completed', 'completed_at'];
    protected $dates = ['completed_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
