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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alpha'])->default('alpha');
            $table->float('confidence')->nullable(); // Confidence score (0.0 - 1.0) dari face recognition
            $table->string('face_image_path')->nullable(); // Snap webcam saat presensi
            $table->enum('method', ['face_recognition', 'manual'])->default('face_recognition');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Kombinasi student_id dan date harus unik agar tidak double presensi di hari yang sama
            $table->unique(['student_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
