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
            $table->string('id_str');
            $table->string('name');
            $table->string('screen_name')->unique();
            $table->string('location')->nullable();
            $table->string('description')->nullable();
            $table->string('url')->nullable();

            // (api key 的用户)是否正在关注该用户
            $table->boolean('following')->default(0);
            $table->integer('statuses_count')->unsigned()->default(0);
            $table->integer('favourites_count')->unsigned()->default(0);
            $table->integer('followers_count')->unsigned()->default(0);
            $table->integer('friends_count')->unsigned()->default(0);

            $table->boolean('profile_background_tile')->default(0);

            $table->timestamps();

            $table->primary('id');
            $table->index('following');
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
