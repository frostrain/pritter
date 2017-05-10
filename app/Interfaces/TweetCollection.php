<?php

namespace App\Interfaces;

interface TweetCollection
{
    /**
     * 返回 tweet 数据
     * @return array
     */
    public function getTweets();
    /**
     * 返回是否已经导入
     * @return bool
     */
    public function isImported();
    /**
     * 设置导入状态.
     * @param bool $val
     * @return void
     */
    public function setImport($val);
}