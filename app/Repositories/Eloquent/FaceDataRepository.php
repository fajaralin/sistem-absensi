<?php

namespace App\Repositories\Eloquent;

use App\Models\FaceData;
use App\Repositories\Contracts\FaceDataRepositoryInterface;

class FaceDataRepository extends BaseRepository implements FaceDataRepositoryInterface
{
    /**
     * FaceDataRepository constructor.
     */
    public function __construct(FaceData $model)
    {
        parent::__construct($model);
    }

    /**
     * Dapatkan semua data wajah master milik mahasiswa tertentu.
     */
    public function findByStudentId(int $studentId)
    {
        return $this->model->where('student_id', $studentId)->get();
    }

    /**
     * Dapatkan data wajah master yang aktif milik mahasiswa tertentu.
     */
    public function getActiveFaceByStudent(int $studentId)
    {
        return $this->model->where('student_id', $studentId)
                           ->where('status', 'active')
                           ->first();
    }
}
