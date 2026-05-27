<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StudentService;
use App\Repositories\Contracts\FaceDataRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentCRUDController extends Controller
{
    protected $studentService;
    protected $faceRepo;

    /**
     * StudentCRUDController constructor.
     */
    public function __construct(
        StudentService $studentService,
        FaceDataRepositoryInterface $faceRepo
    ) {
        $this->studentService = $studentService;
        $this->faceRepo = $faceRepo;
    }

    /**
     * Tampilkan daftar mahasiswa.
     */
    public function index()
    {
        $students = $this->studentService->getAllStudents();
        return view('admin.students.index', compact('students'));
    }

    /**
     * Tampilkan form tambah mahasiswa.
     */
    public function create()
    {
        return view('admin.students.create');
    }

    /**
     * Simpan data mahasiswa baru.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'nisn' => 'required|string|max:50|unique:students',
            'department' => 'required|string|max:100',
            'class_name' => 'required|string|max:50',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password' => 'nullable|string|min:6',
            'face_image' => 'nullable|string' // base64 string master wajah
        ];

        $validated = $request->validate($rules);

        $student = $this->studentService->createStudent($validated, $request->face_image);

        return redirect()->route('admin.students.index')
            ->with('success', "Siswa {$student->user->name} berhasil ditambahkan!");
    }

    /**
     * Tampilkan halaman edit mahasiswa.
     */
    public function edit(int $id)
    {
        $student = $this->studentService->getStudentById($id);
        if (!$student) {
            return redirect()->route('admin.students.index')->with('error', 'Siswa tidak ditemukan');
        }
        return view('admin.students.edit', compact('student'));
    }

    /**
     * Simpan perubahan data mahasiswa.
     */
    public function update(Request $request, int $id)
    {
        $student = $this->studentService->getStudentById($id);
        if (!$student) {
            return redirect()->route('admin.students.index')->with('error', 'Siswa tidak ditemukan');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($student->user_id)
            ],
            'department' => 'required|string|max:100',
            'class_name' => 'required|string|max:50',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:active,inactive',
            'face_image' => 'nullable|string' // base64 string master wajah baru
        ];

        $validated = $request->validate($rules);

        $this->studentService->updateStudent($id, $validated, $request->face_image);

        return redirect()->route('admin.students.index')
            ->with('success', 'Biodata siswa berhasil diperbarui!');
    }

    /**
     * Hapus mahasiswa.
     */
    public function destroy(int $id)
    {
        $result = $this->studentService->deleteStudent($id);

        if ($result) {
            return redirect()->route('admin.students.index')
                ->with('success', 'Siswa dan seluruh data terkait berhasil dihapus!');
        }

        return redirect()->route('admin.students.index')
            ->with('error', 'Gagal menghapus siswa. Silakan coba lagi.');
    }

    /**
     * AJAX Endpoint untuk mendaftarkan wajah secara langsung
     */
    public function registerFace(Request $request, int $id)
    {
        $request->validate([
            'image' => 'required|string' // base64 image
        ]);

        $student = $this->studentService->getStudentById($id);
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Siswa tidak ditemukan.'], 404);
        }

        try {
            $faceRecord = $this->studentService->registerStudentFace($student, $request->image);
            return response()->json([
                'success' => true,
                'message' => 'Wajah master berhasil didaftarkan!',
                'data' => [
                    'image_url' => asset('storage/' . $faceRecord->image_path)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendaftarkan wajah: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Daftar seluruh data wajah master (untuk menu data wajah master admin).
     */
    public function faceDataList()
    {
        $faceRecords = $this->faceRepo->all();
        // Eager load mahasiswa dan user
        $faceRecords->load('student.user');

        return view('admin.face-data.index', compact('faceRecords'));
    }

    /**
     * Hapus data wajah master tertentu.
     */
    public function deleteFaceData(int $id)
    {
        $face = $this->faceRepo->find($id);
        if ($face) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($face->image_path);
            $this->faceRepo->delete($id);
            return redirect()->route('admin.face-data.index')
                ->with('success', 'Wajah master berhasil dihapus.');
        }

        return redirect()->route('admin.face-data.index')
            ->with('error', 'Data wajah tidak ditemukan.');
    }
}
