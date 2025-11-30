<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Download Ditolak</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 30px;
            border-radius: 10px 10px 0 0;
            text-align: center;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .info-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #f5576c;
        }
        .reason-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">❌ Permintaan Ditolak</h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">Portal Data AN-TKA Disdik Sulteng</p>
    </div>

    <div class="content">
        <p>Halo <strong>{{ $downloadRequest->nama }}</strong>,</p>

        <p>Mohon maaf, permintaan download data Anda <strong>tidak dapat disetujui</strong> saat ini.</p>

        <div class="info-box">
            <strong>Detail Permintaan:</strong>
            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                <li>Jenis Data: {{ ucfirst(str_replace('_', ' ', $downloadRequest->data_type)) }}</li>
                <li>Tahun: {{ $downloadRequest->tahun }}</li>
                <li>Wilayah: {{ $downloadRequest->wilayah->nama ?? '-' }}</li>
                <li>Jenjang: {{ $downloadRequest->jenjangPendidikan->nama ?? '-' }}</li>
                <li>Instansi: {{ $downloadRequest->instansi }}</li>
            </ul>
        </div>

        @if($downloadRequest->admin_notes)
        <div class="reason-box">
            <strong>Alasan Penolakan:</strong>
            <p style="margin: 10px 0 0 0;">{{ $downloadRequest->admin_notes }}</p>
        </div>
        @endif

        <p>Jika Anda memiliki pertanyaan atau ingin mengajukan permintaan baru dengan informasi yang lebih lengkap, silakan hubungi kami atau ajukan permintaan baru melalui portal.</p>

        <p>Terima kasih atas pengertian Anda.</p>

        <p>Salam,<br>
        <strong>Tim Portal Data AN-TKA Disdik Sulteng</strong></p>
    </div>

    <div class="footer">
        <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
        <p>© {{ date('Y') }} Dinas Pendidikan Provinsi Sulawesi Tengah</p>
    </div>
</body>
</html>
