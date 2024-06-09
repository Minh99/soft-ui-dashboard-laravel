<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDayCompleted extends Model
{
    use HasFactory;

    protected $table = 'user_day_completeds';

    protected $fillable = [
        'user_id',
        'day_number',
        'is_completed',
        'is_passed_first_quiz',
        'is_passed_quiz_story_1',
        'is_passed_quiz_story_2',
        'is_passed_quiz_story_3',
        'is_passed_quiz_story_4',
        'is_passed_test_2',
        'vocabulary_ids',
        'words_to_gen_story_1',
        'words_to_gen_story_2',
        'words_to_gen_story_3',
        'words_to_gen_story_4',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
