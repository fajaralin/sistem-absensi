<?php

namespace App\Repositories\Contracts;

interface AttendanceRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Cari presensi mahasiswa berdasarkan student_id dan tanggal tertentu.
     */
    public function findByStudentAndDate(int $studentId, string $date);

    /**
     * Dapatkan semua presensi hari ini.
     */
    public function getTodayAttendances();

    /**
     * Dapatkan daftar presensi dengan berbagai filter (tanggal, NIM/Nama, status).
     */
    public function getWithFilters(array $filters);

    /**
     * Dapatkan riwayat kehadiran seorang mahasiswa tertentu.
     */
    public function getStudentHistory(int $studentId);

    /**
     * Hitung statistik kehadiran hari ini (Hadir, Sakit, Izin, Alpha).
     */
    public function getTodayStats();
}
