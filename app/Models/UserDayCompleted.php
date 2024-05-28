<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDayCompleted extends Model
{
    use HasFactory;

    protected $table = 'user_day_completed';

    protected $fillable = [
        'user_id',
        'day_number',
        'is_completed',
        'is_passed_first_quiz',
        'is_passed_quiz_story_1',
        'is_passed_quiz_story_2',
        'vocabulary_ids',
    ];
}
