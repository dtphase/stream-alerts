<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TwitchApi;

class TwitchFollowerAlert extends Model
{
    protected $fillable = [
        'user_id', 'twitch_id', 'data',
    ];

}
