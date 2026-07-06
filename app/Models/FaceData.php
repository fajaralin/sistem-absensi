<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaceData extends Model
{
    use HasFactory;

    protected $table = 'face_data';

    /**
     * Properti yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'student_id',
        'image_path',
        'face_embedding',
        'status',
    ];

    /**
     * Hubungan Belongs-to dengan model Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
