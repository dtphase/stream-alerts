<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwitchBitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twitch_bits', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id'); //ID of the client using the site
            $table->text('data');
            $table->boolean('alerted')->default(0);
            $table->timestamps();
        });
        DB::table('twitch_bits')->insert(['user_id' => -1, 'data' => 'Inital entry', 'created_at' => \Carbon\Carbon::now(), 'updated_at' => \Carbon\Carbon::now()]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('twitch_bits');
    }
}
