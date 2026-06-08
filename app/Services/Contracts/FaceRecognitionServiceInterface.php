<?php

namespace App\Services\Contracts;

interface FaceRecognitionServiceInterface
{
    /**
     * Kirim gambar wajah (base64) ke Python AI Engine untuk di-recognize.
     */
    public function recognize(string $imageBase64, ?string $nisn = null): array;

    /**
     * Kirim gambar wajah (base64) ke Python AI Engine untuk diidentifikasi secara 1-to-many
     * (tanpa NISN/nama, sistem mencari sendiri siswa mana yang paling cocok).
     */
    public function identify(string $imageBase64): array;

    /**
     * Kirim data wajah (base64/path) ke Python AI Engine untuk didaftarkan sebagai master face.
     */
    public function registerFace(string $nisn, string $imageBase64): array;
}
