<?php

namespace App\Repositories\Contracts;

interface SettingRepositoryInterface
{
    /**
     * Dapatkan satu nilai pengaturan berdasarkan key.
     */
    public function get(string $key, $default = null);

    /**
     * Dapatkan seluruh pengaturan sebagai array asosiatif [key => value].
     */
    public function all();

    /**
     * Simpan/perbarui satu nilai pengaturan berdasarkan key.
     */
    public function set(string $key, $value);

    /**
     * Simpan/perbarui banyak pengaturan sekaligus dari array asosiatif [key => value].
     */
    public function setMany(array $values): void;
}
