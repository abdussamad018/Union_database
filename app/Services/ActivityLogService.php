<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogService
{
    public function log(string $action, Model $model, ?array $changes = null, ?int $userId = null): void
    {
        ActivityLog::create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'model_type' => $model->getMorphClass(),
            'model_id' => $model->id,
            'changes' => $changes,
            'created_at' => now(),
        ]);
    }
}
