<?php

namespace App\Console\Commands;

use App\Scraper\Chinadaily as ScraperChinadaily;
use Illuminate\Console\Command;

class Chinadaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape news china daily';

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
        $bot = new ScraperChinadaily();
        $bot->scraper();
    }
}
