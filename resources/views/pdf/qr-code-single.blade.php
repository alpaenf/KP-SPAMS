<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR Code - {{ $pelanggan->id_pelanggan }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            padding: 20px;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .card {
            border: 3px solid #1e40af;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin-top: 50px;
        }
        
        .header {
            margin-bottom: 20px;
        }
        
        .header h1 {
            color: #1e40af;
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #64748b;
            font-size: 16px;
        }
        
        .qr-container {
            margin: 30px 0;
            padding: 20px;
            background: #f8fafc;
            border-radius: 10px;
        }
        
        .qr-container img {
            width: 300px;
            height: 300px;
            margin: 0 auto;
            display: block;
        }
        
        .info {
            margin-top: 20px;
            padding: 20px;
            background: #eff6ff;
            border-radius: 10px;
            text-align: left;
        }
        
        .info-row {
            padding: 10px 0;
            border-bottom: 1px solid #bfdbfe;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .info-value {
            color: #1e293b;
            font-size: 16px;
            font-weight: 600;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            color: #64748b;
            font-size: 12px;
        }
        
        .footer strong {
            color: #1e40af;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>PAMSIMAS</h1>
                <p>QR Code Pelanggan</p>
            </div>
            
            <div class="qr-container">
                <img src="{{ $qr_code }}" alt="QR Code">
            </div>
            
            <div class="info">
                <div class="info-row">
                    <div class="info-label">ID Pelanggan</div>
                    <div class="info-value">{{ $pelanggan->id_pelanggan }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Nama Pelanggan</div>
                    <div class="info-value">{{ $pelanggan->nama_pelanggan }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Alamat</div>
                    <div class="info-value">RT {{ $pelanggan->rt }} / RW {{ $pelanggan->rw }}</div>
                </div>
                @if($pelanggan->wilayah)
                <div class="info-row">
                    <div class="info-label">Wilayah</div>
                    <div class="info-value">{{ $pelanggan->wilayah }}</div>
                </div>
                @endif
                <div class="info-row">
                    <div class="info-label">Status</div>
                    <div class="info-value">{{ $pelanggan->status_aktif ? 'Aktif' : 'Tidak Aktif' }}</div>
                </div>
            </div>
            
            <div class="footer">
                <p><strong>Cara Penggunaan:</strong></p>
                <p>Scan QR Code ini menggunakan aplikasi PAMSIMAS untuk input meteran air</p>
                <p style="margin-top: 10px;">Dicetak: {{ date('d F Y H:i') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
