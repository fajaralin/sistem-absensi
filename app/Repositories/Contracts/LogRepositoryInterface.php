<?php

namespace App\Repositories\Contracts;

interface LogRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Dapatkan log aktivitas terbaru dengan limit tertentu.
     */
    public function getRecentLogs(int $limit = 20);

    /**
     * Log aktivitas user/sistem dengan cepat.
     */
    public function logActivity(?int $userId, string $action, ?string $details = null);
}
