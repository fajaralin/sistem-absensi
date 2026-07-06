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
            'telat' => 0,
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

    /**
     * Dapatkan tren jumlah kehadiran (per status) per hari selama N hari terakhir.
     * Hasil selalu berisi N entri berurutan (hari tanpa data diisi nol) agar siap dipakai chart.
     */
    public function getAttendanceTrend(int $days = 7)
    {
        $startDate = today()->copy()->subDays($days - 1);

        $records = $this->model
            ->selectRaw('date, status, COUNT(*) as total')
            ->where('date', '>=', $startDate->toDateString())
            ->groupBy('date', 'status')
            ->get();

        $lookup = [];
        foreach ($records as $record) {
            $dateKey = Carbon::parse($record->date)->toDateString();
            $lookup[$dateKey][$record->status] = (int) $record->total;
        }

        $trend = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dateKey = $date->toDateString();

            $trend[] = [
                'date' => $dateKey,
                'label' => $date->translatedFormat('d M'),
                'hadir' => $lookup[$dateKey]['hadir'] ?? 0,
                'telat' => $lookup[$dateKey]['telat'] ?? 0,
                'sakit' => $lookup[$dateKey]['sakit'] ?? 0,
                'izin' => $lookup[$dateKey]['izin'] ?? 0,
                'alpha' => $lookup[$dateKey]['alpha'] ?? 0,
            ];
        }

        return $trend;
    }

    /**
     * Dapatkan rekap agregat kehadiran per kelas/jurusan dalam rentang tanggal tertentu.
     * Mengembalikan koleksi berisi jumlah per status & persentase kehadiran (Hadir + Telat dianggap masuk).
     */
    public function getClassRecap(string $startDate, string $endDate)
    {
        return $this->model
            ->selectRaw('students.class_name, students.department, attendances.status, COUNT(*) as total')
            ->join('students', 'students.id', '=', 'attendances.student_id')
            ->whereBetween('attendances.date', [$startDate, $endDate])
            ->groupBy('students.class_name', 'students.department', 'attendances.status')
            ->get()
            ->groupBy(function ($item) {
                return $item->class_name . '|' . $item->department;
            })
            ->map(function ($items, $key) {
                [$className, $department] = explode('|', $key);

                $recap = [
                    'class_name' => $className,
                    'department' => $department,
                    'hadir' => 0,
                    'telat' => 0,
                    'sakit' => 0,
                    'izin' => 0,
                    'alpha' => 0,
                    'total' => 0,
                ];

                foreach ($items as $item) {
                    if (array_key_exists($item->status, $recap)) {
                        $recap[$item->status] = (int) $item->total;
                    }
                    $recap['total'] += (int) $item->total;
                }

                $hadirSetara = $recap['hadir'] + $recap['telat'];
                $recap['attendance_rate'] = $recap['total'] > 0
                    ? round(($hadirSetara / $recap['total']) * 100, 1)
                    : 0.0;

                return $recap;
            })
            ->sortByDesc('attendance_rate')
            ->values();
    }
}
