<?php

namespace App\Jobs;

use App\Interfaces\Downloadable;
use Storage;

/**
 * 下载推特上的多媒体文件.
 * 图片/视频封面图 等
 */
class DownloadTweetMedia
{
    protected $entity;
    /**
     * @var string
     */
    protected $disk;

    /**
     * Create a new job instance.
     * @param \App\Interfaces\Downloadable $entity
     * @param string $disk
     * @return void
     */
    public function __construct(Downloadable $entity, $disk)
    {
        $this->entity = $entity;
        $this->disk = $disk;
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle()
    {
        $this->downloadEntity($this->entity, $this->disk);
     }

    protected function downloadEntity($entity, $disk)
    {
        $url = $entity->getDownloadUrl();
        $contents = $this->downloadContents($url);
        $path = parse_url($url, PHP_URL_PATH);
        Storage::disk($disk)->put($path, $contents);
        $entity->setFilePath($disk, $path);
    }

    /**
     * @param string $url
     * @return string
     */
    protected function downloadContents($url)
    {
        $client = app('guzzle.twitter');
        $response = $client->get($url);
        return $contents = (string) $response->getBody();
    }
}
