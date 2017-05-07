<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTweetTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tweet_translations', function (Blueprint $table) {
            $table->increments('id')->unsigned();

            $table->bigInteger('tweet_id')->unsigned();

            $table->string('text');
            $table->string('lang')->nullable();
            $table->string('source');
            $table->smallInteger('order')->default(0);
            $table->tinyInteger('state')->unsigned()->default(0);

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
        Schema::dropIfExists('tweet_translations');
    }
}
