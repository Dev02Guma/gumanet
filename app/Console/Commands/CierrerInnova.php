<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\InnovaController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class CierrerInnova extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cmd:CierreInnova';

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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $url     = config('app.url').'/api/calcInnStat';
        $client = new Client(['verify' => false]);
        $client->get($url);
    }
}
