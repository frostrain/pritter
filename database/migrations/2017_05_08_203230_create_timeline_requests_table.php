<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimelineRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timeline_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('disk')->nullable();
            $table->string('path')->nullable();
            $table->bigInteger('since_id')->unsigned()->nullable();
            $table->bigInteger('max_id')->unsigned()->nullable();
            $table->bigInteger('start_id')->unsigned()->nullable();
            $table->bigInteger('end_id')->unsigned()->nullable();
            // count 一般不会超过 200 ...
            $table->smallInteger('count')->unsigned()->default(0);
            $table->boolean('is_success')->default(0);
            // 当前请求是否覆盖了整个范围, 也就是 since_id 和 max_id 覆盖的范围
            $table->boolean('is_covered')->default(0);
            // 保存的文件大小
            $table->integer('file_size')->unsigned()->nullable();
            // 是否导入了
            $table->boolean('is_imported')->default(0);
            $table->string('error')->nullable();

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
        Schema::dropIfExists('timeline_requests');
    }
}
