<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwitterFollowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twitter_followers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id'); //ID of the client using the s]ite
            $table->string('twitter_id'); //ID from the API
            $table->text('data');
            $table->boolean('alerted')->default(0);
            $table->timestamps();
        });

        DB::table('twitter_followers')->insert(['user_id' => -1, 'twitter_id' => -1, 'data' => 'Inital entry', 'created_at' => \Carbon\Carbon::now(), 'updated_at' => \Carbon\Carbon::now()]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('twitter_followers');
    }
}
