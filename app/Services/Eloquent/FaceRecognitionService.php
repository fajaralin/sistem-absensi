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
        $this->apiUrl = env('PYTHON_API_URL', 'http://127.0.0.1:5001');
    }

    /**
     * Pastikan server python berjalan. Jika tidak, jalankan di background secara otomatis.
     */
    protected function ensureServerIsRunning()
    {
        $port = parse_url($this->apiUrl, PHP_URL_PORT) ?: 5001;
        $host = parse_url($this->apiUrl, PHP_URL_HOST) ?: '127.0.0.1';

        // Coba koneksi soket cepat untuk melihat apakah port terbuka
        $connection = @fsockopen($host, $port, $errno, $errstr, 0.5);
        if (is_resource($connection)) {
            fclose($connection);
            return; // Server sudah aktif
        }

        // Jalankan server python di background dengan executable yang tepat
        $pythonCmd = $this->getPythonCommand();
        $scriptPath = base_path('python_server.py');
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Jalankan di Windows (membuka window konsol baru agar persisten dan log terlihat)
            pclose(popen("start \"\" {$pythonCmd} \"{$scriptPath}\"", "r"));
        } else {
            // Jalankan background di Linux/macOS
            exec("python \"{$scriptPath}\" > /dev/null 2>&1 &");
        }

        // Tunggu hingga server flask aktif (maksimal 5 detik)
        for ($i = 0; $i < 10; $i++) {
            $connection = @fsockopen($host, $port, $errno, $errstr, 0.2);
            if (is_resource($connection)) {
                fclose($connection);
                return; // Server sudah aktif dan siap menerima request
            }
            usleep(500000); // 0.5 detik
        }
    }

    /**
     * Temukan path executable Python yang tersedia di sistem.
     */
    protected function getPythonCommand(): string
    {
        // 1. Cek apakah global command 'python' berfungsi
        $checkGlobal = @shell_exec("python --version");
        if ($checkGlobal && str_contains(strtolower($checkGlobal), 'python')) {
            return 'python';
        }

        // 2. Cek Laragon Python default path
        $laragonPath = 'C:\\laragon\\bin\\python\\python-3.13\\python.exe';
        if (file_exists($laragonPath)) {
            return "\"{$laragonPath}\"";
        }

        // 3. Cek folder python lain di Laragon (jika versi berbeda, misal python-3.10)
        if (file_exists('C:\\laragon\\bin\\python')) {
            $dirs = glob('C:\\laragon\\bin\\python\\python-*', GLOB_ONLYDIR);
            if (!empty($dirs)) {
                rsort($dirs); // Urutkan dari versi tertinggi
                $exePath = $dirs[0] . '\\python.exe';
                if (file_exists($exePath)) {
                    return "\"{$exePath}\"";
                }
            }
        }

        // Default fallback ke global command
        return 'python';
    }

    /**
     * Kirim gambar wajah (base64) ke Python AI Engine untuk di-recognize.
     */
    public function recognize(string $imageBase64, ?string $nisn = null): array
    {
        $this->ensureServerIsRunning();

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
        $this->ensureServerIsRunning();

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
        $this->ensureServerIsRunning();

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

    /**
     * Pastikan server python berjalan di background.
     */
    public function initializeServer(): void
    {
        $this->ensureServerIsRunning();
    }
}
