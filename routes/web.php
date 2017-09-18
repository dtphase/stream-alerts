<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/',function () {
    return view('home');
})->middleware('auth');

Route::get('/end', function () {
    return view('home');
});

Route::get('/uploadfile','AnimationDataController@index');

Route::post('/uploadfile','AnimationDataController@showUploadFile');

Route::get('/test', 'AlertController@test')->middleware('auth');

Route::get('/twitter/followers', 'TwitterFollowerAlertController@index')->middleware('auth');

//TODO: delete RTs table every 12 hours after first entry
Route::get('/twitter/retweets', 'TwitterRetweetAlertController@index')->middleware('auth');

Route::get('/twitch/followers', 'TwitchFollowerAlertController@index')->middleware('auth');

Route::group(['middleware' => ['web']], function () {
    Route::get('/alerts/get', 'AlertController@index')->middleware('auth');
});

Route::get('/alerts', 'AlertController@display')->middleware('auth');

Route::get('/alerts/fix', 'AlertController@fix')->middleware('auth');

Route::get('/alerts/alerted/{id}', function (\App\Alert $id) {
    \App\Http\Controllers\AlertController::alerted($id);
})->middleware('auth');


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::get('twitter/login', ['as' => 'twitter.login', function(){
	// your SIGN IN WITH TWITTER  button should point to this route
	$sign_in_twitter = true;
	$force_login = false;

	// Make sure we make this request w/o tokens, overwrite the default values in case of login.
	Twitter::reconfig(['token' => '', 'secret' => '']);
	$token = Twitter::getRequestToken(route('twitter.callback'));

	if (isset($token['oauth_token_secret']))
	{
		$url = Twitter::getAuthorizeURL($token, $sign_in_twitter, $force_login);

		Session::put('oauth_state', 'start');
		Session::put('oauth_request_token', $token['oauth_token']);
		Session::put('oauth_request_token_secret', $token['oauth_token_secret']);

		return Redirect::to($url);
	}

	return Redirect::route('twitter.error');
}]);

Route::get('twitter/callback', ['as' => 'twitter.callback', function() {
	// You should set this route on your Twitter Application settings as the callback
	// https://apps.twitter.com/app/YOUR-APP-ID/settings
	if (Session::has('oauth_request_token'))
	{
		$request_token = [
			'token'  => Session::get('oauth_request_token'),
			'secret' => Session::get('oauth_request_token_secret'),
		];

		Twitter::reconfig($request_token);

		$oauth_verifier = false;

		if (Input::has('oauth_verifier'))
		{
			$oauth_verifier = Input::get('oauth_verifier');
			// getAccessToken() will reset the token for you
			$token = Twitter::getAccessToken($oauth_verifier);
		}

		if (!isset($token['oauth_token_secret']))
		{
			return Redirect::route('twitter.error')->with('flash_error', 'We could not log you in on Twitter.');
		}

		$credentials = Twitter::getCredentials();

		if (is_object($credentials) && !isset($credentials->error))
		{

            //dump($credentials);
            //dump($token);

            $user = Auth::user();
            $user->twitter_token = $token;
            $user->save();


			Session::put('access_token', $token);

			return Redirect::to('/')->with('flash_notice', 'Congrats! You\'ve successfully signed in with Twitter!');
		}

		//return Redirect::route('twitter.error')->with('flash_error', 'Crab! Something went wrong while signing you up!');
	}
}]);

Route::get('twitter/error', ['as' => 'twitter.error', function(){
	// Something went wrong, add your own error handling here
}]);

Route::get('twitter/logout', ['as' => 'twitter.logout', function(){
	Session::forget('access_token');
	return Redirect::to('/')->with('flash_notice', 'You\'ve successfully logged out!');
}]);

Route::get('/twitch/login', function(){
    return redirect(TwitchApi::getAuthenticationUrl());
});

Route::get('/twitch/callback/', function(Illuminate\Http\Request $request){
    // Request Twitch token from Twitch
    $code = $request->input('code');
    $response = TwitchApi::getAccessObject($code);


    TwitchApi::setToken($response['access_token']);

    // Get user object from Twitch
    $twitchUser = TwitchApi::authUser();

    $user = Auth::user();
    $user->twitch_id = $twitchUser['_id'];
    $user->save();

    /**
     * Find or create user (this expects a twitch_id column in your users table).
     *
     * It's recommended to identify Twitch users by twitch_id, rather than by name.
     * Names may be changed by Twitch staff, however, the twitch_id remains the same.
     */
    //$user = User::findOrNew(['twitch_id' => $twitchUser['_id']]);

    // Authenticate user
    //Auth::login($user);
});
