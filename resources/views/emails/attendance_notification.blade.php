<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Presensi Siswa SMAN 1 Utama</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 11px;
            opacity: 0.85;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #0f172a;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
        }
        .details-table th, .details-table td {
            padding: 12px 15px;
            text-align: left;
            font-size: 14px;
            border-bottom: 1px solid #f1f5f9;
        }
        .details-table th {
            font-weight: 600;
            color: #64748b;
            width: 35%;
        }
        .details-table td {
            color: #0f172a;
            font-weight: 500;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-hadir {
            background-color: #dcfce7;
            color: #15803d;
        }
        .status-telat {
            background-color: #fef3c7;
            color: #b45309;
        }
        .status-sakit, .status-izin {
            background-color: #dbeafe;
            color: #1d4ed8;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #f1f5f9;
            font-size: 11px;
            color: #94a3b8;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SMAN 1 UTAMA</h1>
            <p>Sistem Presensi Biometrik Lobi Sekolah</p>
        </div>
        
        <div class="content">
            <div class="greeting">Yth. Orang Tua / Wali Murid,</div>
            <p style="font-size: 14px; line-height: 1.6; color: #475569; margin: 0;">
                Kami menginformasikan bahwa putra/putri Anda telah mencatatkan riwayat kehadiran pada sistem Kiosk Presensi SMAN 1 Utama dengan rincian berikut:
            </p>
            
            <table class="details-table">
                <tr>
                    <th>Nama Lengkap</th>
                    <td>{{ $student->user->name }}</td>
                </tr>
                <tr>
                    <th>NISN</th>
                    <td style="font-family: monospace;">{{ $student->nisn }}</td>
                </tr>
                <tr>
                    <th>Kelas / Rombel</th>
                    <td>{{ $student->class_name }}</td>
                </tr>
                <tr>
                    <th>Status Presensi</th>
                    <td>
                        @if($status === 'hadir')
                            <span class="status-badge status-hadir">Hadir Tepat Waktu</span>
                        @elseif($status === 'telat')
                            <span class="status-badge status-telat">Hadir Terlambat</span>
                        @elseif($status === 'sakit')
                            <span class="status-badge status-sakit">Sakit</span>
                        @elseif($status === 'izin')
                            <span class="status-badge status-izin">Izin</span>
                        @else
                            <span class="status-badge">{{ strtoupper($status) }}</span>
                        @endif
                    </td>
                </tr>
                @if($time)
                <tr>
                    <th>Waktu Masuk</th>
                    <td>{{ $time }} WIB</td>
                </tr>
                @endif
            </table>
            
            <p style="font-size: 14px; line-height: 1.6; color: #475569; margin: 0;">
                Terima kasih atas perhatian Anda dalam membantu kami memantau kedisiplinan dan kehadiran siswa di sekolah.
            </p>
        </div>
        
        <div class="footer">
            &copy; 2026 SMAN 1 Utama &bull; Bidang Kurikulum & Kesiswaan<br>
            Pesan ini dikirim secara otomatis oleh sistem, mohon tidak membalas email ini.
        </div>
    </div>
</body>
</html>
