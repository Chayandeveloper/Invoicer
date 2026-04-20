<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait HasActivityLogs
{
    public static function bootHasActivityLogs()
    {
        static::created(function ($model) {
            $model->logActivity('created');
        });

        static::updated(function ($model) {
            $model->logActivity('updated');
        });

        static::deleted(function ($model) {
            $model->logActivity('deleted');
        });
    }

    protected function logActivity($action)
    {
        if (!Auth::check()) {
            return;
        }

        $changes = null;
        if ($action === 'updated') {
            $changes = [
                'before' => array_intersect_key($this->getOriginal(), $this->getDirty()),
                'after' => $this->getDirty(),
            ];

            // Don't log if no meaningful changes
            if (empty($changes['after'])) {
                return;
            }
        } elseif ($action === 'created') {
            $changes = ['after' => $this->getAttributes()];
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'loggable_type' => get_class($this),
            'loggable_id' => $this->id,
            'action' => $action,
            'changes' => $changes,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
