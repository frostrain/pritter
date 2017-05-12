<?php

namespace App\Interfaces;

interface Downloadable
{
    /**
     * 获取下载地址.
     * @return string
     */
    public function getDownloadUrl();
    /**
     * 设置下的文件位置.
     * @param string $disk
     * @param string $path
     */
    public function setFilePath($disk, $path);

    public function setFailed();
}