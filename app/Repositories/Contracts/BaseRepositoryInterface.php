<?php

namespace App\Repositories\Contracts;

interface BaseRepositoryInterface
{
    /**
     * Dapatkan semua data.
     */
    public function all();

    /**
     * Temukan data berdasarkan ID.
     */
    public function find($id);

    /**
     * Buat data baru.
     */
    public function create(array $data);

    /**
     * Perbarui data berdasarkan ID.
     */
    public function update($id, array $data);

    /**
     * Hapus data berdasarkan ID.
     */
    public function delete($id);
}
