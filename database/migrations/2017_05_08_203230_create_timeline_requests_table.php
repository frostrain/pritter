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
        // 凡是保存到 timeline_requests 中的, 说明请求一定成功了
        Schema::create('timeline_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('disk')->nullable();
            $table->string('path')->nullable();
            $table->bigInteger('since_id')->unsigned()->nullable();
            $table->bigInteger('max_id')->unsigned()->nullable();
            $table->bigInteger('start_id')->unsigned()->nullable();
            $table->bigInteger('end_id')->unsigned()->nullable();
            // 请求的count数目, 一般不会超过 200 ...
            $table->smallInteger('count')->unsigned()->nullable();
            // 实际返回的 count
            $table->smallInteger('return_count')->unsigned()->default(0);
            // 当前请求是否覆盖了整个范围, 也就是 since_id 和 max_id 覆盖的范围
            $table->boolean('is_covered')->default(0);
            // 保存的文件大小
            $table->integer('file_size')->unsigned()->nullable();
            // 是否导入了
            $table->boolean('is_imported')->default(0);
            // 导入用时
            // $table->decimal('import_used_time', 5, 2)->nullable();
            $table->timestamps();

            // 用于检查存在
            $table->index(['path', 'disk']);
            // 用于获取 max_id
            $table->index('start_id');
            // 用于获取 since_id
            $table->index('end_id');
            // 用于检查遗漏
            $table->index('is_covered');
            // 用于导入
            $table->index(['is_imported', 'count', 'end_id']);
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
