<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Media;
use App\Jobs\DownloadFile;

class DownloadMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pri:download-media {--c|count=5}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download <fg=yellow>media</fg=yellow>: user profile img, etc';

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
        $disk = config('pritter.default_public_disk');
        $count = $this->option('count');
        $collection = Media::getUndownloaded($count);

        $finded = $collection->count();

        if (!$finded) {
            $this->info('Nothing to download, skipped.');
            return;
        }
        foreach ($collection as $media) {
            $this->dispatchDownloadJob($media, $disk);
        }

        $rows = [];
        $header = ['No.', 'path', 'size'];
        $count = 0;
        foreach ($collection as $media) {
            if ($media->is_handled) {
                $path = "[{$media->disk}] {$media->path}";
                $rows[] = [++$count, $path, $media->size];
            }
        }

        $this->info("downloaded files count: $count / $finded");
        if ($this->output->isVerbose()) {
            $this->table($header, $rows);
        }
    }

    protected function dispatchDownloadJob($media, $disk)
    {
        $this->info('start downloading: '.$media->media_url);
        $job = new DownloadFile($media, $disk);
        $job->handle();
        $path = "[{$media->disk}] {$media->path}";
        $this->info('download success: '.$path);
    }
}
