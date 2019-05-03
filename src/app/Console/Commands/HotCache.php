<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HotCache extends Command
{
    protected $round = 60;
    protected $period = 1;
    protected $timeout = 5;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hotcache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /*
     * Cache /api/v1/users route
     */
    protected function cache()
    {
        Log::info("Cache Exec");

        $key = 'page:/api/v1/users';
        $list = User::all();

        app('redis')->set($key, $list->toJson());
        app('redis')->expire($key, $this->timeout);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info("Cache Start Cron");

        $dt = Carbon::now();
        $iteration = $this->round / $this->period;
        $start = $dt->unix();
        $exitTimeout = 0;

        do {
            $diff = Carbon::now()->unix() - $start;
            if ($diff >= $this->round - $exitTimeout) {
                break;
            }

            try {
                $this->cache();
                time_sleep_until($dt->addSeconds($this->period)->timestamp);
                $exitTimeout = 1;
            } catch (\Exception $e) {
                time_sleep_until($dt->addSeconds($this->timeout)->timestamp);
                $exitTimeout = 5;
            }
        } while ($iteration-- > 1);
    }
}
