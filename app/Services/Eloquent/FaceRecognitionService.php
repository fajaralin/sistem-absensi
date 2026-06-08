<?php

namespace App\Services\Eloquent;

use App\Services\Contracts\FaceRecognitionServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FaceRecognitionService implements FaceRecognitionServiceInterface
{
    protected $apiUrl;

    /**
     * FaceRecognitionService constructor.
     */
    public function __construct()
    {
        $this->apiUrl = env('PYTHON_API_URL', 'http://127.0.0.1:5000');
    }

    /**
     * Kirim gambar wajah (base64) ke Python AI Engine untuk di-recognize.
     */
    public function recognize(string $imageBase64, ?string $nisn = null): array
    {
        try {
            // Bersihkan format data URI prefix jika ada (misal: "data:image/jpeg;base64,")
            if (preg_match('/^data:image\/(\w+);base64,/', $imageBase64, $type)) {
                $imageBase64 = substr($imageBase64, strpos($imageBase64, ',') + 1);
            }

            $payload = [
                'image' => $imageBase64
            ];
            if ($nisn) {
                $payload['nisn'] = $nisn;
            }

            $response = Http::timeout(8)
                ->post("{$this->apiUrl}/recognize", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'message' => 'Python API Server error (' . $response->status() . ')',
                'confidence' => 0.0
            ];

        } catch (\Exception $e) {
            Log::error("FaceRecognitionService recognize failed: " . $e->getMessage());

            // Fallback simulasi jika python server mati (sangat membantu saat presentasi offline)
            return [
                'success' => false,
                'message' => 'Python API tidak aktif (Koneksi terputus: ' . $e->getMessage() . ')',
                'confidence' => 0.0,
                'offline' => true
            ];
        }
    }

    /**
     * Kirim gambar wajah (base64) ke Python AI Engine untuk diidentifikasi 1-to-many
     * (tanpa NISN/nama — Python akan mencocokkan terhadap seluruh wajah master terdaftar).
     */
    public function identify(string $imageBase64): array
    {
        try {
            if (preg_match('/^data:image\/(\w+);base64,/', $imageBase64, $type)) {
                $imageBase64 = substr($imageBase64, strpos($imageBase64, ',') + 1);
            }

            $response = Http::timeout(8)
                ->post("{$this->apiUrl}/identify", [
                    'image' => $imageBase64
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'message' => 'Python API Server error (' . $response->status() . ')',
                'confidence' => 0.0
            ];

        } catch (\Exception $e) {
            Log::error("FaceRecognitionService identify failed: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Python API tidak aktif (Koneksi terputus: ' . $e->getMessage() . ')',
                'confidence' => 0.0,
                'offline' => true
            ];
        }
    }

    /**
     * Kirim data wajah (base64) ke Python AI Engine untuk didaftarkan sebagai master face.
     */
    public function registerFace(string $nisn, string $imageBase64): array
    {
        try {
            if (preg_match('/^data:image\/(\w+);base64,/', $imageBase64, $type)) {
                $imageBase64 = substr($imageBase64, strpos($imageBase64, ',') + 1);
            }

            $response = Http::timeout(8)
                ->post("{$this->apiUrl}/register-face", [
                    'nisn' => $nisn,
                    'image' => $imageBase64
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'message' => 'Gagal mendaftarkan wajah di Python AI Server'
            ];

        } catch (\Exception $e) {
            Log::error("FaceRecognitionService registerFace failed: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Python API tidak aktif (' . $e->getMessage() . ')',
                'offline' => true
            ];
        }
    }
}
