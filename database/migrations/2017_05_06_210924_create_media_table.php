<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->increments('id');
            $table->string('origin_url');

            // owner_id 和 type 组成了关联不同模型的关键
            $table->bigInteger('owner_id')->unsigned();
            $table->tinyInteger('type')->unsigned();

            $table->boolean('is_handled')->default(0);
            $table->boolean('is_failed')->default(0);
            // 尺寸
            $table->integer('size')->unsigned()->default(0);
            $table->string('disk')->nullable();
            $table->string('path')->nullable();
            $table->timestamps();

            $table->index('is_handled');
            $table->index(['owner_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media');
    }
}
