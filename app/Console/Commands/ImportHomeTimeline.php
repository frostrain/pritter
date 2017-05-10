<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Storage;
use File;
use App\Models\TimelineRequest;
use App\Jobs\ImportTimelineResponse;
use Frostrain\Console\CommandWithArrayInputsTrait;

class ImportHomeTimeline extends Command
{
    use CommandWithArrayInputsTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pri:import-home-timeline {--c|count=3} {--f|file=} {--d|dir=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import home timeline from file';

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
        $count = $this->option('count');
        $files = $this->parseArrayInput($this->option('file'));
        $dirs = $this->parseArrayInput($this->option('dir'));

        // 如果 $files 或 $dirs 存在(本地文件), 则直接导入文件
        if ($files || $dirs) {
            $this->importFromFiles($files);
            $this->importFromDirs($dirs);
        } else {
            $this->importFromRequests($count);
        }
    }

    protected function importFromRequests($count)
    {
        $requests = TimelineRequest::getUnimportedRequest($count);
        foreach ($requests as $req) {
            $this->info("start import: [{$req->disk}] {$req->path}");
            $start = microtime(true);
            dispatch(new ImportTimelineResponse(null, $req));
            $end = microtime(true);
            $seconds = sprintf('%.2f', $end - $start);
            $this->info("import success, used time: $seconds");
        }
    }

    protected function importFromFiles($files)
    {
        foreach ($files as $f) {
            // TODO: 读取文件
            $data = [];
            dispatch(new ImportTimelineResponse($data));
        }
    }

    protected function importFromDirs($dirs)
    {
        $localDiskRoot = realpath(config('filesystems.disks.local.root'));
        $disk = 'local';

        $r = Storage::disk(null)->directories();
        var_dump($r);
        return ;


        foreach ($dirs as $dir) {
            $files = File::files($dir);
            foreach ($files as $file) {
                $real = realpath($file);
                if (strstr($real, $localDiskRoot)) {
                    $path = str_replace('\\', '/', substr($real, strlen($localDiskRoot) + 1));
                    // TODO
                }
            }

            // TODO: 读取文件
            $data = [];
            dispatch(new ImportTimelineResponse($data));
        }
    }
}
