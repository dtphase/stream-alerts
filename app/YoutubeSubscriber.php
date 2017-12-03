<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class YoutubeSubscriber extends Model
{
    protected $fillable = [
        'user_id', 'youtube_id', 'data',
    ];
}
