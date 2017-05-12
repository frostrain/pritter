<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimelineStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timeline_statistics', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('request_type')->unsigned();
            $table->bigInteger('twitter_user_id')->unsigned()->nullable();
            // 目前获取的最小id
            $table->bigInteger('min_id')->unsigned()->nullable();
            // 最小id是否已经到头了(无法获取更多旧数据了)
            $table->boolean('is_min_end')->default(0);
            // 目前获取的最大id
            $table->bigInteger('max_id')->unsigned()->nullable();
            $table->integer('count')->unsigned()->nullable();
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
        Schema::dropIfExists('timeline_statistics');
    }
}
