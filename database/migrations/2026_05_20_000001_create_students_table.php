<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('nisn')->unique(); // Nomor Induk Siswa Nasional
            $table->string('department');    // Jurusan/Prodi
            $table->string('class_name');    // Kelas (misal: IF-4A)
            $table->string('phone')->nullable();
            $table->string('photo_path')->nullable(); // Foto profil mahasiswa
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
