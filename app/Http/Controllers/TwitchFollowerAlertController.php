<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use TwitchApi;
use Auth;

class TwitchFollowerAlertController extends Controller
{
    public function index()
    {

        $options = [
            'limit' => 100,
        ];
        $followers = TwitchApi::followers(Auth::user()->twitch_id, $options);
        //dd($followers['follows']);
        foreach($followers['follows'] as $follower) {
            $data = [
                'user_id' => Auth::user()->id,
                'twitch_id' => $follower['user']['_id'],
                'data' => $follower,
            ];
            if($this->validator($data) == false) {
                $this->create($data);
            }
        }

    }

    protected function validator(array $data)
    {
        return \Validator::make($data, [
            'user_id' => 'required',
            'twitch_id' => 'required|unique:twitch_follower_alerts',
            'data' => 'required',
        ])->invalid();
    }

    protected function create(array $data) {
        \App\TwitchFollowerAlert::create([
            'user_id' => $data['user_id'],
            'twitch_id' => $data['twitch_id'],
            'data' => serialize($data['data']),
        ]);
    }
}
