<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TopicUser extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'topic_users';

    protected $fillable = [
        'topic_id',
        'topic_name',
        'user_id',
        'data',
        'history_chat',
    ];
}
