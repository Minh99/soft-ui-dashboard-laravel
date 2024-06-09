<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWithChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'word',
        'history',
    ];
}
