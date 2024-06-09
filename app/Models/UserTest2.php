<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTest2 extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'user_test2s';

    protected $fillable = [
        'user_id',
        'type',
        'history',
    ];
}
