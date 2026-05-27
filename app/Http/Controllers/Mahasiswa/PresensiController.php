<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use App\Repositories\Contracts\StudentRepositoryInterface;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    protected $attendanceService;
    protected $studentRepo;
    protected $attendanceRepo;

    /**
     * PresensiController constructor.
     */
    public function __construct(
        AttendanceService $attendanceService,
        StudentRepositoryInterface $studentRepo,
        AttendanceRepositoryInterface $attendanceRepo
    ) {
        $this->attendanceService = $attendanceService;
        $this->studentRepo = $studentRepo;
        $this->attendanceRepo = $attendanceRepo;
    }

    /**
     * Tampilkan halaman utama (Webcam Scanner & Status).
     */
    public function index()
    {
        $student = $this->studentRepo->allWithUser()->where('user_id', Auth::id())->first();
        if (!$student) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Profil mahasiswa tidak ditemukan.']);
        }

        // Cari presensi hari ini
        $todayPresence = $this->attendanceRepo->findByStudentAndDate($student->id, today()->toDateString());

        // Cari riwayat presensi mini (5 terakhir)
        $recentAttendances = $this->attendanceRepo->getStudentHistory($student->id)->take(5);

        // Tulis active student ke json untuk sinkronisasi otomatis dengan Python Mock Server
        try {
            file_put_contents(storage_path('app/active_student.json'), json_encode([
                'nim' => $student->nim,
                'name' => $student->user->name
            ]));
        } catch (\Exception $e) {
            // Abaikan jika ada error penulisan file
        }

        return view('mahasiswa.dashboard', compact('student', 'todayPresence', 'recentAttendances'));
    }

    /**
     * Proses AJAX scanning wajah presensi.
     */
    public function scan(Request $request)
    {
        $request->validate([
            'image' => 'required|string' // base64 string
        ]);

        $result = $this->attendanceService->scanFacePresence(Auth::id(), $request->image);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => [
                    'name' => $result['name'],
                    'confidence' => $result['confidence'],
                    'time' => now()->format('H:i')
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 422);
    }

    /**
     * Tampilkan riwayat presensi lengkap mahasiswa.
     */
    public function riwayat()
    {
        $student = $this->studentRepo->allWithUser()->where('user_id', Auth::id())->first();
        if (!$student) {
            return redirect()->route('login');
        }

        $attendances = $this->attendanceRepo->getStudentHistory($student->id);

        return view('mahasiswa.riwayat', compact('student', 'attendances'));
    }
}
