<?php

namespace App\Models;

use Storage;

trait MediaTrait
{
    /**
     * 获取用来 排序 的字段. 用于优先下载.
     * @return array ['field' => '..', 'direction' => '..']
     */
    protected static function getPriorityOrderBy(){
        return ['field' => 'id' , 'direction' => 'desc'];
    }

    /**
     * 标记为下载失败.
     */
    public function setFailed()
    {
        $this->is_handled = true;
        $this->is_failed = true;
        $this->save();
    }
    /**
     * 返回未下载的 TweetMedia 集合.
     * @param int $count -1 表示不限制返回数目.
     * @param int $skip
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getUndownloaded($count, $skip = 0)
    {
        $sort = self::getPriorityOrderBy();

        // 返回所有
        if ($count === -1) {
            // 注意, skip() 必须与 take() 一起使用, 否则生成的 sql 无效..
            return self::where('is_handled', false)
                ->orderBy($sort['field'], $sort['direction'])->get();
        }

        return self::where('is_handled', false)
            ->orderBy($sort['field'], $sort['direction'])
            ->skip($skip)->take($count)->get();
    }

    /**
     * 尝试确认本地文件是否存在, 如果存在, 直接关联.
     * @param string $disk
     * @return bool
     */
    public function tryToFindFileFromStorage($disk = null)
    {
        $url = $this->getDownloadUrl();
        $path = parse_url($url, PHP_URL_PATH);
        $disk = $disk ?: config('pritter.default_public_disk');
        if (Storage::disk($disk)->exists($path)) {
            $this->path = $path;
            $this->disk = $disk;
            $this->size = Storage::disk($disk)->size($path);
            $this->is_handled = true;
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * (本地)文件是否可用.
     * @return bool
     */
    public function isAvailable()
    {
        return $this->path ? true : false;
    }

    /**
     * 设置文件对应的位置.
     * @param string $disk
     * @param string $path
     */
    public function setFilePath($disk, $path)
    {
        if (Storage::disk($disk)->exists($path)) {
            $size = Storage::disk($disk)->size($path);
            $this->disk = $disk;
            $this->path = $path;
            $this->size = $size;
            // 标记为已处理
            $this->is_handled = true;
            $this->save();
        } else {
            throw new \InvalidArgumentException("文件不存在: [$disk] $path");
        }
    }

    /**
     * @return string
     */
    public function getUrlAttribute()
    {
        if ($this->disk && $this->path) {
            try {
                return Storage::disk($this->disk)->url($this->path);
            } catch (\Exception $e) {
                // TODO ...
            }
        }
        return '/img/default_img.gif';
    }

    /**
     * @param string $val
     */
    public function setTypeAttribute($val)
    {
        $types = self::$mediaType;
        if (!isset($types[$val])) {
            // throw \Exception('未知的 media 类型: '.$val);
            // 未知时, 先设置为 0
            $value = 0;
        } else {
            $value = $types[$val];
        }

        $this->attributes['type'] = $value;
    }

    /**
     * @return string
     */
    public function getTypeAttribute()
    {
        $key = array_search($this->attributes['type'], self::$mediaType);
        return $key;
    }

    public static function getTypeValue($typeName)
    {
        if (isset(self::$mediaType[$typeName])) {
            return self::$mediaType[$typeName];
        } else {
            throw new \InvalidArgumentException("不合法的类型: $typeName");
        }
    }
}
