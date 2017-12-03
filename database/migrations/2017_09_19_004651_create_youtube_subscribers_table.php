<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYoutubeSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('youtube_subscribers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id');
            $table->string('youtube_id');
            $table->string('data');
            $table->boolean('alerted')->default(0);
            $table->timestamps();
        });

        DB::table('youtube_subscribers')->insert(['user_id' => -1, 'youtube_id' => -1, 'data' => 'Inital entry', 'created_at' => \Carbon\Carbon::now(), 'updated_at' => \Carbon\Carbon::now()]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('youtube_subscribers');
    }
}
