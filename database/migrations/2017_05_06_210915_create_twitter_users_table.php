<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwitterUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twitter_users', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned();
            $table->string('name');
            $table->string('screen_name')->unique();
            $table->string('location')->nullable();
            $table->string('description')->nullable();
            $table->string('url')->nullable();

            $table->integer('statuses_count')->unsigned()->default(0);
            $table->integer('favourites_count')->unsigned()->default(0);
            $table->integer('followers_count')->unsigned()->default(0);
            $table->integer('friends_count')->unsigned()->default(0);

            // 这些图片对应 Media 模型, 而不是 TweetMedia 模型
            $table->integer('profile_image_id')->unsigned()->nullable();
            $table->integer('profile_banner_id')->unsigned()->nullable();
            $table->integer('profile_background_image_id')->unsigned()->nullable();

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
        Schema::dropIfExists('twitter_users');
    }
}
