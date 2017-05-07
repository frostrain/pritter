<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTweetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tweets', function (Blueprint $table) {
            // 我们不使用 自增id 作为主键, 直接使用 tweet 的 id 作为主键, 推特的id是64位无符号整形
            // $table->increments('id');
            $table->bigInteger('id')->unsigned();
            // 回复
            $table->bigInteger('in_reply_to_status_id')->unsigned()->nullable();
            $table->string('in_reply_to_screen_name')->nullable();
            // 引用
            $table->bigInteger('quoted_id')->unsigned()->nullable();
            // 转推
            $table->bigInteger('retweeted_id')->unsigned()->nullable();


            $table->string('text')->nullable();
            $table->text('entities')->nullable();

            $table->bigInteger('twitter_user_id')->unsigned();

            $table->integer('retweet_count')->unsigned()->default(0);
            $table->integer('favorite_count')->unsigned()->default(0);
            $table->string('lang')->nullable();

            $table->tinyInteger('state')->unsigned()->default(0);
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
        Schema::dropIfExists('tweets');
    }
}
