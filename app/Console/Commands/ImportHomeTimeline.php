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
    protected $signature = 'pri:import-home-timeline {--c|count=3} {--f|file=} {--disk=} {--r|root-dir=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import home timeline from model or file';
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

        if ($rootDir) {
            $this->importFromDirectoryRecursively($rootDir, $this->diskName);
        } elseif ($files) {
            $this->importFromFiles($files);
        } else {
            $this->importFromRequests($count);
        }
    }

    /**
     * 从 TimelineRequest 模型对应的文件中导入数据.
     */
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

    protected function isValidHomeTimelineName($path)
    {
        return preg_match('/home_timeline_\d+?_\w+?.json$/', $path) ? true : false;
    }

    /**
     * 从指定目录下的 json 文件中导入数据.
     * @param string $root
     * @param string $diskName
     */
    protected function importFromDirectoryRecursively($root, $diskName)
    {
        $disk = Storage::disk($diskName);
        if (!$disk->exists($root)) {
            $this->error("dir [$root] not exists! aborted.");
            return;
        }

        $files = $disk->allFiles($root);
        $validPaths = array_filter($files, [$this, 'isValidHomeTimelineName']);
        $count = count($validPaths);
        if ($count == 0) {
            $this->info("None files founded in $root, aborted.");
            return;
        }

        if (!$this->confirm("Finded $count json files. Confirm import?")) {
            $this->info('Import aborted!');
            return;
        }
        foreach ($validPaths as $path) {
            $job = new GetTimelineRequestFromFile($diskName, $path);
            $request = $job->handle();

            $this->dispatchImportJob($request);
        }
    }
}
