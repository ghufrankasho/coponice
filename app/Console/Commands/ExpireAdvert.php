<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Advert;

class ExpireAdvert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'advert:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description =  'Set product visibility to false if the expire date is reached';

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
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();

        // Update products that have reached their expire date
        Advert::where('expire_date', '<=', $now)
            ->where('visible', true)
            ->update(['visible' => false]);

        $this->info('Expired products have been updated.');
    }
}