<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    /**
     * BaseRepository constructor.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Dapatkan semua data.
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Temukan data berdasarkan ID.
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * Buat data baru.
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Perbarui data berdasarkan ID.
     */
    public function update($id, array $data)
    {
        $record = $this->find($id);
        if ($record) {
            $record->update($data);
            return $record;
        }
        return null;
    }

    /**
     * Hapus data berdasarkan ID.
     */
    public function delete($id)
    {
        $record = $this->find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }
}
