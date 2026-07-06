<?php

namespace App\Repositories\Eloquent;

use App\Models\Log;
use App\Repositories\Contracts\LogRepositoryInterface;

class LogRepository extends BaseRepository implements LogRepositoryInterface
{
    /**
     * LogRepository constructor.
     */
    public function __construct(Log $model)
    {
        parent::__construct($model);
    }

    /**
     * Dapatkan log aktivitas terbaru dengan limit tertentu.
     */
    public function getRecentLogs(int $limit = 20)
    {
        return $this->model->with('user')
                           ->orderBy('created_at', 'desc')
                           ->limit($limit)
                           ->get();
    }

    /**
     * Log aktivitas user/sistem dengan cepat.
     */
    public function logActivity(?int $userId, string $action, ?string $details = null)
    {
        return $this->create([
            'user_id' => $userId,
            'action' => $action,
            'details' => $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
