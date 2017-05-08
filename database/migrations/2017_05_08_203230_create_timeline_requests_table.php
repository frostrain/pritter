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
            $table->string('disk');
            $table->string('path');
            $table->bigInteger('since_id')->nullable();
            $table->bigInteger('max_id')->nullable();
            $table->bigInteger('start_id')->nullable();
            $table->bigInteger('end_id')->nullable();
            $table->integer('count')->default(0);
            $table->boolean('is_success')->default(0);
            // 当前请求是否覆盖了整个范围, 也就是 since_id 和 max_id 覆盖的范围
            $table->boolean('is_covered')->default(0);
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
