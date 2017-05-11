<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Frostrain\Console\CommandWithArrayInputsTrait;
use Storage;
use File;
use App\Models\TimelineRequest;
use App\Jobs\ImportTweets;
use App\Jobs\GetTimelineRequestFromFile;


class ImportHomeTimeline extends Command
{
    use CommandWithArrayInputsTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pri:import-home-timeline {--c|count=3} {--f|file=} {--d|dir=} {--disk=} {--r|root-dir=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import home timeline from file';
    protected $disk;
    /**
     * @var string
     */
    protected $diskName;

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
        // 从数据库中搜索request来导入
        $count = $this->option('count');
        // 指定 storage 的 disk
        $this->diskName = $this->option('disk') ?: config('pritter.default_disk') ;
        $this->disk = Storage::disk($this->diskName);
        // 这个选项存在表示从 $rootDir 下(recursive)找到的所有有效的文件导入
        $rootDir = $this->option('root-dir');
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
        if ($requests->isEmpty()) {
            $this->info('Nothing to import, skipped.');
            return;
        }
        foreach ($requests as $req) {
            $this->dispatchImportJob($req);
        }
    }

    protected function dispatchImportJob($request)
    {
        $file = "[{$request->disk}] {$request->path}";
        if ($request->isImported()) {
            $this->info("$file - already imported, skipped.");
        } else {
            $this->info("start import: $file");
            $start = microtime(true);
            dispatch(new ImportTweets($request));
            $end = microtime(true);
            $seconds = sprintf('%.2f', $end - $start);
            $this->info("import success, used time: $seconds");
        }
    }

    protected function importFromFiles($files)
    {
        foreach ($files as $f) {
            // TODO
        }
    }

    /**
     * @param string $root
     */
    protected function importFromDirectoryRecursively($root)
    {
        // TODO
    }

    protected function importFromDirs($dirs)
    {
        // TODO: 重构这个方法
        $localDiskRoot = realpath(config('filesystems.disks.local.root'));

        // $r = Storage::disk(null)->directories();

        foreach ($dirs as $dir) {
            $files = File::files($dir);
            if (count($files) == 0) {
                $this->error("empty dir [$dir] skipped.");
                continue;
            }
            foreach ($files as $file) {
                $real = realpath($file);
                // 判断是否为local存储下的文件
                if (strstr($real, $localDiskRoot)) {
                    $path = substr($real, strlen($localDiskRoot) + 1);
                    if ($this->disk->exists($path)) {
                        $job = new GetTimelineRequestFromFile($this->diskName, $path);
                        $request = $job->handle();
                        $this->dispatchImportJob($request);
                    }
                }
            }
        }
    }
}
