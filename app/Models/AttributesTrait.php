<?php

namespace App\Models;

trait AttributesTrait
{
    /**
     * 返回 只能增加的(数值类型) 字段名数组.
     * @return string[]
     */
    public function getIncrementAttributes()
    {
        return [];
    }

    /**
     * 返回 布尔类型 的字段名数组.
     * @return string[]
     */
    public function getBooleanAttributes()
    {
        return [];
    }

    /**
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        $increments = $this->getIncrementAttributes();
        $booleans = $this->getBooleanAttributes();

        if (in_array($key, $increments)) {
            $old = array_get($this->attributes, $key);
            // 如果字段只允许变大, 则对于更小的值直接返回
            if (!is_null($old) && $old > $value) {
                return $this;
            }
        }

        // 将传入的 $value 转换为 0 或 1.
        // 虽然不转换 $value 也可以工作, 但是 $model->getDirty() 会认为有数据'脏了'.
        if (in_array($key, $booleans)) {
            $value = $value ? 1 : 0;
        }

        return parent::setAttribute($key, $value);
    }
}
