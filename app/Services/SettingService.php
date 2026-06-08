<?php

namespace App\Services;

use App\Repositories\Contracts\SettingRepositoryInterface;

class SettingService
{
    protected $settingRepo;

    /**
     * SettingService constructor.
     */
    public function __construct(SettingRepositoryInterface $settingRepo)
    {
        $this->settingRepo = $settingRepo;
    }

    /**
     * Dapatkan satu nilai pengaturan berdasarkan key.
     */
    public function get(string $key, $default = null)
    {
        return $this->settingRepo->get($key, $default);
    }

    /**
     * Dapatkan seluruh pengaturan sebagai array asosiatif [key => value].
     */
    public function getAll(): array
    {
        return $this->settingRepo->all();
    }

    /**
     * Perbarui banyak pengaturan sekaligus dari hasil form admin.
     */
    public function updateSettings(array $values): void
    {
        $this->settingRepo->setMany($values);
    }

    /**
     * Dapatkan jam mulai presensi (default 07:00 jika belum diatur).
     */
    public function getAttendanceStartTime(): string
    {
        return $this->settingRepo->get('attendance_start_time', '07:00');
    }

    /**
     * Dapatkan batas jam sebelum siswa dianggap telat (default 07:30 jika belum diatur).
     */
    public function getLateThreshold(): string
    {
        return $this->settingRepo->get('attendance_late_threshold', '07:30');
    }

    /**
     * Tentukan status kehadiran ('hadir' atau 'telat') berdasarkan jam check-in dibandingkan batas telat.
     */
    public function resolveAttendanceStatus(string $checkInTime): string
    {
        $threshold = $this->getLateThreshold();

        return $checkInTime > $threshold ? 'telat' : 'hadir';
    }

    /**
     * Dapatkan informasi identitas sekolah untuk kop surat laporan PDF.
     */
    public function getSchoolInfo(): array
    {
        $settings = $this->getAll();

        return [
            'name' => $settings['school_name'] ?? 'SMA Negeri 1 Utama',
            'address' => $settings['school_address'] ?? '-',
            'phone' => $settings['school_phone'] ?? '-',
            'email' => $settings['school_email'] ?? '-',
            'principal_name' => $settings['principal_name'] ?? '-',
            'principal_nip' => $settings['principal_nip'] ?? '-',
        ];
    }
}
