<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class TwitterFollowerController extends Controller
{
    public function index() {

        $followTime = \App\TwitterFollower::orderBy('created_at', 'desc')->first();
        if($followTime === null || \Carbon\Carbon::now()->gt($followTime->created_at->addMinutes(1.5))) {
            //Adds a request to the database to record when the API was last accessed
            $data = [
                'user_id' => -1,
                'twitter_id' => -1,
                'data' => 'API request logged',
            ];
            $this->create($data);

            //Accesses the API and creates new entry in the database
            $params = ['count' => 200];
            $followersJSON = \Twitter::getFollowers($params);
            $followers = $followersJSON->users;

            foreach($followers as $follower) {
                $streamid = Auth::user()->id;
                $id = $follower->id_str;
                $data = [
                    'user_id' => $streamid,
                    'twitter_id' => $id,
                    'data' => $follower
                ];
                if($this->validator($data) == false) {
                    $this->create($data);
                }
            }
        } else {
            dd('API on cooldown');
        }
    }

    protected function validator(array $data)
    {
        return \Validator::make($data, [
            'user_id' => 'required',
            'twitter_id' => 'required|unique:twitter_followers',
            'data' => 'required',
        ])->invalid();
    }

    protected function create(array $data) {
        \App\TwitterFollower::create([
            'user_id' => $data['user_id'],
            'twitter_id' => $data['twitter_id'],
            'data' => serialize($data['data']),
        ]);
    }
}