<?php

namespace App\Services;

use App\Repositories\Contracts\StudentRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\FaceDataRepositoryInterface;
use App\Repositories\Contracts\LogRepositoryInterface;
use App\Services\Contracts\FaceRecognitionServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentService
{
    protected $studentRepo;
    protected $userRepo;
    protected $faceRepo;
    protected $logRepo;
    protected $faceRecognitionService;

    /**
     * StudentService constructor.
     */
    public function __construct(
        StudentRepositoryInterface $studentRepo,
        UserRepositoryInterface $userRepo,
        FaceDataRepositoryInterface $faceRepo,
        LogRepositoryInterface $logRepo,
        FaceRecognitionServiceInterface $faceRecognitionService
    ) {
        $this->studentRepo = $studentRepo;
        $this->userRepo = $userRepo;
        $this->faceRepo = $faceRepo;
        $this->logRepo = $logRepo;
        $this->faceRecognitionService = $faceRecognitionService;
    }

    /**
     * Dapatkan semua data mahasiswa dengan user.
     */
    public function getAllStudents()
    {
        return $this->studentRepo->allWithUser();
    }

    /**
     * Dapatkan detail mahasiswa.
     */
    public function getStudentById(int $id)
    {
        return $this->studentRepo->findWithDetails($id);
    }

    /**
     * Buat mahasiswa baru beserta akun user dan register wajah.
     */
    public function createStudent(array $data, ?string $masterFaceBase64 = null)
    {
        return DB::transaction(function () use ($data, $masterFaceBase64) {
            // 1. Buat akun user
            $user = $this->userRepo->create([
                'name' => $data['name'],
                'email' => $data['email'] ?? ($data['nisn'] . '@siswa.school.id'),
                'password' => Hash::make($data['password'] ?? 'password123'),
                'role' => 'student'
            ]);

            // 2. Upload foto profil jika ada
            $photoPath = null;
            if (isset($data['photo']) && $data['photo']->isValid()) {
                $photoPath = $data['photo']->store('uploads/profiles', 'public');
            }

            // 3. Buat profil mahasiswa
            $student = $this->studentRepo->create([
                'user_id' => $user->id,
                'nisn' => $data['nisn'],
                'department' => $data['department'],
                'class_name' => $data['class_name'],
                'phone' => $data['phone'] ?? null,
                'photo_path' => $photoPath,
                'status' => 'active'
            ]);

            // 4. Jika ada input capture base64 wajah master
            if ($masterFaceBase64) {
                $this->registerStudentFace($student, $masterFaceBase64);
            }

            $this->logRepo->logActivity(
                auth()->id(), 
                'create_student', 
                "Berhasil mendaftarkan siswa NISN {$student->nisn} (" . $user->name . ")"
            );

            return $student;
        });
    }

    /**
     * Perbarui profil mahasiswa.
     */
    public function updateStudent(int $id, array $data, ?string $masterFaceBase64 = null)
    {
        return DB::transaction(function () use ($id, $data, $masterFaceBase64) {
            $student = $this->studentRepo->find($id);
            if (!$student) {
                throw new \Exception('Siswa tidak ditemukan');
            }

            // Update user
            $this->userRepo->update($student->user_id, [
                'name' => $data['name'],
                'email' => $data['email'] ?? $student->user->email,
            ]);

            $updateData = [
                'department' => $data['department'],
                'class_name' => $data['class_name'],
                'phone' => $data['phone'] ?? null,
                'status' => $data['status'] ?? 'active'
            ];

            // Upload foto baru jika ada
            if (isset($data['photo']) && $data['photo']->isValid()) {
                // Hapus foto lama jika ada
                if ($student->photo_path) {
                    Storage::disk('public')->delete($student->photo_path);
                }
                $updateData['photo_path'] = $data['photo']->store('uploads/profiles', 'public');
            }

            $student = $this->studentRepo->update($id, $updateData);

            // Jika ada perubahan/penambahan wajah master baru
            if ($masterFaceBase64) {
                $this->registerStudentFace($student, $masterFaceBase64);
            }

            $this->logRepo->logActivity(
                auth()->id(), 
                'update_student', 
                "Berhasil memperbarui biodata siswa NISN {$student->nisn}"
            );

            return $student;
        });
    }

    /**
     * Hapus mahasiswa beserta akun user-nya.
     */
    public function deleteStudent(int $id)
    {
        return DB::transaction(function () use ($id) {
            $student = $this->studentRepo->find($id);
            if (!$student) {
                return false;
            }

            $userId = $student->user_id;
            $nisn = $student->nisn;

            // Hapus file foto profil & data wajah master dari storage
            if ($student->photo_path) {
                Storage::disk('public')->delete($student->photo_path);
            }

            $faces = $this->faceRepo->findByStudentId($id);
            foreach ($faces as $face) {
                Storage::disk('public')->delete($face->image_path);
            }

            // Hapus mahasiswa (cascade ke face_data & attendance karena constraint FK)
            $this->studentRepo->delete($id);

            // Hapus user jika ada
            if ($userId) {
                $this->userRepo->delete($userId);
            }

            $this->logRepo->logActivity(
                auth()->id(), 
                'delete_student', 
                "Berhasil menghapus siswa NISN {$nisn}"
            );

            return true;
        });
    }

    /**
     * Logika untuk menyimpan file wajah master secara lokal dan mendaftarkannya ke Python API
     */
    public function registerStudentFace($student, string $imageBase64)
    {
        // 1. Simpan foto wajah base64 ke local disk storage
        if (preg_match('/^data:image\/(\w+);base64,/', $imageBase64, $type)) {
            $data = substr($imageBase64, strpos($imageBase64, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, dll
        } else {
            $data = $imageBase64;
            $type = 'jpg';
        }

        $data = base64_decode($data);
        $fileName = 'faces/' . $student->nisn . '_' . time() . '.' . $type;
        Storage::disk('public')->put($fileName, $data);

        // 2. Set wajah master lama menjadi inactive
        $existingFaces = $this->faceRepo->findByStudentId($student->id);
        foreach ($existingFaces as $existFace) {
            $this->faceRepo->update($existFace->id, ['status' => 'inactive']);
        }

        // 3. Simpan data wajah master baru ke tabel face_data
        $faceRecord = $this->faceRepo->create([
            'student_id' => $student->id,
            'image_path' => $fileName,
            'status' => 'active'
        ]);

        // 4. Sinkronisasikan dengan Python AI Engine
        $pythonResult = $this->faceRecognitionService->registerFace($student->nisn, $imageBase64);

        if (!$pythonResult['success']) {
            // Log warning tetapi simpan di Laravel agar bisa di-retry nanti jika Python mati
            \Illuminate\Support\Facades\Log::warning("Face registration for student NISN {$student->nisn} failed on Python engine. Offline simulation might be active.");
        }

        $this->logRepo->logActivity(
            auth()->id(), 
            'register_face', 
            "Mendaftarkan wajah master siswa NISN {$student->nisn}. Python sync status: " . ($pythonResult['success'] ? 'SUCCESS' : 'OFFLINE/FAILED')
        );

        return $faceRecord;
    }
}
