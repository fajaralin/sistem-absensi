<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Nilai default agar sistem tetap berjalan normal sebelum admin mengubahnya
        $defaults = [
            'attendance_start_time' => '07:00',
            'attendance_late_threshold' => '07:30',
            'school_name' => 'SMA Negeri 1 Utama',
            'school_address' => 'Jl. Raya Utama No. 123, Jakarta Selatan',
            'school_phone' => '(021) 555-1234',
            'school_email' => 'info@sman1utama.sch.id',
            'principal_name' => 'Drs. H. Mulyadi, M.Pd.',
            'principal_nip' => '19680515 199303 1 003',
        ];

        $now = now();
        foreach ($defaults as $key => $value) {
            DB::table('settings')->insert([
                'key' => $key,
                'value' => $value,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
