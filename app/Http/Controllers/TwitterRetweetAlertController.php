<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class TwitterRetweetAlertController extends Controller
{
    public function index() {
        $retweetTime = \App\TwitterRetweetAlert::orderBy('created_at', 'desc')->first()->created_at;
        if(\Carbon\Carbon::now()->gt($retweetTime->addMinutes(1.5))) {
            $retweets = \Twitter::getRtsTimeline();
            $count = 0;
            foreach($retweets as $tweet) {
                $tweetID = $tweet->id_str;
                $rts = \Twitter::getRts($tweetID); //Gets the list of users

                foreach($rts as $rt) {
                    $streamid = Auth::user()->id;
                    $id = $rt->user->id_str;
                    $data = [
                        'user_id' => $streamid,
                        'twitter_id' => $id,
                        'data' => $rt,
                    ];
                    if($this->validator($data) == false) {
                        $this->create($data);
                    }
                }


                if($count > 4) {
                    return;
                }
                $count++;
            }
        }
    }

    protected function validator(array $data)
    {
        return \Validator::make($data, [
            'user_id' => 'required',
            'twitter_id' => 'required|unique:twitter_retweet_alerts',
            'data' => 'required',
        ])->invalid();
    }

    protected function create(array $data) {
        \App\TwitterRetweetAlert::create([
            'user_id' => $data['user_id'],
            'twitter_id' => $data['twitter_id'],
            'data' => serialize($data['data']),
        ]);
    }
}
