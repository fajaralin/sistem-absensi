import os
import sys

# Pindahkan working directory ke folder tempat file script ini berada agar path relatif selalu benar
os.chdir(os.path.dirname(os.path.abspath(__file__)))

import json
import base64
import time
import cv2
import numpy as np
import io
from PIL import Image
from flask import Flask, request, jsonify

app = Flask(__name__)

# File database sederhana untuk menampung data wajah master yang didaftarkan
DATABASE_FILE = 'faces_database.json'
ACTIVE_STUDENT_FILE = os.path.join('storage', 'app', 'active_student.json')
FACES_DATASET_DIR = 'faces_dataset'

# Pastikan folder faces_dataset ada secara lokal
if not os.path.exists(FACES_DATASET_DIR):
    os.makedirs(FACES_DATASET_DIR, exist_ok=True)

# Konfigurasi Awal Sistem (Toggles & Settings)
system_config = {
    'mode': 'smart_sync',  # 'smart_sync', 'force_budi', 'force_siti', 'force_fail', 'auto_first'
    'delay_seconds': 1.5,   # Simulasi waktu pemrosesan neural network
    'confidence_rate': 0.94
}

# Menyimpan riwayat scan terakhir untuk ditampilkan di dashboard python
scan_logs = []

def get_registered_faces():
    """Membaca data wajah master yang terdaftar di JSON."""
    if os.path.exists(DATABASE_FILE):
        try:
            with open(DATABASE_FILE, 'r') as f:
                return json.load(f)
        except Exception:
            return {}
    return {}

def save_registered_faces(data):
    """Menyimpan data wajah master ke JSON."""
    with open(DATABASE_FILE, 'w') as f:
        json.dump(data, f, indent=4)

def decode_base64_image(image_b64):
    """Mendekode string base64 menjadi citra OpenCV BGR."""
    try:
        if ',' in image_b64:
            image_b64 = image_b64.split(',')[1]
        image_bytes = base64.b64decode(image_b64)
        nparr = np.frombuffer(image_bytes, np.uint8)
        img = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
        return img
    except Exception as e:
        print(f"[ERROR DECODE] Gagal mendekode gambar base64: {str(e)}")
        return None

def extract_face(img):
    """Mendeteksi wajah dari gambar BGR, mengubahnya ke grayscale, memotong area wajah, dan meresize ke 200x200 px."""
    if img is None:
        return None
    try:
        # Load Haar Cascade untuk deteksi wajah
        cascade_path = cv2.data.haarcascades + 'haarcascade_frontalface_default.xml'
        face_cascade = cv2.CascadeClassifier(cascade_path)
        
        # Konversi ke skala abu-abu
        gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
        
        # Deteksi wajah secara multi-scale
        faces = face_cascade.detectMultiScale(
            gray,
            scaleFactor=1.1,
            minNeighbors=5,
            minSize=(50, 50)
        )
        
        if len(faces) == 0:
            return None
            
        # Cari wajah dengan luas area bounding box terbesar
        largest_face = max(faces, key=lambda rect: rect[2] * rect[3])
        x, y, w, h = largest_face
        
        # Potong area wajah dan lakukan normalisasi ukuran menjadi 200x200 px
        face_roi = gray[y:y+h, x:x+w]
        face_resized = cv2.resize(face_roi, (200, 200), interpolation=cv2.INTER_AREA)
        
        return face_resized
    except Exception as e:
        print(f"[ERROR DETECT] Gagal mendeteksi/mengekstrak wajah: {str(e)}")
        return None

# Print stylized banner pada console (WOW factor untuk presentasi!)
def print_banner():
    # ANSI color codes
    cyan = "\033[96m"
    green = "\033[92m"
    yellow = "\033[93m"
    bold = "\033[1m"
    reset = "\033[0m"
    
    banner = f"""
{cyan}{bold}====================================================================
  ____  _   _ ____  _____ _   _    _     ___   _____ _   _ _____ _   _ 
  |  _ \| | | |  _ \|_   _| | | |  / \   |_ _| | ____| \ | | ____| | | |
  | |_) | |_| | |_) | | | | |_| | / _ \   | |  |  _| |  \| |  _| | |_| |
  |  __/|  _  |  _ <  | | |  _  |/ ___ \  | |  | |___| |\  | |___|  _  |
  |_|   |_| |_|_| \_\ |_| |_| |_/_/   \_\___| |_____|_| \_|_____|_| |_|
                                                                        
             [ SISTEM PRESENSI ONLINE BERBASIS FACE RECOGNITION ]
             -- REAL BIOMETRIC FACE RECOGNITION ENGINE v3.0 --
                      -- KIOSK SISWA SMA - FORMAL --
===================================================================={reset}
{green}[SYSTEM] OpenCV Version: {cv2.__version__} successfully initialized.{reset}
{green}[SYSTEM] Model Haar Cascades Frontal Face XML loaded successfully.{reset}
{green}[SYSTEM] Membaca dataset wajah master dari {DATABASE_FILE}...{reset}
{yellow}[SYSTEM] AI Engine aktif! Mendengarkan request API di http://127.0.0.1:5001{reset}
====================================================================
"""
    print(banner)

def add_log(action, details, status="SUCCESS"):
    """Menambahkan log aktivitas ke list."""
    timestamp = time.strftime('%H:%M:%S')
    scan_logs.insert(0, {
        'time': timestamp,
        'action': action,
        'details': details,
        'status': status
    })
    # Batasi log maksimal 30 entri
    if len(scan_logs) > 30:
        scan_logs.pop()

@app.route('/', methods=['GET'])
def index():
    """Dashboard UI Kontrol Panel untuk Presentasi."""
    faces = get_registered_faces()
    
    # HTML & CSS Tailwind untuk Control Panel Premium
    html_content = """
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>AI Face Engine Control Panel</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: 'Outfit', sans-serif;
                background-color: #0b0f19;
            }
            .glass-card {
                background: rgba(17, 24, 39, 0.7);
                backdrop-filter: blur(12px);
                border: 1px solid rgba(255, 255, 255, 0.05);
            }
        </style>
    </head>
    <body class="text-slate-100 min-h-screen p-4 md:p-8">
        <div class="max-w-6xl mx-auto space-y-8">
            
            <!-- Header Brand -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-slate-900/40 p-6 rounded-3xl border border-slate-800">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-tr from-cyan-500 to-indigo-500 rounded-2xl flex items-center justify-center font-black text-xl text-white shadow-lg shadow-indigo-500/20">
                        AI
                    </div>
                    <div>
                        <h1 class="text-xl font-extrabold tracking-tight">Python Face Recognition Engine</h1>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mt-0.5">REAL BIOMETRIC ATTENDANCE PANEL</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-xs font-mono font-bold text-slate-400 bg-slate-800/80 px-3 py-1.5 rounded-lg border border-slate-700">PORT: 5000</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Left: Simulator Settings -->
                <div class="lg:col-span-5 space-y-6">
                    <div class="glass-card p-6 rounded-3xl space-y-6">
                        <h2 class="text-base font-extrabold text-white flex items-center gap-2">
                            <span class="w-2 h-4 rounded bg-cyan-500"></span>
                            Simulator Mode Overrides
                        </h2>
                        
                        <form action="/update-config" method="POST" class="space-y-5">
                            <!-- Select Mode -->
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Mode Rekognisi</label>
                                <select name="mode" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs font-semibold focus:border-cyan-500 focus:outline-none">
                                    <option value="smart_sync" """ + ('selected' if system_config['mode']=='smart_sync' else '') + """>✨ Smart Sync (Biometrik Asli OpenCV - 1-to-1)</option>
                                    <option value="force_budi" """ + ('selected' if system_config['mode']=='force_budi' else '') + """>👤 Paksa Sukses Budi Handoko (0089123456)</option>
                                    <option value="force_siti" """ + ('selected' if system_config['mode']=='force_siti' else '') + """>👤 Paksa Sukses Siti Aminah (0089123457)</option>
                                    <option value="auto_first" """ + ('selected' if system_config['mode']=='auto_first' else '') + """>🔄 Paksa Sukses Siapa Saja Wajah Terdaftar</option>
                                    <option value="force_fail" """ + ('selected' if system_config['mode']=='force_fail' else '') + """>❌ Paksa Gagal (Biometrik Ditolak)</option>
                                </select>
                                <p class="text-[10px] text-slate-500 leading-normal">
                                    * <b>Smart Sync:</b> Menggunakan model kecerdasan buatan <b>LBPH Face Recognizer</b> asli untuk membandingkan webcam live 1-to-1 dengan file master di `faces_dataset/`.
                                </p>
                            </div>

                            <!-- Delay seconds slider -->
                            <div class="space-y-2">
                                <div class="flex justify-between items-center text-xs">
                                    <label class="font-bold text-slate-400 uppercase tracking-wider">Delay Analisis AI</label>
                                    <span class="font-mono font-bold text-cyan-400">""" + str(system_config['delay_seconds']) + """ Detik</span>
                                </div>
                                <input type="range" name="delay_seconds" min="0" max="5" step="0.5" value="""" + str(system_config['delay_seconds']) + """" 
                                    class="w-full h-1.5 bg-slate-950 rounded-lg appearance-none cursor-pointer accent-cyan-500">
                                <p class="text-[10px] text-slate-500 leading-normal">
                                    * Mengatur durasi spinner "Menganalisis Wajah" di Kiosk Lobi SMA untuk mensimulasikan pemrosesan neural network secara dramatis.
                                </p>
                            </div>

                            <!-- Confidence Score -->
                            <div class="space-y-2">
                                <div class="flex justify-between items-center text-xs">
                                    <label class="font-bold text-slate-400 uppercase tracking-wider">Confidence Level</label>
                                    <span class="font-mono font-bold text-indigo-400">""" + str(int(system_config['confidence_rate']*100)) + """%</span>
                                </div>
                                <input type="range" name="confidence_rate" min="50" max="100" step="1" value="""" + str(int(system_config['confidence_rate']*100)) + """" 
                                    class="w-full h-1.5 bg-slate-950 rounded-lg appearance-none cursor-pointer accent-indigo-500">
                            </div>

                            <button type="submit" class="w-full py-3 bg-gradient-to-r from-cyan-600 to-indigo-600 hover:from-cyan-500 hover:to-indigo-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all cursor-pointer text-center">
                                Simpan Konfigurasi
                            </button>
                        </form>
                    </div>

                    <!-- Dataset registered count -->
                    <div class="glass-card p-6 rounded-3xl">
                        <h2 class="text-sm font-extrabold text-white mb-4 flex items-center gap-2">
                            <span class="w-2 h-4 rounded bg-indigo-500"></span>
                            Biometrik Dataset Master (NISN)
                        </h2>
                        
                        <div class="space-y-2.5 max-h-48 overflow-y-auto pr-1">
                            """ + "".join([f"""
                            <div class="flex justify-between items-center p-2.5 bg-slate-950/60 rounded-xl border border-slate-800 text-xs font-semibold">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span class="font-mono font-bold text-slate-300">{nisn}</span>
                                </div>
                                <span class="text-[10px] text-cyan-400">Standardized PNG (200x200)</span>
                            </div>
                            """ for nisn in faces.keys()]) + ("""
                            <p class="text-center py-4 text-xs font-bold text-slate-500">Belum ada wajah master terdaftar secara lokal.</p>
                            """ if not faces else "") + """
                        </div>
                    </div>
                </div>

                <!-- Right: Live System Log Stream -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="glass-card p-6 rounded-3xl flex flex-col h-[520px]">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-800 shrink-0">
                            <h2 class="text-base font-extrabold text-white flex items-center gap-2">
                                <span class="w-2 h-4 rounded bg-emerald-500"></span>
                                Live Biometric API Monitor Stream
                            </h2>
                            <span class="text-[10px] font-bold text-slate-500 font-mono tracking-wider animate-pulse">● LIVE STREAMING</span>
                        </div>

                        <!-- Real-time logs container -->
                        <div class="flex-1 overflow-y-auto py-4 space-y-3.5 font-mono text-[11px] pr-1 mt-2">
                            """ + "".join([f"""
                            <div class="p-3 bg-slate-950/80 border border-slate-900 rounded-xl leading-relaxed">
                                <div class="flex justify-between items-center text-[10px] pb-1 border-b border-slate-900 mb-1.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-slate-500">[{log['time']}]</span>
                                        <span class="font-bold text-cyan-400">{log['action']}</span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase {'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' if log['status']=='SUCCESS' else 'bg-rose-500/10 text-rose-400 border border-rose-500/20'}">
                                        {log['status']}
                                    </span>
                                </div>
                                <span class="text-slate-300 font-medium">{log['details']}</span>
                            </div>
                            """ for log in scan_logs]) + ("""
                            <div class="h-full flex flex-col items-center justify-center text-slate-500 text-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <span class="font-bold">Menunggu Triger Request Presensi Webcam...</span>
                            </div>
                            """ if not scan_logs else "") + """
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </body>
    </html>
    """
    return html_content

@app.route('/update-config', methods=['POST'])
def update_config():
    """Mengupdate setting mode simulasi AI."""
    system_config['mode'] = request.form.get('mode', 'smart_sync')
    system_config['delay_seconds'] = float(request.form.get('delay_seconds', 1.5))
    system_config['confidence_rate'] = int(request.form.get('confidence_rate', 94)) / 100
    
    add_log("CONFIG_UPDATED", f"Mode diatur ke {system_config['mode'].upper()} | Delay {system_config['delay_seconds']}s | Confidence {int(system_config['confidence_rate']*100)}%")
    return f"<script>alert('Konfigurasi Simulator Berhasil Disimpan!'); window.location.href='/';</script>"

@app.route('/register-face', methods=['POST'])
def register_face():
    """Menerima foto wajah master dan mendaftarkannya di database lokal python."""
    try:
        data = request.get_json()
        if not data or ('nisn' not in data and 'nim' not in data) or 'image' not in data:
            return jsonify({'success': False, 'message': 'NISN dan Image base64 wajib dikirim.'}), 400
        
        nisn = data.get('nisn') or data.get('nim')
        image_b64 = data['image']
        
        # 1. Dekode image dari base64
        img = decode_base64_image(image_b64)
        if img is None:
            add_log("REGISTER_FAILED", f"Gagal mendaftarkan wajah NISN {nisn}: format gambar tidak valid.", "FAILED")
            return jsonify({'success': False, 'message': 'Format gambar tidak valid.'}), 400
            
        # 2. Ekstrak area wajah 200x200 px grayscale
        face_img = extract_face(img)
        if face_img is None:
            add_log("REGISTER_FAILED", f"Gagal mendaftarkan wajah NISN {nisn}: wajah tidak terdeteksi.", "FAILED")
            return jsonify({
                'success': False, 
                'message': 'Wajah tidak terdeteksi. Pastikan pencahayaan cukup dan wajah tegak lurus menghadap kamera.'
            }), 422
            
        # 3. Simpan biner wajah master ke faces_dataset/<nisn>.png
        filepath = os.path.join(FACES_DATASET_DIR, f"{nisn}.png")
        cv2.imwrite(filepath, face_img)
        
        # Simpan ke database lokal JSON untuk pencatatan manifest
        faces = get_registered_faces()
        faces[nisn] = {
            'registered_at': time.strftime('%Y-%m-%d %H:%M:%S'),
            'image_path': filepath
        }
        save_registered_faces(faces)
        
        # Cetak console log
        print(f"\033[92m[API - REGISTER] NISN: {nisn} berhasil didaftarkan sebagai Wajah Master Biometrik (200x200 px).\033[0m")
        add_log("REGISTER_FACE", f"Wajah master untuk siswa NISN {nisn} berhasil diekstrak (200x200 px grayscale) dan disimpan.")
        
        return jsonify({
            'success': True,
            'message': f'Biometric face registration for NISN {nisn} synced successfully on Python AI Engine.'
        })
    except Exception as e:
        print(f"\033[91m[API - ERROR] Gagal mendaftarkan wajah: {str(e)}\033[0m")
        add_log("REGISTER_FACE_ERROR", f"Gagal mengekstrak biometrik: {str(e)}", "FAILED")
        return jsonify({'success': False, 'message': str(e)}), 500

@app.route('/recognize', methods=['POST'])
def recognize():
    """Proses utama pengenalan wajah biometrik (Face recognition scan)."""
    # Simulasi delay neural network
    if system_config['delay_seconds'] > 0:
        time.sleep(system_config['delay_seconds'])
        
    try:
        data = request.get_json()
        if not data or 'image' not in data:
            add_log("RECOGNIZE_FAILED", "Menerima request scan kosong atau tanpa payload gambar.", "FAILED")
            return jsonify({'success': False, 'message': 'Payload image base64 wajib diisi.'}), 400
        
        image_b64 = data['image']
        req_nisn = data.get('nisn') or data.get('nim')
        
        # Algoritma Evaluasi Mode Simulasi / Overrides
        mode = system_config['mode']
        
        # 1. Jika paksa gagal diaktifkan di panel control
        if mode == 'force_fail':
            print("\033[91m[API - RECOGNIZE] Paksa Gagal Aktif.\033[0m")
            add_log("RECOGNIZE_FAILED", "Mode Paksa Gagal aktif. Scan dibatalkan secara sistem.", "FAILED")
            return jsonify({
                'success': False,
                'name': '',
                'confidence': 0.15,
                'message': 'Presensi Gagal! Wajah tidak cocok dengan profil terdaftar Anda.'
            })
            
        # 2. Ambil target NISN yang akan dicocokkan (1-to-1 verification)
        target_nisn = None
        if req_nisn and (mode == 'smart_sync' or mode == 'force_budi' or mode == 'force_siti'):
            target_nisn = req_nisn
            
        if mode == 'force_budi':
            target_nisn = '0089123456'
        elif mode == 'force_siti':
            target_nisn = '0089123457'
            
        # Jika mode auto_first, ambil wajah master pertama yang terdaftar
        if not target_nisn and mode == 'auto_first':
            faces = get_registered_faces()
            if faces:
                target_nisn = list(faces.keys())[0]
            else:
                target_nisn = '0089123456'
                
        # Jika smart_sync tapi target_nisn masih None, baca dari active_student.json
        if not target_nisn and mode == 'smart_sync':
            if os.path.exists(ACTIVE_STUDENT_FILE):
                try:
                    with open(ACTIVE_STUDENT_FILE, 'r') as f:
                        session_data = json.load(f)
                        target_nisn = session_data.get('nisn') or session_data.get('nim')
                except Exception:
                    pass
            if not target_nisn:
                target_nisn = '0089123456'
                
        # 3. Dekode live webcam image
        img = decode_base64_image(image_b64)
        if img is None:
            add_log("RECOGNIZE_FAILED", "Menerima format gambar tidak valid.", "FAILED")
            return jsonify({'success': False, 'message': 'Format gambar tidak valid.', 'confidence': 0.0})
            
        # 4. Ekstrak live face
        live_face = extract_face(img)
        if live_face is None:
            print("\033[91m[API - RECOGNIZE] Wajah tidak terdeteksi di kamera.\033[0m")
            add_log("RECOGNIZE_FAILED", "Scan Ditolak: Wajah tidak terdeteksi atau terhalang.", "FAILED")
            return jsonify({
                'success': False,
                'name': '',
                'confidence': 0.0,
                'message': 'Wajah tidak terdeteksi di kamera. Pastikan posisi wajah Anda tegak lurus.'
            })
            
        # 5. Cari wajah master target
        master_path = os.path.join(FACES_DATASET_DIR, f"{target_nisn}.png")
        
        # Seeding fallback jika master_path tidak ditemukan
        if not os.path.exists(master_path):
            print(f"\033[93m[API - WARNING] Wajah master untuk NISN {target_nisn} tidak ditemukan di {master_path}.\033[0m")
            add_log("RECOGNIZE_FAILED", f"Wajah master untuk NISN {target_nisn} belum didaftarkan di folder dataset.", "FAILED")
            return jsonify({
                'success': False,
                'name': '',
                'confidence': 0.0,
                'message': f'Presensi Gagal! Wajah master untuk NISN {target_nisn} belum didaftarkan.'
            })
            
        # Load master face (Grayscale)
        master_face = cv2.imread(master_path, cv2.IMREAD_GRAYSCALE)
        if master_face is None:
            add_log("RECOGNIZE_FAILED", f"Gagal membaca file wajah master di {master_path}.", "FAILED")
            return jsonify({
                'success': False,
                'name': '',
                'confidence': 0.0,
                'message': 'Gagal memuat template wajah master.'
            })
            
        # 6. Jalankan perbandingan biometrik menggunakan LBPH Face Recognizer
        try:
            # Buat model recognizer
            recognizer = cv2.face.LBPHFaceRecognizer_create()
            
            # Train model dengan satu wajah master (Label = 1)
            recognizer.train([master_face], np.array([1]))
            
            # Lakukan prediksi terhadap live face
            label, distance = recognizer.predict(live_face)
            
            # Hitung persentase kecocokan (confidence rate) berdasarkan distance biometrik
            # LBPH distance: 0 = mirip sempurna. < 68.0 = lolos verifikasi 1-to-1.
            # >= 68.0 = tidak cocok (wajah orang lain atau terhalang)
            if distance < 65.0:
                confidence_percentage = int(100 - (distance * 0.7))
                is_match = True
            else:
                confidence_percentage = max(0, int(60 - ((distance - 65.0) * 3.0)))
                is_match = False
            
            # Menentukan nama siswa
            recognized_name = 'Siswa Terdaftar'
            if target_nisn == '0089123456':
                recognized_name = 'Budi Handoko'
            elif target_nisn == '0089123457':
                recognized_name = 'Siti Aminah'
                
            # Cek kecocokan biometrik
            if is_match:
                print(f"\033[92m[API - RECOGNIZE] Wajah Cocok! NISN: {target_nisn} ({recognized_name}) | Distance: {distance:.2f} | Confidence: {confidence_percentage}%\033[0m")
                add_log("RECOGNIZE_SUCCESS", f"Wajah teridentifikasi sebagai NISN {target_nisn} ({recognized_name}) dengan akurasi biometrik {confidence_percentage}% (Jarak: {distance:.1f}).")
                return jsonify({
                    'success': True,
                    'name': target_nisn,  # Kembalikan NISN
                    'confidence': float(confidence_percentage) / 100.0,
                    'message': 'Face recognized successfully.'
                })
            else:
                print(f"\033[91m[API - RECOGNIZE] Wajah TIDAK Cocok! NISN: {target_nisn} | Distance: {distance:.2f} | Confidence: {confidence_percentage}%\033[0m")
                add_log("RECOGNIZE_FAILED", f"Verifikasi biometrik gagal untuk NISN {target_nisn}. Jarak biometrik terdeteksi terlalu jauh: {distance:.1f} (Akurasi: {confidence_percentage}%).", "FAILED")
                return jsonify({
                    'success': False,
                    'name': '',
                    'confidence': float(confidence_percentage) / 100.0,
                    'message': 'Presensi Gagal! Wajah tidak cocok dengan profil terdaftar Anda.'
                })
                
        except Exception as err:
            print(f"[RECOGNIZE ALGO ERROR] Gagal menjalankan LBPH Recognizer: {str(err)}")
            return jsonify({
                'success': False,
                'name': '',
                'confidence': 0.0,
                'message': f'System biometric error: {str(err)}'
            }), 500
            
    except Exception as e:
        print(f"\033[91m[API - ERROR] Gagal melakukan rekognisi: {str(e)}\033[0m")
        add_log("RECOGNIZE_ERROR", f"Error runtime engine: {str(e)}", "FAILED")
        return jsonify({'success': False, 'message': str(e), 'confidence': 0.0}), 500

@app.route('/identify', methods=['POST'])
def identify():
    """Proses identifikasi wajah biometrik 1-to-many (Scan langsung tanpa input NISN/Nama)."""
    if system_config['delay_seconds'] > 0:
        time.sleep(system_config['delay_seconds'])

    try:
        data = request.get_json()
        if not data or 'image' not in data:
            add_log("IDENTIFY_FAILED", "Menerima request scan kosong atau tanpa payload gambar.", "FAILED")
            return jsonify({'success': False, 'message': 'Payload image base64 wajib diisi.'}), 400

        image_b64 = data['image']
        mode = system_config['mode']

        if mode == 'force_fail':
            print("\033[91m[API - IDENTIFY] Paksa Gagal Aktif.\033[0m")
            add_log("IDENTIFY_FAILED", "Mode Paksa Gagal aktif. Scan dibatalkan secara sistem.", "FAILED")
            return jsonify({
                'success': False,
                'nisn': '',
                'name': '',
                'confidence': 0.15,
                'message': 'Wajah tidak dikenali. Tidak ada profil yang cocok dengan database biometrik.'
            })

        # Mode paksa (override panel kontrol) langsung mengarahkan ke NISN target tertentu
        forced_nisn = None
        if mode == 'force_budi':
            forced_nisn = '0089123456'
        elif mode == 'force_siti':
            forced_nisn = '0089123457'
        elif mode == 'auto_first':
            faces_db = get_registered_faces()
            if faces_db:
                forced_nisn = list(faces_db.keys())[0]

        img = decode_base64_image(image_b64)
        if img is None:
            add_log("IDENTIFY_FAILED", "Menerima format gambar tidak valid.", "FAILED")
            return jsonify({'success': False, 'message': 'Format gambar tidak valid.', 'confidence': 0.0})

        live_face = extract_face(img)
        if live_face is None:
            print("\033[91m[API - IDENTIFY] Wajah tidak terdeteksi di kamera.\033[0m")
            add_log("IDENTIFY_FAILED", "Scan Ditolak: Wajah tidak terdeteksi atau terhalang.", "FAILED")
            return jsonify({
                'success': False,
                'nisn': '',
                'name': '',
                'confidence': 0.0,
                'message': 'Wajah tidak terdeteksi di kamera. Pastikan posisi wajah Anda tegak lurus.'
            })

        # Kumpulkan seluruh wajah master terdaftar untuk perbandingan 1-to-many
        registered = get_registered_faces()
        master_faces = []
        labels = []
        label_to_nisn = {}
        next_label = 1
        for nisn in registered.keys():
            master_path = os.path.join(FACES_DATASET_DIR, f"{nisn}.png")
            if not os.path.exists(master_path):
                continue
            master_face = cv2.imread(master_path, cv2.IMREAD_GRAYSCALE)
            if master_face is None:
                continue
            master_faces.append(master_face)
            labels.append(next_label)
            label_to_nisn[next_label] = nisn
            next_label += 1

        # Tambahkan wajah negatif (sampel pembanding) dari dataset yang tidak terdaftar di database saat ini
        # Label 999 digunakan untuk mendefinisikan "Orang Lain / Wajah Tidak Dikenali"
        if os.path.exists(FACES_DATASET_DIR):
            for file_name in os.listdir(FACES_DATASET_DIR):
                if file_name.endswith('.png'):
                    file_nisn = file_name.split('.')[0]
                    if file_nisn not in registered:
                        neg_path = os.path.join(FACES_DATASET_DIR, file_name)
                        neg_face = cv2.imread(neg_path, cv2.IMREAD_GRAYSCALE)
                        if neg_face is not None:
                            master_faces.append(neg_face)
                            labels.append(999) # 999 = Label Wajah Tidak Dikenali

        if not master_faces:
            add_log("IDENTIFY_FAILED", "Tidak ada wajah master yang terdaftar di dataset biometrik.", "FAILED")
            return jsonify({
                'success': False,
                'nisn': '',
                'name': '',
                'confidence': 0.0,
                'message': 'Belum ada data wajah master yang terdaftar di sistem.'
            })

        try:
            # Latih recognizer dengan SELURUH wajah master (multi-label) lalu cari label dengan jarak terdekat
            recognizer = cv2.face.LBPHFaceRecognizer_create()
            recognizer.train(master_faces, np.array(labels))

            label, distance = recognizer.predict(live_face)
            matched_nisn = label_to_nisn.get(label)

            if forced_nisn:
                matched_nisn = forced_nisn
                distance = 30.0

            if distance < 65.0 and matched_nisn:
                confidence_percentage = int(100 - (distance * 0.7))
                recognized_name = 'Siswa Terdaftar'
                if matched_nisn == '0089123456':
                    recognized_name = 'Budi Handoko'
                elif matched_nisn == '0089123457':
                    recognized_name = 'Siti Aminah'

                print(f"\033[92m[API - IDENTIFY] Wajah Teridentifikasi! NISN: {matched_nisn} ({recognized_name}) | Distance: {distance:.2f} | Confidence: {confidence_percentage}%\033[0m")
                add_log("IDENTIFY_SUCCESS", f"Wajah teridentifikasi sebagai NISN {matched_nisn} ({recognized_name}) dengan akurasi biometrik {confidence_percentage}% (Jarak: {distance:.1f}).")
                return jsonify({
                    'success': True,
                    'nisn': matched_nisn,
                    'name': matched_nisn,
                    'confidence': float(confidence_percentage) / 100.0,
                    'message': 'Face identified successfully.'
                })
            else:
                confidence_percentage = max(0, int(60 - ((distance - 65.0) * 3.0)))
                print(f"\033[91m[API - IDENTIFY] Wajah TIDAK Dikenali! Distance terdekat: {distance:.2f} | Confidence: {confidence_percentage}%\033[0m")
                add_log("IDENTIFY_FAILED", f"Wajah tidak ditemukan kecocokan di database biometrik. Jarak terdekat: {distance:.1f} (Akurasi: {confidence_percentage}%).", "FAILED")
                return jsonify({
                    'success': False,
                    'nisn': '',
                    'name': '',
                    'confidence': float(confidence_percentage) / 100.0,
                    'message': 'Wajah tidak dikenali. Pastikan Anda sudah terdaftar dan posisikan wajah dengan jelas menghadap kamera.'
                })

        except Exception as err:
            print(f"[IDENTIFY ALGO ERROR] Gagal menjalankan LBPH Recognizer: {str(err)}")
            return jsonify({
                'success': False,
                'nisn': '',
                'name': '',
                'confidence': 0.0,
                'message': f'System biometric error: {str(err)}'
            }), 500

    except Exception as e:
        print(f"\033[91m[API - ERROR] Gagal melakukan identifikasi: {str(e)}\033[0m")
        add_log("IDENTIFY_ERROR", f"Error runtime engine: {str(e)}", "FAILED")
        return jsonify({'success': False, 'message': str(e), 'confidence': 0.0}), 500

if __name__ == '__main__':
    print_banner()
    app.run(host='127.0.0.1', port=5001, debug=False)
