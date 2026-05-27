<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Admin
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@presensi.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'admin'
        ]);

        // 2. Seed Students (Siswa)
        $userBudi = User::create([
            'name' => 'Budi Handoko',
            'email' => 'budi@student.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'student'
        ]);

        $studentBudi = \App\Models\Student::create([
            'user_id' => $userBudi->id,
            'nisn' => '0089123456',
            'department' => 'MIPA',
            'class_name' => 'XI-MIPA-2',
            'phone' => '081234567890',
            'status' => 'active'
        ]);

        $userSiti = User::create([
            'name' => 'Siti Aminah',
            'email' => 'siti@student.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'student'
        ]);

        $studentSiti = \App\Models\Student::create([
            'user_id' => $userSiti->id,
            'nisn' => '0089123457',
            'department' => 'IPS',
            'class_name' => 'XI-IPS-1',
            'phone' => '089876543210',
            'status' => 'active'
        ]);

        // 3. Seed Master Face Records (References Only)
        \App\Models\FaceData::create([
            'student_id' => $studentBudi->id,
            'image_path' => 'faces/0089123456_seeder.jpg',
            'status' => 'active'
        ]);

        \App\Models\FaceData::create([
            'student_id' => $studentSiti->id,
            'image_path' => 'faces/0089123457_seeder.jpg',
            'status' => 'active'
        ]);

        // 4. Seed Attendance History (Yesterday & Today)
        $yesterday = \Carbon\Carbon::yesterday();

        // Kemarin Budi Hadir, Siti Hadir
        \App\Models\Attendance::create([
            'student_id' => $studentBudi->id,
            'date' => $yesterday->toDateString(),
            'check_in' => '07:45:12',
            'status' => 'hadir',
            'confidence' => 0.94,
            'method' => 'face_recognition'
        ]);

        \App\Models\Attendance::create([
            'student_id' => $studentSiti->id,
            'date' => $yesterday->toDateString(),
            'check_in' => '08:12:45',
            'status' => 'hadir',
            'confidence' => 0.89,
            'method' => 'face_recognition'
        ]);

        // Hari Ini Budi Hadir (untuk contoh data hari ini), Siti belum scan
        \App\Models\Attendance::create([
            'student_id' => $studentBudi->id,
            'date' => today()->toDateString(),
            'check_in' => '07:52:30',
            'status' => 'hadir',
            'confidence' => 0.95,
            'face_image_path' => 'attendance_snaps/0089123456_demo.jpg',
            'method' => 'face_recognition'
        ]);

        // 5. Seed Activity Logs
        \App\Models\Log::create([
            'user_id' => $admin->id,
            'action' => 'seed_system',
            'details' => 'Menginisialisasi basis data sistem presensi bawaan.',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Seeder'
        ]);

        \App\Models\Log::create([
            'user_id' => $userBudi->id,
            'action' => 'scan_face_success',
            'details' => 'Presensi berhasil dengan face recognition (Confidence: 95.0%)',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Chrome Windows'
        ]);
    }
}
