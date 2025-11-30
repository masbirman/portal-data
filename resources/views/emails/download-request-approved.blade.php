<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Download Disetujui</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            border-left: 4px solid #667eea;
        }
        .info-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            width: 150px;
            color: #6c757d;
        }
        .info-value {
            flex: 1;
            color: #212529;
        }
        .download-button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
        }
        .warning-box {
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
        <h1 style="margin: 0;">✅ Permintaan Disetujui</h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">Portal Data AN-TKA Disdik Sulteng</p>
    </div>

    <div class="content">
        <p>Halo <strong>{{ $downloadRequest->nama }}</strong>,</p>

        <p>Permintaan download data Anda telah <strong>disetujui</strong>. Berikut adalah detail permintaan Anda:</p>

        <div class="info-box">
            <div class="info-row">
                <div class="info-label">Jenis Data:</div>
                <div class="info-value">{{ ucfirst(str_replace('_', ' ', $downloadRequest->data_type)) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tahun:</div>
                <div class="info-value">{{ $downloadRequest->tahun }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Wilayah:</div>
                <div class="info-value">{{ $downloadRequest->wilayah->nama ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Jenjang:</div>
                <div class="info-value">{{ $downloadRequest->jenjangPendidikan->nama ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Instansi:</div>
                <div class="info-value">{{ $downloadRequest->instansi }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Berlaku Hingga:</div>
                <div class="info-value">{{ $downloadRequest->token_expires_at->format('d F Y, H:i') }} WIB</div>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('download-request.download', $downloadRequest->download_token) }}" class="download-button">
                📥 Download Data Sekarang
            </a>
        </div>

        <div class="warning-box">
            <strong>⚠️ Penting:</strong>
            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                <li>Link download berlaku selama <strong>7 hari</strong></li>
                <li>Setelah diklik, data akan terdownload otomatis</li>
                <li>Gunakan data sesuai tujuan yang telah disebutkan</li>
                <li>Jangan bagikan link ini kepada orang lain</li>
            </ul>
        </div>

        <p>Jika Anda memiliki pertanyaan, silakan hubungi kami melalui email ini.</p>

        <p>Terima kasih,<br>
        <strong>Tim Portal Data AN-TKA Disdik Sulteng</strong></p>
    </div>

    <div class="footer">
        <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
        <p>© {{ date('Y') }} Dinas Pendidikan Provinsi Sulawesi Tengah</p>
    </div>
</body>
</html>
