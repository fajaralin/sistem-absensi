<?php

namespace App\Repositories\Eloquent;

use App\Models\Setting;
use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class SettingRepository implements SettingRepositoryInterface
{
    protected $model;

    /**
     * SettingRepository constructor.
     */
    public function __construct(Setting $model)
    {
        $this->model = $model;
    }

    /**
     * Dapatkan satu nilai pengaturan berdasarkan key (di-cache agar query DB minim).
     */
    public function get(string $key, $default = null)
    {
        return $this->all()[$key] ?? $default;
    }

    /**
     * Dapatkan seluruh pengaturan sebagai array asosiatif [key => value].
     */
    public function all()
    {
        return Cache::rememberForever('app_settings', function () {
            return $this->model->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Simpan/perbarui satu nilai pengaturan berdasarkan key.
     */
    public function set(string $key, $value)
    {
        $setting = $this->model->updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('app_settings');

        return $setting;
    }

    /**
     * Simpan/perbarui banyak pengaturan sekaligus dari array asosiatif [key => value].
     */
    public function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            $this->model->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget('app_settings');
    }
}
