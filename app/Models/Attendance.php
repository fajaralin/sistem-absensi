<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    /**
     * Properti yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'student_id',
        'date',
        'check_in',
        'status',
        'confidence',
        'face_image_path',
        'method',
        'notes',
    ];

    /**
     * Cast untuk tipe data tertentu.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
        'confidence' => 'float',
    ];

    /**
     * Hubungan Belongs-to dengan model Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Scope untuk memfilter presensi hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    /**
     * Scope untuk memfilter presensi berdasarkan tanggal tertentu
     */
    public function scopeDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }
}
