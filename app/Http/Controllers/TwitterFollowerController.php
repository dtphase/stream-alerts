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

    public function names() {
        $names = file_get_contents('/mnt/g/Scripts/names.txt');
        $n_array = explode(',', $names);
        $takenNames = array();
        $chunked = array_chunk($n_array, 99);

        foreach($chunked as $chunk) {
            $names = implode(',', $chunk);
            $params = ['screen_name' => $names];
            $followersJSON = \Twitter::getUsersLookup($params);
            foreach($followersJSON as $follower) {
                array_push($takenNames,$follower->screen_name);
            }
        }

        $n_array = array_map('strtolower', $n_array);
        $takenNames = array_map('strtolower', $takenNames);
        echo "<p>" . implode(', ', $n_array) . "</p>";
        echo "<p>" . implode(', ', $takenNames) . "</p>";
        $diffNames = array_diff($n_array, $takenNames);
        echo "<p>" . implode(', ', $diffNames) . "</p>";
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
