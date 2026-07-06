<?php

namespace App\Repositories\Eloquent;

use App\Models\Student;
use App\Repositories\Contracts\StudentRepositoryInterface;

class StudentRepository extends BaseRepository implements StudentRepositoryInterface
{
    /**
     * StudentRepository constructor.
     */
    public function __construct(Student $model)
    {
        parent::__construct($model);
    }

    /**
     * Temukan siswa berdasarkan NISN.
     */
    public function findByNisn(string $nisn)
    {
        return $this->model->where('nisn', $nisn)->first();
    }

    /**
     * Dapatkan semua mahasiswa dengan data user-nya.
     */
    public function allWithUser()
    {
        return $this->model->with('user')->orderBy('created_at', 'desc')->get();
    }

    /**
     * Dapatkan mahasiswa dengan detail user dan wajah master.
     */
    public function findWithDetails(int $id)
    {
        return $this->model->with(['user', 'faceData'])->find($id);
    }

    /**
     * Cari mahasiswa berdasarkan nama untuk autocomplete.
     */
    public function searchByName(string $nameQuery, int $limit = 5)
    {
        return $this->model->whereHas('user', function ($q) use ($nameQuery) {
            $q->where('name', 'like', '%' . $nameQuery . '%');
        })->with('user')->limit($limit)->get();
    }
}
