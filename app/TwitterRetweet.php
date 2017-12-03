<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwitterRetweet extends Model
{
    protected $fillable = [
        'user_id', 'twitter_id', 'data',
    ];
}
