<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class TwitterRetweetController extends Controller
{
    public function index() {
        $retweetTime = \App\TwitterRetweet::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->first();
        if($retweetTime === null || \Carbon\Carbon::now()->gt($retweetTime->created_at->addMinutes(1.5))) {
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
            'twitter_id' => 'required|unique:twitter_retweets',
            'data' => 'required',
        ])->invalid();
    }

    protected function create(array $data) {
        \App\TwitterRetweet::create([
            'user_id' => $data['user_id'],
            'twitter_id' => $data['twitter_id'],
            'data' => serialize($data['data']),
        ]);
    }
}
