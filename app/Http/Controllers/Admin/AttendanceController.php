<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use App\Services\StudentService;
use App\Services\SettingService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected $attendanceService;
    protected $studentService;
    protected $settingService;

    /**
     * AttendanceController constructor.
     */
    public function __construct(
        AttendanceService $attendanceService,
        StudentService $studentService,
        SettingService $settingService
    ) {
        $this->attendanceService = $attendanceService;
        $this->studentService = $studentService;
        $this->settingService = $settingService;
    }

    /**
     * Tampilkan data presensi dengan filter pencarian dan tanggal.
     */
    public function index(Request $request)
    {
        $filters = [
            'date' => $request->get('date', today()->toDateString()),
            'status' => $request->get('status'),
            'search' => $request->get('search'),
        ];

        $attendances = $this->attendanceService->getAttendancesWithFilters($filters);
        $students = $this->studentService->getAllStudents()->where('status', 'active');

        return view('admin.attendance.index', compact('attendances', 'students', 'filters'));
    }

    /**
     * Tampilkan rekap kehadiran per kelas/jurusan dengan filter rentang tanggal.
     */
    public function recap(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $recap = $this->attendanceService->getClassRecap($startDate, $endDate);

        $filters = [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];

        return view('admin.attendance.recap', compact('recap', 'filters'));
    }

    /**
     * Simpan data presensi secara manual oleh admin.
     */
    public function storeManual(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'status' => 'required|in:hadir,telat,sakit,izin,alpha',
            'check_in' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        try {
            $this->attendanceService->recordManualPresence($validated);
            return redirect()->route('admin.attendance.index', ['date' => $validated['date']])
                ->with('success', 'Presensi manual berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->route('admin.attendance.index')
                ->with('error', 'Gagal mencatat presensi: ' . $e->getMessage());
        }
    }

    /**
     * Export laporan presensi dalam format CSV.
     */
    public function exportCsv(Request $request)
    {
        $filters = [
            'date' => $request->get('date'),
            'status' => $request->get('status'),
            'search' => $request->get('search'),
        ];

        $attendances = $this->attendanceService->getAttendancesWithFilters($filters);
        
        $fileName = 'laporan_presensi_' . ($filters['date'] ?? 'semua') . '_' . time() . '.csv';
        
        $headers = array(
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('NISN', 'Nama Siswa', 'Jurusan / Peminatan', 'Kelas', 'Tanggal', 'Jam Masuk', 'Status', 'Metode', 'Confidence', 'Catatan');

        $callback = function() use($attendances, $columns) {
            $file = fopen('php://output', 'w');
            
            // Tambahkan BOM untuk support karakter UTF-8 di Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, $columns, ';'); // Menggunakan titik koma agar otomatis memisah kolom di Excel regional Indonesia

            foreach ($attendances as $att) {
                $row['NISN'] = $att->student->nisn;
                $row['Nama Siswa']  = $att->student->user->name;
                $row['Jurusan']  = $att->student->department;
                $row['Kelas']  = $att->student->class_name;
                $row['Tanggal']  = $att->date->format('Y-m-d');
                $row['Jam Masuk']  = $att->check_in ? $att->check_in : '-';
                $row['Status']  = strtoupper($att->status);
                $row['Metode']  = $att->method === 'face_recognition' ? 'Scan Wajah' : 'Manual Admin';
                $row['Confidence']  = $att->confidence ? number_format($att->confidence * 100, 1) . '%' : '-';
                $row['Catatan']  = $att->notes ? $att->notes : '-';

                fputcsv($file, array(
                    $row['NISN'],
                    $row['Nama Siswa'],
                    $row['Jurusan'],
                    $row['Kelas'],
                    $row['Tanggal'],
                    $row['Jam Masuk'],
                    $row['Status'],
                    $row['Metode'],
                    $row['Confidence'],
                    $row['Catatan']
                ), ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Cetak Laporan PDF (Tampilan Printable).
     */
    public function exportPdf(Request $request)
    {
        $filters = [
            'date' => $request->get('date'),
            'status' => $request->get('status'),
            'search' => $request->get('search'),
        ];

        $attendances = $this->attendanceService->getAttendancesWithFilters($filters);
        $dateLabel = $filters['date'] ? \Carbon\Carbon::parse($filters['date'])->translatedFormat('d F Y') : 'Semua Tanggal';
        $schoolInfo = $this->settingService->getSchoolInfo();

        return view('admin.attendance.print', compact('attendances', 'dateLabel', 'filters', 'schoolInfo'));
    }

    /**
     * Hapus data presensi secara permanen.
     */
    public function destroy($id)
    {
        try {
            $this->attendanceService->deletePresence($id);
            return redirect()->back()->with('success', 'Data presensi berhasil dihapus secara permanen!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data presensi: ' . $e->getMessage());
        }
    }
}
