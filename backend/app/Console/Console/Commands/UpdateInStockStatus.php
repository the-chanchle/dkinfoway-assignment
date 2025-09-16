<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stock;
use Carbon\Carbon;

class UpdateInStockStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stocks:update-instock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update stock entries status to In-Stock where in_stock_date = today';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today()->toDateString();

        $count = Stock::whereDate('in_stock_date', $today)
            ->update(['status' => 'In-Stock']);

        $this->info("Updated {$count} stock entries to In-Stock.");
        return 0;
    }
}