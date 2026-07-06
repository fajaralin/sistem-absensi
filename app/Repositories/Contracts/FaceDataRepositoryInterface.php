<?php

namespace App\Repositories\Contracts;

interface FaceDataRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Dapatkan semua data wajah master milik mahasiswa tertentu.
     */
    public function findByStudentId(int $studentId);

    /**
     * Dapatkan data wajah master yang aktif milik mahasiswa tertentu.
     */
    public function getActiveFaceByStudent(int $studentId);
}
