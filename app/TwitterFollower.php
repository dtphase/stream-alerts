<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwitterFollower extends Model
{
    protected $fillable = [
        'user_id', 'twitter_id', 'data',
    ];


}
