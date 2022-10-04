<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_favourites extends Model
{
    use HasFactory;

    protected $table = 'user_favourites';

    protected $fillable = [
        'user_id',
        'favourite_id'
    ];
}
