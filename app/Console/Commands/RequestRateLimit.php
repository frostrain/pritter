<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Twitter;
use Carbon\Carbon;

/**
 * 获取 twitter api 的请求速率限制.
 */
class RequestRateLimit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pri:rate-limit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'request twitter api rate limit';

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
        $r = Twitter::getAppRateLimit(['format'=>'json']);
        if (is_string($r)) {
            $r = json_decode($r, true);
        }

        // var_dump($r);

        // api地址 => 点号形式的数据路径
        $limits = [
            '/application/rate_limit_status' => 'application./application/rate_limit_status',
            '/statuses/home_timeline' => 'statuses./statuses/home_timeline',
            '/trends/place' => 'trends./trends/place',
        ];

        $data = [];
        foreach ($limits as $api => $path) {
            // 所有数据都在 resources 字段下面
            $path = 'resources.'.$path;
            $remaining = array_get($r, $path.'.remaining');
            $limit = array_get($r, $path.'.limit');
            $resetTime = (string)Carbon::createFromTimestamp(array_get($r, $path.'.reset'));
            $data[] = [$api, '<fg=yellow>'.$remaining.'</fg=yellow> / '.$limit, $resetTime];
        }
        $header = ['api', 'limit', 'reset'];
        $this->table($header, $data);
    }
}
