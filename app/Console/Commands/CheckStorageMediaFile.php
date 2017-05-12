<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TweetMedia;
use App\Models\Media;

/**
 * 尝试确认本地文件是否存在, 如果存在, 直接关联.
 */
class CheckStorageMediaFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pri:check-storage-media-file {--d|disk=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Try to find media file from storage.';

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
        $disk = $this->option('disk') ?: config('pritter.default_public_disk');

        $mediaCollection = Media::getUndownloaded(-1);
        $tweetMediaCollection = TweetMedia::getUndownloaded(-1);

        $c1 = $this->handleCollection($mediaCollection, $disk);
        $this->info("Finded $c1 media files.");
        $c2 = $this->handleCollection($tweetMediaCollection, $disk);
        $this->info("Finded $c2 tweet media files.");
    }

    public function handleCollection($collection, $disk)
    {
        $count = 0;
        foreach ($collection as $media) {
            if ($media->tryToFindFileFromStorage($disk)) {
                $count++;
            }
        }
        return $count;
    }
}
