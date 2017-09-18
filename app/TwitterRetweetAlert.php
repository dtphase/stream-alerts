<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwitterRetweetAlert extends Model
{
    protected $fillable = [
        'user_id', 'twitter_id', 'data',
    ];
}
