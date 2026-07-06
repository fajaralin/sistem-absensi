<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    /**
     * Properti yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'nisn',
        'department',
        'class_name',
        'phone',
        'parent_email',
        'photo_path',
        'status',
    ];

    /**
     * Hubungan Belongs-to dengan model User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Hubungan One-to-Many dengan model FaceData (wajah master)
     */
    public function faceData()
    {
        return $this->hasMany(FaceData::class);
    }

    /**
     * Hubungan One-to-Many dengan model Attendance (presensi)
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
