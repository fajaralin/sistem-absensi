<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan nilai 'telat' pada kolom enum status, untuk menandai siswa
     * yang melakukan presensi setelah melewati batas jam yang dikonfigurasi di Pengaturan.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE attendances MODIFY COLUMN status ENUM('hadir', 'telat', 'sakit', 'izin', 'alpha') NOT NULL DEFAULT 'alpha'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE attendances SET status = 'hadir' WHERE status = 'telat'");
            DB::statement("ALTER TABLE attendances MODIFY COLUMN status ENUM('hadir', 'sakit', 'izin', 'alpha') NOT NULL DEFAULT 'alpha'");
        }
    }
};
