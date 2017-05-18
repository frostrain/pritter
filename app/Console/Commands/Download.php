<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Media;
use App\Models\TweetMedia;
use App\Jobs\DownloadFile;

/**
 * 下载命令.
 * 可以用于下载 TweetMedia/Media 相关的文件.
 */
class Download extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature
        = 'pri:download '.
        '{type* : media,tweet-media} '.
        '{--c|count=20 : target download counts for each type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download <fg=yellow>files</fg=yellow>: images, user avatars, etc';

    /**
     * 用来记录任务中下载的文件.
     * @var array
     */
    protected $downloaded = [];
    /**
     * @var string
     */
    protected $disk;
    /**
     * @var int 指定的下载数目.
     */
    protected $count;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->disk = config('pritter.default_public_disk');
        $this->count = $this->option('count');
        // $type 是 array
        $types = $this->argument('type');

        foreach ($types as $type) {
            $func = 'download'.studly_case($type);
            if (is_callable([$this, $func])) {
                $this->$func();
            } else {
                $this->info("Dwonload type [$type] is invalid, skipped.");
            }
        }


        $header = ['path', 'size'];
        if ($this->output->isVerbose()) {
            $this->table($header, $this->downloaded);
        }
    }

    protected function downloadTweetMedia()
    {
        $collection = TweetMedia::getUndownloaded($this->count);
        $this->handleMediaCollection($collection, 'tweet-media');
    }

    protected function downloadMedia()
    {
        $collection = Media::getUndownloaded($this->count);
        $this->handleMediaCollection($collection, 'media');
    }

    protected function handleMediaCollection($collection, $type)
    {
        $finded = $collection->count();
        $disk = $this->disk;

        if (!$finded) {
            $this->info("[media] Nothing new to download, skipped.");
            return;
        }

        $this->info("[media] $finded files to download.");

        $success = 0;
        foreach ($collection as $media) {
            if ($this->dispatchDownloadJob($media, $disk, $type)) {
                $success++;
            }
        }


        foreach ($collection as $media) {
            if ($media->path) {
                $path = "[{$media->disk}] {$media->path}";
                $this->downloaded[] = [$path, $media->size];
            }
        }

        $info = "[$type] downloaded count: $success / $finded / {$this->count}";
        $this->info($info);
    }

    protected function dispatchDownloadJob($media, $disk, $type)
    {
        $url = $media->getDownloadUrl();
        $this->info("[$type] start downloading: ".$url);
        try {
            $job = new DownloadFile($media, $disk, 'guzzle.twitter');
            $job->handle();
            $path = "({$media->disk}) {$media->path}";
            $this->info("[$type] download success: $path");
            return true;
        } catch (\Exception $e) {
            $this->error("[$type] download failed: $url");
            $this->error($e->getMessage());
            return false;
        }
    }
}
