<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use App\Services\StudentService;
use App\Services\LogService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $attendanceService;
    protected $studentService;
    protected $logService;

    /**
     * DashboardController constructor.
     */
    public function __construct(
        AttendanceService $attendanceService,
        StudentService $studentService,
        LogService $logService
    ) {
        $this->attendanceService = $attendanceService;
        $this->studentService = $studentService;
        $this->logService = $logService;
    }

    /**
     * Tampilkan halaman dashboard utama admin.
     */
    public function index()
    {
        // 1. Dapatkan statistik presensi hari ini
        $stats = $this->attendanceService->getTodayStats();

        // 2. Dapatkan total mahasiswa aktif
        $students = $this->studentService->getAllStudents();
        $totalStudents = $students->count();
        $activeStudentsCount = $students->where('status', 'active')->count();

        // 3. Hitung persentase kehadiran hari ini
        $attendancePercentage = 0;
        if ($activeStudentsCount > 0) {
            $attendancePercentage = ($stats['hadir'] / $activeStudentsCount) * 100;
        }

        // 4. Dapatkan 5 log aktivitas terbaru
        $recentLogs = $this->logService->getRecentLogs(6);

        // 5. Dapatkan list presensi hari ini
        $todayPresences = $this->attendanceService->getTodayAttendances()->take(5);

        return view('admin.dashboard', compact(
            'stats',
            'totalStudents',
            'activeStudentsCount',
            'attendancePercentage',
            'recentLogs',
            'todayPresences'
        ));
    }

    /**
     * Tampilkan log audit aktivitas lengkap.
     */
    public function logs()
    {
        $logs = $this->logService->getRecentLogs(100);
        return view('admin.logs', compact('logs'));
    }
}
