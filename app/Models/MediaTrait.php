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
        return ['id' , 'desc'];
    }
    /**
     * 返回未下载的 TweetMedia 集合.
     * @param int $count
     * @param int $skip
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getUndownloaded($count, $skip = 0)
    {
        $sort = self::getPriorityOrderBy();
        return self::where('is_handled', false)
            ->orderBy($sort['field'], $sort['direction'])
            ->skip(0)->take($count)->get();
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
            return Storage::disk($this->disk)->url($this->path);
        } else {
            return '/img/default_img.gif';
        }
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
            throw new \InvalidArgumentExcepttion("不合法的类型: $typeName");
        }
    }
}
