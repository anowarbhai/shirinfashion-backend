<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class RoundRobinService
{
    private const PROCESSING_STATUS = 'processing';

    private const INCOMPLETE_STATUS = 'incomplete';

    public function assignOrder(Order $order): ?User
    {
        $moderators = $this->getActiveModerators();

        if ($moderators->isEmpty()) {
            return null;
        }

        $assignedModerator = $this->getNextModerator($order->status, $moderators);

        if ($assignedModerator) {
            $order->update(['moderator_id' => $assignedModerator->id]);
        }

        return $assignedModerator;
    }

    private function getActiveModerators()
    {
        return User::whereHas('roles', function ($query) {
            $query->where('slug', 'moderator');
        })
            ->where('is_active', true)
            ->orderBy('id')
            ->get();
    }

    private function getNextModerator(string $status, $moderators)
    {
        $moderatorArray = $moderators->values()->all();
        $count = count($moderatorArray);

        if ($count === 0) {
            return null;
        }

        $lastModeratorId = $this->getLastAssignedModeratorId($status);

        if ($lastModeratorId === null) {
            $nextIndex = 0;
        } else {
            $lastIndex = $this->findModeratorIndex($moderatorArray, $lastModeratorId);
            $nextIndex = ($lastIndex + 1) % $count;
        }

        $nextModerator = $moderatorArray[$nextIndex];

        $this->updateLastAssignedModeratorId($status, $nextModerator->id);

        return $nextModerator;
    }

    private function findModeratorIndex(array $moderators, int $moderatorId): int
    {
        foreach ($moderators as $index => $mod) {
            if ($mod->id === $moderatorId) {
                return $index;
            }
        }

        return -1;
    }

    private function getLastAssignedModeratorId(string $status): ?int
    {
        $lastOrder = Order::where('status', $status)
            ->whereNotNull('moderator_id')
            ->orderBy('id', 'desc')
            ->first();

        return $lastOrder?->moderator_id;
    }

    private function updateLastAssignedModeratorId(string $status, int $moderatorId): void
    {
        $cacheKey = 'round_robin_last_'.$status;
        Cache::put($cacheKey, $moderatorId, now()->addDays(30));
    }

    public function reassignOrder(Order $order, int $moderatorId): void
    {
        $order->update(['moderator_id' => $moderatorId]);
    }

    public function unassignOrder(Order $order): void
    {
        $order->update(['moderator_id' => null]);
    }
}
