<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwitchFollowerAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twitch_follower_alerts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id'); //ID of the client using the site
            $table->string('twitch_id'); //ID of the client using the site
            $table->text('data');
            $table->boolean('alerted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('twitch_follower_alerts');
    }
}
