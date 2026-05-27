<?php

namespace App\Repositories\Eloquent;

use App\Models\Attendance;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use Carbon\Carbon;

class AttendanceRepository extends BaseRepository implements AttendanceRepositoryInterface
{
    /**
     * AttendanceRepository constructor.
     */
    public function __construct(Attendance $model)
    {
        parent::__construct($model);
    }

    /**
     * Cari presensi mahasiswa berdasarkan student_id dan tanggal tertentu.
     */
    public function findByStudentAndDate(int $studentId, string $date)
    {
        return $this->model->where('student_id', $studentId)
                           ->whereDate('date', $date)
                           ->first();
    }

    /**
     * Dapatkan semua presensi hari ini.
     */
    public function getTodayAttendances()
    {
        return $this->model->with('student.user')
                           ->whereDate('date', today())
                           ->get();
    }

    /**
     * Dapatkan daftar presensi dengan berbagai filter (tanggal, NIM/Nama, status).
     */
    public function getWithFilters(array $filters)
    {
        $query = $this->model->with('student.user');

        if (!empty($filters['date'])) {
            $query->whereDate('date', $filters['date']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        return $query->orderBy('date', 'desc')
                     ->orderBy('check_in', 'desc')
                     ->get();
    }

    /**
     * Dapatkan riwayat kehadiran seorang mahasiswa tertentu.
     */
    public function getStudentHistory(int $studentId)
    {
        return $this->model->where('student_id', $studentId)
                           ->orderBy('date', 'desc')
                           ->get();
    }

    /**
     * Hitung statistik kehadiran hari ini (Hadir, Sakit, Izin, Alpha).
     */
    public function getTodayStats()
    {
        $stats = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpha' => 0,
            'total' => 0
        ];

        $todayPresences = $this->model->whereDate('date', today())->get();

        foreach ($todayPresences as $presence) {
            if (isset($stats[$presence->status])) {
                $stats[$presence->status]++;
            }
        }

        $stats['total'] = count($todayPresences);

        return $stats;
    }
}
