<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR Code Pelanggan PAMSIMAS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            padding: 10mm;
        }
        
        .header {
            text-align: center;
            margin-bottom: 10mm;
            padding-bottom: 5mm;
            border-bottom: 3px solid #1e40af;
        }
        
        .header h1 {
            color: #1e40af;
            font-size: 24px;
            margin-bottom: 3px;
        }
        
        .header p {
            color: #64748b;
            font-size: 12px;
        }
        
        .qr-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 5mm;
        }
        
        .qr-card {
            width: calc(33.333% - 3.5mm);
            border: 2px solid #cbd5e1;
            border-radius: 8px;
            padding: 5mm;
            text-align: center;
            page-break-inside: avoid;
            background: white;
        }
        
        .qr-card-header {
            border-bottom: 2px solid #1e40af;
            padding-bottom: 3mm;
            margin-bottom: 3mm;
        }
        
        .qr-card-header h3 {
            color: #1e40af;
            font-size: 12px;
            font-weight: bold;
        }
        
        .qr-card-header p {
            color: #64748b;
            font-size: 8px;
        }
        
        .qr-image {
            margin: 3mm 0;
        }
        
        .qr-image img {
            width: 100%;
            max-width: 45mm;
            height: auto;
        }
        
        .qr-info {
            border-top: 1px solid #e2e8f0;
            padding-top: 3mm;
            margin-top: 3mm;
        }
        
        .qr-id {
            font-size: 10px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 2mm;
        }
        
        .qr-name {
            font-size: 9px;
            color: #475569;
            margin-bottom: 1mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .qr-address {
            font-size: 8px;
            color: #64748b;
        }
        
        .qr-footer {
            margin-top: 2mm;
            padding-top: 2mm;
            border-top: 1px solid #e2e8f0;
        }
        
        .qr-footer p {
            font-size: 7px;
            color: #94a3b8;
            font-style: italic;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        @media print {
            body {
                padding: 5mm;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>KP-SPAMS DAMAR WULAN</h1>
        <p>Dicetak: {{ date('d F Y H:i') }} | Total: {{ count($pelangganList) }} Pelanggan</p>
    </div>
    
    <div class="qr-grid">
        @foreach($pelangganList as $index => $pelanggan)
        <div class="qr-card {{ ($index + 1) % 12 === 0 ? 'page-break' : '' }}">
            <div class="qr-card-header">
                <h3>KP-SPAMS</h3>
                <p>DAMAR WULAN</p>
            </div>
            
            <div class="qr-image">
                <img src="{{ $pelanggan['qr_code'] }}" alt="{{ $pelanggan['id_pelanggan'] }}">
            </div>
            
            <div class="qr-info">
                <div class="qr-id">{{ $pelanggan['id_pelanggan'] }}</div>
                <div class="qr-name">{{ $pelanggan['nama_pelanggan'] }}</div>
                <div class="qr-address">RT {{ $pelanggan['rt'] }} / RW {{ $pelanggan['rw'] }}</div>
                @if(isset($pelanggan['wilayah']) && $pelanggan['wilayah'])
                <div class="qr-address" style="margin-top: 1mm;">{{ $pelanggan['wilayah'] }}</div>
                @endif
            </div>
            
            <div class="qr-footer">
                <p>Scan untuk Input Meteran</p>
            </div>
        </div>
        @endforeach
    </div>
</body>
</html>
