<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\RoundRobinService;
use Illuminate\Console\Command;

class AssignIncompleteOrders extends Command
{
    protected $signature = 'orders:assign-incomplete';
    protected $description = 'Assign unassigned incomplete orders to moderators via round-robin';

    public function handle(): int
    {
        $service = app(RoundRobinService::class);
        
        $unassignedIncomplete = Order::where('status', 'incomplete')
            ->whereNull('moderator_id')
            ->get();
        
        $count = 0;
        foreach ($unassignedIncomplete as $order) {
            $assigned = $service->assignOrder($order);
            if ($assigned) {
                $count++;
                $this->info("Order #{$order->id} assigned to {$assigned->name}");
            }
        }
        
        $this->info("Total assigned: {$count} out of {$unassignedIncomplete->count()}");
        
        return Command::SUCCESS;
    }
}