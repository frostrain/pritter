<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Storage;
use App\Interfaces\TweetCollection;

class TimelineRequest extends Model implements TweetCollection
{
    protected $fillable = [
        "disk", "path", "since_id", "max_id", "start_id", "end_id", "count",
        "return_count", "is_covered", "is_imported", 'file_size',
    ];

    /**
     * @return array
     */
    public function getTweets()
    {
        $jsonStr = Storage::disk($this->disk)->get($this->path);
        return json_decode($jsonStr, true, 512, JSON_BIGINT_AS_STRING);
    }
    /**
     * @return bool
     */
    public function isImported()
    {
        return $this->is_imported;
    }

    /**
     * 设置导入状态, 这个方法需要立即持久化.
     * @param bool $val
     * @return void
     */
    public function setImport($val)
    {
        $this->is_imported = $val;
        $this->save();
    }

    /**
     * 返回用于 最新的home_timeline 请求的 since_id.
     * @return string|null
     */
    public static function getSinceId()
    {
        return DB::table('timeline_requests')->max('end_id');
    }

    /**
     * 返回用于 过去的home_timeline 请求的 max_id.
     * @return string|null
     */
    public static function getMaxId()
    {
        // 这里我们需要返回 (最小值 - 1) 的结果, 因为 推特api 的 max_id 值是可以 等于 的
        // 另外为了能在 32bit php 中也能正常运行, 这里把 - 1 操作直接在数据库中完成了
        $collection = DB::table('timeline_requests')
                    ->select(DB::raw('min(start_id)-1 as min'))->get();
        if (! $collection->isEmpty()) {
            return $collection->first()->min;
        }
        // return DB::table('timeline_requests')->min('start_id');
    }

    /**
     * 返回未导入的请求.
     * @param int $count
     * @param int $skip
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getUnimportedRequest($count, $skip = 0)
    {
        return self::where('is_imported', false)->where('count', '>', '0')
            ->orderBy('end_id', 'asc')->skip(0)->take($count)->get();
    }

    /**
     * 返回未导入的请求(文件)的总数.
     * @return int
     */
    public static function getUnimportedCounts()
    {
        return self::where('is_imported', false)
            ->where('count', '>', '0')->count();
    }
}
