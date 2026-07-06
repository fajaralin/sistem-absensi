<?php

namespace App\Repositories\Contracts;

interface StudentRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Temukan siswa berdasarkan NISN.
     */
    public function findByNisn(string $nisn);

    /**
     * Dapatkan semua mahasiswa dengan data user-nya.
     */
    public function allWithUser();

    /**
     * Dapatkan mahasiswa dengan detail user dan wajah master.
     */
    public function findWithDetails(int $id);

    /**
     * Cari mahasiswa berdasarkan nama untuk autocomplete.
     */
    public function searchByName(string $nameQuery, int $limit = 5);
}
