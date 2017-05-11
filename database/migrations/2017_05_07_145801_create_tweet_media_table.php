<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTweetMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tweet_media', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned();
            $table->string('id_str');

            $table->bigInteger('tweet_id')->unsigned();

            $table->string('media_url');
            $table->tinyInteger('type')->unsigned();

            $table->boolean('is_handled')->unsigned()->default(0);
            // 文件尺寸
            $table->integer('size')->unsigned()->default(0);
            $table->string('disk')->nullable();
            $table->string('path')->nullable();
            $table->timestamps();

            $table->primary('id');
            $table->index('is_handled');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tweet_media');
    }
}
