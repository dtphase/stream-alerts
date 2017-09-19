<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google;

class YoutubeSubscriberAlertController extends Controller
{
    public function index() {
        $part = 'subscriberSnippet';
        $params = [
            'myRecentSubscribers' => true,
        ];

        $client = Google::getClient();
        $youtube = Google::make('YouTube');
        $client->addScope('https://www.googleapis.com/auth/youtube');
        $url = $client->createAuthUrl();

        \Redirect::to($url)->send();
        //$youtube = Google::make('YouTube');
        //dd($youtube->subscriptions->listSubscriptions($part, $params));
    }

    public function callback(\Illuminate\Http\Request $request) {
        $auth_code = $request->input('code');

        $access_token = Google::getClient()->authenticate($auth_code);
        dump($access_token);
        \Auth::user()->youtube_token = serialize($access_token);
        \Auth::user()->save();

    }
    public function getSubscribers() {
        $part = 'subscriberSnippet';
        $params = [
            'mySubscribers' => true,
            'maxResults' => 50,
        ];


        $youtube = Google::make('YouTube');
        $client = Google::getClient();
        $client->setAccessToken(unserialize(\Auth::user()->youtube_token));
        $subs = $youtube->subscriptions->listSubscriptions($part, $params);

        foreach($subs as $sub) {
                //echo $sub->subscriberSnippet->title;
                //echo $sub->subscriberSnippet->channelId;
            $data = [
                'user_id' => \Auth::user()->id,
                'youtube_id' => $sub->subscriberSnippet->channelId,
                'data' => $sub->subscriberSnippet->title,
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
            'youtube_id' => 'required|unique:youtube_subscriber_alerts',
            'data' => 'required',
        ])->invalid();
    }

    protected function create(array $data) {
        \App\YoutubeSubscriberAlert::create([
            'user_id' => $data['user_id'],
            'youtube_id' => $data['youtube_id'],
            'data' => $data['data'],
        ]);
    }

}
