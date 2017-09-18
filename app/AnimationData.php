<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnimationData extends Model
{
    protected $fillable = [
        'user_id', 'json', 
    ];
}
