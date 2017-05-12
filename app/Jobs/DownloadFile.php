<?php

namespace App\Jobs;

use App\Interfaces\Downloadable;
use Storage;

/**
 * 下载文件.
 * 比如退特的 图片/视频封面图 等.
 */
class DownloadFile
{
    /**
     * @var \App\Interfaces\Downloadable
     */
    protected $entity;
    /**
     * @var string
     */
    protected $disk;
    /**
     * @var string
     */
    protected $clientName;

    /**
     * Create a new job instance.
     * @param \App\Interfaces\Downloadable $entity
     * @param string $disk
     * @param string $clientName
     * @return void
     */
    public function __construct(Downloadable $entity, $disk, $clientName)
    {
        $this->entity = $entity;
        $this->disk = $disk;
        $this->clientName = $clientName;
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle()
    {
        $this->downloadEntity($this->entity, $this->disk);
     }

    /**
     * 如果下载失败, 这里会抛出异常.
     */
    protected function downloadEntity($entity, $disk)
    {
        $url = $entity->getDownloadUrl();
        try {
            $contents = $this->downloadContents($url);
            $path = parse_url($url, PHP_URL_PATH);
            Storage::disk($disk)->put($path, $contents);
            $entity->setFilePath($disk, $path);
        } catch (\Exception $e) {
            // TODO: 重试?
            // 注意, 即使 404 貌似也有可能重试成功
            $entity->setFailed();
            throw $e;
        }
    }

    /**
     * @param string $url
     * @return string
     */
    protected function downloadContents($url)
    {
        $client = app($this->clientName);
        $response = $client->get($url);
        return $contents = (string) $response->getBody();
    }
}
