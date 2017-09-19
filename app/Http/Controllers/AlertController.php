<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Alert;
use App\TwitterRetweetAlert;
use App\TwitterFollowerAlert;
use App\TwitchFollowerAlert;
use Auth;
use App\Events\AlertEvent;

class AlertController extends Controller
{
    public function index() {
        $this->processAlerts();
        $this->broadcastAlerts();
    }

    public function getRecentAlerts() {
        $this->processAlerts();
        Alert::where('user_id', Auth::user()->id)->take(100)->get();
    }

    protected function processAlerts() {
        $twitterFollowers = TwitterFollowerAlert::where('user_id', Auth::user()->id)->where('alerted', 0)->get();
        foreach($twitterFollowers as $follower) {
            $data = unserialize($follower->data);
            $userID = Auth::user()->id;
            $name = '@' . $data->screen_name;
            $type = 'twitter_follow';
            Alert::create([
                'user_id' => $userID,
                'type' => $type,
                'name' => $name,
            ]);

        }
        TwitterFollowerAlert::where('user_id', Auth::user()->id)->where('alerted', 0)->update(['alerted' => 1]);

        $twitterRetweets = TwitterRetweetAlert::where('user_id', Auth::user()->id)->where('alerted', 0)->get();
        foreach($twitterRetweets as $retweet) {
            $data = unserialize($retweet->data);
            $userID = Auth::user()->id;
            $name = '@' . $data->user->screen_name;
            $type = 'twitter_retweet';
            Alert::create([
                'user_id' => $userID,
                'type' => $type,
                'name' => $name,
            ]);
        }
        TwitterRetweetAlert::where('user_id', Auth::user()->id)->where('alerted', 0)->update(['alerted' => 1]);

        $twitchFollowers = TwitchFollowerAlert::where('user_id', Auth::user()->id)->where('alerted', 0)->get();
        foreach($twitchFollowers as $follower) {
            $data = unserialize($follower->data);
            $userID = Auth::user()->id;
            $name = $data['user']['name'];
            $type = 'twitch_follow';
            Alert::create([
                'user_id' => $userID,
                'type' => $type,
                'name' => $name,
            ]);

        }
        TwitchFollowerAlert::where('user_id', Auth::user()->id)->where('alerted', 0)->update(['alerted' => 1]);

        $youtubeSubscribers = YoutubeSubscriberAlert::where('user_id', Auth::user()->id)->where('alerted', 0)->get();
        foreach($youtubeSubscribers as $follower) {

            $userID = Auth::user()->id;
            $name = $follower->data;
            $type = 'youtube_subscriber';
            Alert::create([
                'user_id' => $userID,
                'type' => $type,
                'name' => $name,
            ]);

        }
        YoutubeSubscriberAlert::where('user_id', Auth::user()->id)->where('alerted', 0)->update(['alerted' => 1]);
    }

    public function test() {
        \DB::table('alerts')->update(['broadcasted' => 0]);
    }

    protected function broadcastAlerts() {
        $alerts = Alert::where('user_id', Auth::user()->id)->where('broadcasted', 0)->take(50)->get();
        $count = $alerts->count();
        broadcast(new AlertEvent(Auth::user()->id, $alerts));
        Alert::where('user_id', Auth::user()->id)->where('broadcasted', 0)->take(50)->update(['broadcasted' => 1]);
    }

    public function display() {
        return view('alerts');
    }

    public function fix() {
        Alert::where('user_id', Auth::user()->id)->where('alerted', 0)->where('broadcasted', 1)->update(['broadcasted' => 0]);
    }

    public static function alerted(Alert $id) {
        if($id->user_id == Auth::user()->id) {
            $id->alerted = 1;
            $id->save();
        }
    }
}
