<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Interfaces\Downloadable;
use App\Models\MediaTrait;
use Storage;

class Media extends Model implements Downloadable
{
    use MediaTrait;

    protected static $mediaType = [
        'unknown' => 0,
        'user_profile_image' => 1,
    ];

    protected $fillable = [
        'owner_id', 'type', 'origin_url', 'disk', 'path', 'size',
    ];

    public function getDownloadUrl()
    {
        return $this->origin_url;
    }

    public function setOriginUrlAttribute($url)
    {
        $old = array_get($this->attributes, 'origin_url');
        // 如果 url 没变, 不需要更新
        if ($old && $old == $url) {
            return;
        } else {
            // 如果有新的 url, 需要重置文件路径信息
            $this->attributes['origin_url'] = $url;
            $this->disk = null;
            $this->path = null;
            $this->is_handled = false;
        }
    }
}
