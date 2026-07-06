<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\StudentRepositoryInterface;
use App\Services\AttendanceService;
use App\Services\SettingService;
use App\Services\Contracts\FaceRecognitionServiceInterface;
use Illuminate\Http\Request;

class PublicAbsensiController extends Controller
{
    protected $studentRepo;
    protected $attendanceService;
    protected $settingService;
    protected $faceService;

    /**
     * PublicAbsensiController constructor.
     */
    public function __construct(
        StudentRepositoryInterface $studentRepo,
        AttendanceService $attendanceService,
        SettingService $settingService,
        FaceRecognitionServiceInterface $faceService
    ) {
        $this->studentRepo = $studentRepo;
        $this->attendanceService = $attendanceService;
        $this->settingService = $settingService;
        $this->faceService = $faceService;
    }

    /**
     * Tampilkan landing page kiosk absensi lobi sekolah.
     */
    public function index()
    {
        $settings = $this->settingService->getAll();
        
        // Pastikan python server aktif saat web dibuka
        $this->faceService->initializeServer();
        
        return view('absensi', compact('settings'));
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
            'image' => 'required|string', // base64 string
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        try {
            $result = $this->attendanceService->scanFacePresenceByNisn(
                $request->nisn,
                $request->image,
                $request->latitude ? floatval($request->latitude) : null,
                $request->longitude ? floatval($request->longitude) : null
            );
            
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
     * AJAX POST endpoint untuk scan langsung tanpa input NISN/nama.
     * Sistem mengidentifikasi sendiri siswa mana yang sedang scan (1-to-many).
     */
    public function scanAuto(Request $request)
    {
        $request->validate([
            'image' => 'required|string', // base64 string
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'purpose' => 'nullable|string|in:attendance,leave'
        ]);

        try {
            $purpose = $request->input('purpose', 'attendance');

            if ($purpose === 'leave') {
                $result = $this->attendanceService->identifyStudentForLeave($request->image);
            } else {
                $result = $this->attendanceService->scanFacePresenceAuto(
                    $request->image,
                    $request->latitude ? floatval($request->latitude) : null,
                    $request->longitude ? floatval($request->longitude) : null
                );
            }

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
     * AJAX POST endpoint untuk pengajuan izin mandiri siswa (sakit/izin).
     */
    public function submitIzin(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer|exists:students,id',
            'status' => 'required|string|in:sakit,izin',
            'notes' => 'required|string|max:500',
            'attachment' => 'nullable|image|max:2048' // max 2MB image
        ]);

        try {
            $result = $this->attendanceService->submitStudentLeave(
                intval($request->student_id),
                $request->status,
                $request->notes,
                $request->file('attachment')
            );

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
