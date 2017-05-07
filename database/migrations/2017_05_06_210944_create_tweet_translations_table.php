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
            $table->bigInteger('id')->unsigned();

            $table->string('text');
            $table->string('lang');
            $table->string('source');
            $table->smallInteger('state')->unsigned()->default(0);

            $table->timestamps();

            $table->primary('id');
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
