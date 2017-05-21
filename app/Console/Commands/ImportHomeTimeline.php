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
    protected $signature = 'pri:import-home-timeline {--c|count=3} {--f|file=} {--disk=} {--r|root-dir=} {--reimport}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import home timeline from model or file';
    /**
     * @var string
     */
    protected $diskName;
    /**
     * @var int
     */
    protected $count;
    /**
     * @var array
     */
    protected $files;
    /**
     * @var string
     */
    protected $rootDir;
    /**
     * @var bool
     */
    protected $isReimport;

    /* a new command instance.
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
        $this->count = $this->option('count');
        // 指定 storage 的 disk
        $this->diskName = $this->option('disk') ?: config('pritter.default_disk') ;
        // 这个选项存在表示从 $rootDir 下(recursive)找到的所有有效的文件导入
        $this->rootDir = $this->option('root-dir');
        $this->files = $this->parseArrayInput($this->option('file'));
        $this->isReimport = $this->option('reimport');

        if ($this->rootDir) {
            $this->importFromDirectoryRecursively();
        } elseif ($this->files) {
            $this->importFromFiles();
        } else {
            $this->importFromRequests();
        }
    }

    /**
     * 从 TimelineRequest 模型对应的文件中导入数据.
     */
    protected function importFromRequests()
    {
        $requests = TimelineRequest::getUnimportedRequest($this->count);
        if ($requests->isEmpty()) {
            $this->info('Nothing to import, skipped.');
            return;
        }
        foreach ($requests as $req) {
            $this->dispatchImportJob($req);
        }
    }

    protected function dispatchImportJob($request, $reimport = false)
    {
        $file = "[{$request->disk}] {$request->path}";
        if ($request->isImported()) {
            if ($reimport) {
                $this->info("Reimport file: $file");
            } else {
                $this->info("$file - already imported, skipped.");
                return;
            }
        }

        $this->info("start import: $file");
        $start = microtime(true);
        dispatch(new ImportTweets($request, $reimport));
        $end = microtime(true);
        $seconds = sprintf('%.2f', $end - $start);
        $this->info("import success, used time: $seconds");
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
     */
    protected function importFromDirectoryRecursively()
    {
        $root = $this->rootDir;
        $diskName = $this->diskName;
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

            $this->dispatchImportJob($request, $this->isReimport);
        }
    }
}
