<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\StudentRepositoryInterface;
use App\Services\AttendanceService;
use Illuminate\Http\Request;

class PublicAbsensiController extends Controller
{
    protected $studentRepo;
    protected $attendanceService;

    /**
     * PublicAbsensiController constructor.
     */
    public function __construct(
        StudentRepositoryInterface $studentRepo,
        AttendanceService $attendanceService
    ) {
        $this->studentRepo = $studentRepo;
        $this->attendanceService = $attendanceService;
    }

    /**
     * Tampilkan landing page kiosk absensi lobi sekolah.
     */
    public function index()
    {
        return view('absensi');
    }

    /**
     * AJAX POST endpoint untuk mencari siswa berdasarkan NISN.
     */
    public function searchSiswa(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string|max:50'
        ]);

        $nisn = $request->nisn;
        $student = $this->studentRepo->findByNisn($nisn);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'NISN tidak terdaftar di sistem. Silakan hubungi admin sekolah.'
            ], 404);
        }

        $student->load(['user', 'faceData' => function ($q) {
            $q->where('status', 'active');
        }]);

        if ($student->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Status siswa tidak aktif. Silakan hubungi operator sekolah.'
            ], 403);
        }

        $hasFace = $student->faceData->isNotEmpty();
        $faceUrl = $hasFace ? asset('storage/' . $student->faceData->first()->image_path) : null;

        return response()->json([
            'success' => true,
            'student' => [
                'id' => $student->id,
                'nisn' => $student->nisn,
                'name' => $student->user->name,
                'class_name' => $student->class_name,
                'department' => $student->department,
                'photo_url' => $student->photo_path ? asset('storage/' . $student->photo_path) : null,
                'has_face' => $hasFace,
                'face_url' => $faceUrl
            ]
        ]);
    }

    /**
     * AJAX POST endpoint untuk verifikasi wajah & catat presensi.
     */
    public function scan(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string|max:50',
            'image' => 'required|string' // base64 string
        ]);

        try {
            $result = $this->attendanceService->scanFacePresenceByNisn($request->nisn, $request->image);
            
            if ($result['success']) {
                return response()->json($result);
            }

            return response()->json($result, 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX GET endpoint untuk mendapatkan saran siswa berdasarkan nama.
     */
    public function suggestSiswa(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100'
        ]);

        $query = $request->query('q');
        $students = $this->studentRepo->searchByName($query, 6);

        $results = $students->map(function ($student) {
            return [
                'id' => $student->id,
                'nisn' => $student->nisn,
                'name' => $student->user->name,
                'class_name' => $student->class_name,
                'department' => $student->department
            ];
        });

        return response()->json([
            'success' => true,
            'students' => $results
        ]);
    }
}
