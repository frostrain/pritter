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
            // 我们不使用 自增id 作为主键, 直接使用 tweet 的 id 作为主键, 推特的id是64 bit无符号整形
            // $table->increments('id');
            $table->bigInteger('id')->unsigned();
            // 由于 php 中的 bigInt 有可能存在精度问题, 这里用字符串保留原始id的精确值
            $table->string('id_str');
            // 回复
            $table->bigInteger('in_reply_to_status_id')->unsigned()->nullable();
            $table->string('in_reply_to_screen_name')->nullable();
            // 引用
            $table->bigInteger('quoted_id')->unsigned()->nullable();
            // 转推
            $table->bigInteger('retweeted_id')->unsigned()->nullable();

            $table->boolean('truncated')->default(0);
            $table->string('text')->nullable();
            $table->text('entities')->nullable();

            $table->bigInteger('twitter_user_id')->unsigned();
            $table->boolean('is_following_author')->default(0);

            $table->integer('retweet_count')->unsigned()->default(0);
            $table->integer('favorite_count')->unsigned()->default(0);
            $table->string('lang')->nullable();

            $table->tinyInteger('state')->unsigned()->default(0);
            $table->timestamps();

            $table->primary('id');
            // 通过用户查找 推文
            $table->index('twitter_user_id');
            // 用于显示 已关注的人 的推
            $table->index('is_following_author');
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
