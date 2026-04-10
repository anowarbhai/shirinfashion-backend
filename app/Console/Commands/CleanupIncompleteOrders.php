<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CleanupIncompleteOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-incomplete-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete incomplete orders older than 30 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Delete incomplete orders older than 30 minutes
        $deletedCount = Order::where('status', 'incomplete')
            ->where('created_at', '<', Carbon::now()->subMinutes(30))
            ->delete();

        $this->info("Deleted {$deletedCount} incomplete order(s) older than 30 minutes.");

        return Command::SUCCESS;
    }
}
