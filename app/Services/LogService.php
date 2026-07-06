<?php

namespace App\Services;

use App\Repositories\Contracts\LogRepositoryInterface;

class LogService
{
    protected $logRepo;

    /**
     * LogService constructor.
     */
    public function __construct(LogRepositoryInterface $logRepo)
    {
        $this->logRepo = $logRepo;
    }

    /**
     * Dapatkan daftar log aktivitas terbaru.
     */
    public function getRecentLogs(int $limit = 20)
    {
        return $this->logRepo->getRecentLogs($limit);
    }

    /**
     * Catat log aktivitas ke database.
     */
    public function logActivity(?int $userId, string $action, ?string $details = null)
    {
        return $this->logRepo->logActivity($userId, $action, $details);
    }
}
