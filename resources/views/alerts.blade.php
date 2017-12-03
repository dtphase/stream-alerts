<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>StreamUI</title>
    </head>
    <body>
        <div id="bodymovin"></div>
        <script src="{{ asset('js/app.js') }}"></script>
        <script src="{{ asset('js/bodymovin.js') }}"></script>
        @auth
            <script>

            var twitterFollowJSON;
            var twitterRetweetJSON;
            var twitchFollowJSON;
            var youtubeSubscriberJSON;

            var queue = [];


            Echo.private('alerts.{{Auth::user()->id}}')
                .listen('.AlertEvent', (e) => {
                    console.log(e);
                    $.each(e.alert, function(index, value) {
                        id = e.alert[index]['id'];
                        type = e.alert[index]['type'];
                        name = e.alert[index]['name'];
                        message = e.alert[index]['message'];
                        //alert = {id: id, type: type, name: name, message: message};
                        queue.push(e.alert[index]);
                    });

            });

            function refreshJSON() {
                twitterFollowJSON = $.get({
                    url: "{{ asset('json/AnimationData/' . Auth::user()->id . '/TwitterFollow/data.json') }}",
                    type: "GET",
                });

                twitterRetweetJSON = $.get({
                    url: "{{ asset('json/AnimationData/' . Auth::user()->id . '/TwitterRetweet/data.json') }}",
                    type: "GET",
                });

                twitchFollowJSON = $.get({
                    url: "{{ asset('json/AnimationData/' . Auth::user()->id . '/TwitchFollow/data.json') }}",
                    type: "GET",
                });

                youtubeSubscriberJSON = $.get({
                    url: "{{ asset('json/AnimationData/' . Auth::user()->id . '/YoutubeSubscriber/data.json') }}",
                    type: "GET",
                });
            }


            function getAlerts() {
                console.log('Sending AJAX request...');
                $.ajax({
                type: "GET",
                url: "/alerts/get",
                }).done(function(msg) {
                    console.log('success');
                }).fail(function() {
                    console.log('error');
                }).always(function() {
                    setTimeout('getAlerts()', 60000);
                });
            }

            function displayAlert() {
                console.log(queue[0]);
                $("#bodymovin").empty();
                if(queue.length > 0) {
                    first = queue.shift();
                    id = first['id'];
                    console.log(id);
                    type = first['type'];
                    name = first['name'];
                    message = first['message'];

                    //set animation here
                    switch(type) {
                        case 'twitter_follow':
                            var animationData = twitterFollowJSON.responseJSON;
                            animationData.layers[0].t.d.k[0].s.t = name.toLowerCase();
                            break;
                        case 'twitter_retweet':
                            var animationData = twitterRetweetJSON.responseJSON;
                            animationData.layers[0].t.d.k[0].s.t = name.toLowerCase();
                            break;

                        case 'twitch_follow':
                            var animationData = twitchFollowJSON.responseJSON;
                            animationData.layers[0].t.d.k[0].s.t = name.toLowerCase();
                            break;

                        case 'youtube_subscriber':
                            var animationData = youtubeSubscriberJSON.responseJSON;
                            console.log(animationData);
                            animationData.layers[0].t.d.k[0].s.t = name.toLowerCase();
                            break;

                        default:
                            break;
                    }
                    //Play animation here
                    var params = {
                        container: document.getElementById('bodymovin'),
                        renderer: 'svg',
                        loop: false,
                        autoplay: true,
                        animationData: animationData
                    };

                    var anim;

                    anim = bodymovin.loadAnimation(params);
                    refreshJSON();
                    //Remove from array and db
                    console.log('Sending AJAX request...');
                    console.log(id);
                    $.ajax({
                        type: "GET",
                        url: "/alerts/alerted/" + id,
                    }).done(function(msg) {
                        console.log('success');
                    }).fail(function() {
                        console.log('error');
                    }).always(function() {
                    });

                }
                setTimeout('displayAlert()', 9000);
            }

            function fixTable() {
                console.log('Sending AJAX request...');
                $.ajax({
                type: "GET",
                url: "/alerts/fix",
                }).done(function(msg) {
                    console.log('success');
                }).fail(function() {
                    console.log('error');
                }).always(function() {
                });
            }

            function getTwitterFollowers() {
                console.log('Sending AJAX request...');
                $.ajax({
                type: "GET",
                url: "/twitter/followers",
                }).done(function(msg) {
                    console.log('success');
                }).fail(function() {
                    console.log('error');
                }).always(function() {
                    setTimeout('getTwitterFollowers()', 300000);
                });
            }

            function getTwitterRetweets() {
                console.log('Sending AJAX request...');
                $.ajax({
                type: "GET",
                url: "/twitter/retweets",
                }).done(function(msg) {
                    console.log('success');
                }).fail(function() {
                    console.log('error');
                }).always(function() {
                    setTimeout('getTwitterRetweets', 300000);
                });
            }

            function getTwitchFollowers() {
                console.log('Sending AJAX request...');
                $.ajax({
                type: "GET",
                url: "/twitch/followers",
                }).done(function(msg) {
                    console.log('success');
                }).fail(function() {
                    console.log('error');
                }).always(function() {
                    setTimeout('getTwitchFollowers', 60000);
                });
            }

            function getYoutubeSubscribers() {
                console.log('Sending AJAX request...');
                $.ajax({
                type: "GET",
                url: "/youtube/subscribers",
                }).done(function(msg) {
                    console.log('success');
                }).fail(function() {
                    console.log('error');
                }).always(function() {
                    setTimeout('getYoutubeSubscribers', 120000);
                });
            }

            //TODO: fix timeout times + add ajax for calling twitter/followers
            //TODO streaming twitter
            $(function() {
                fixTable();
                refreshJSON();
                setTimeout('getAlerts()', 1000);
                setTimeout('displayAlert()', 2000);
                setTimeout('getTwitchFollowers()', 3000);
                setTimeout('getYoutubeSubscribers()', 6000);
                setTimeout('getTwitterFollowers()', 9000);
                setTimeout('getTwitterRetweets()', 12000);
            });






            </script>
        @endauth
    </body>
</html>
