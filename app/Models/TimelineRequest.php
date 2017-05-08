<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class TimelineRequest extends Model
{
    protected $fillable = [
        "disk", "path", "since_id", "max_id", "start_id", "end_id", "count",
        "is_success", "error",
    ];

    /**
     * 返回用于最新请求的 since_id.
     * @return string|null
     */
    public static function getSinceId()
    {
        return DB::table('timeline_requests')->max('end_id');
    }

    public static function getMaxId()
    {
        return DB::table('timeline_requests')->min('start_id');
    }
}
