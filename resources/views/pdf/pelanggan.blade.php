<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Data Pelanggan - {{ $pelanggan['nama_pelanggan'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px double #2563eb;
        }
        .header img {
            width: 80px;
            height: auto;
            margin-bottom: 15px;
        }
        .header h1 {
            color: #2563eb;
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
            color: #666;
        }
        .meta-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .meta-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .meta-label {
            display: table-cell;
            width: 150px;
            font-weight: bold;
            color: #555;
            vertical-align: top;
        }
        .meta-value {
            display: table-cell;
            color: #000;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        .status-lunas { background-color: #2563eb; }
        .status-belum { background-color: #d9534f; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #2563eb;
            color: white;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .highlight {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
        }
        .stamp {
            margin-top: 30px;
            text-align: right;
            padding-right: 50px;
        }
        .stamp p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo KP-SPAMS">
        <h1>KP-SPAMS "DAMMAR WULAN"</h1>
        <p>Laporan Data Status & Riwayat Pelanggan</p>
    </div>

    <div class="meta-info">
        <div class="meta-row">
            <div class="meta-label">ID Pelanggan</div>
            <div class="meta-value">{{ $pelanggan['id_pelanggan'] }}</div>
        </div>
        <div class="meta-row">
            <div class="meta-label">Nama Lengkap</div>
            <div class="meta-value" style="font-size: 18px; font-weight: bold;">{{ $pelanggan['nama_pelanggan'] }}</div>
        </div>
        <div class="meta-row">
            <div class="meta-label">Alamat</div>
            <div class="meta-value">RT {{ $pelanggan['rt'] }} / RW {{ $pelanggan['rw'] }}</div>
        </div>
        <div class="meta-row">
            <div class="meta-label">Kategori</div>
            <div class="meta-value">{{ ucfirst($pelanggan['kategori']) }}</div>
        </div>
        <div class="meta-row">
            <div class="meta-label">Status Aktif</div>
            <div class="meta-value">
                @if($pelanggan['status_aktif'])
                    <span style="color: #2563eb; font-weight: bold;">✔ Aktif</span>
                @else
                    <span style="color: #d9534f; font-weight: bold;">✘ Non-Aktif</span>
                @endif
            </div>
        </div>
    </div>

    <h3>Status Pembayaran Bulan Ini ({{ $pelanggan['tagihan_bulan_ini'] }})</h3>
    <div style="border: 2px solid {{ $pelanggan['status_bayar'] == 'Lunas' ? '#2563eb' : '#f0ad4e' }}; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
        <div class="meta-row">
            <div class="meta-label">Status Bayar</div>
            <div class="meta-value">
                <span class="status-badge {{ $pelanggan['status_bayar'] == 'Lunas' ? 'status-lunas' : 'status-belum' }}">
                    {{ $pelanggan['status_bayar'] }}
                </span>
            </div>
        </div>
        <div class="meta-row">
            <div class="meta-label">Jumlah Tagihan</div>
            <div class="meta-value highlight">Rp {{ number_format($pelanggan['jumlah_bayar'], 0, ',', '.') }}</div>
        </div>
        @if($pelanggan['tanggal_bayar'])
        <div class="meta-row">
            <div class="meta-label">Tanggal Bayar</div>
            <div class="meta-value">{{ $pelanggan['tanggal_bayar'] }}</div>
        </div>
        @endif
    </div>

    @if(isset($pelanggan['pembayaran_terakhir']) && count($pelanggan['pembayaran_terakhir']) > 0)
    <h3>Pembayaran Bulan Ini / Terakhir</h3>
    <table>
        <thead>
            <tr>
                <th>Bulan Tagihan</th>
                <th>Tanggal Bayar</th>
                <th>Keterangan</th>
                <th style="text-align: right;">Jumlah Bayar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pelanggan['pembayaran_terakhir'] as $item)
            <tr>
                <td>{{ $item['bulan'] }}</td>
                <td>{{ $item['tanggal_bayar'] }}</td>
                <td>
                    @if($item['is_tunggakan'])
                        <span style="color: #f0ad4e; font-weight: bold;">[TUNGGAKAN]</span>
                    @else
                        <span style="color: #2aa64c; font-weight: bold;">[TAGIHAN]</span>
                    @endif
                </td>
                <td style="text-align: right;">Rp {{ number_format($item['jumlah_bayar'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    @endif

    @if(isset($pelanggan['tunggakan']) && count($pelanggan['tunggakan']) > 0)
    <h3 style="color: #d9534f;">Tunggakan Belum Dibayar</h3>
    <div style="border: 2px solid #d9534f; border-radius: 8px; padding: 10px;">
        <p style="margin: 0; padding: 5px; color: #d9534f; font-weight: bold;">
            Anda memiliki tunggakan untuk bulan:
        </p>
        <ul style="margin-top: 5px; margin-bottom: 5px;">
            @foreach($pelanggan['tunggakan'] as $t)
                <li>{{ $t['bulan'] }}</li>
            @endforeach
        </ul>
        <p style="font-size: 11px; margin: 0; color: #d9534f; font-style: italic;">* Mohon segera selesaikan pembayaran.</p>
    </div>
    <br>
    @endif

    <h3>Riwayat Pemakaian Terakhir</h3>
    @if(count($pelanggan['riwayat']) > 0)
    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th style="text-align: right;">Pemakaian Air</th>
                <th style="text-align: right;">Total Biaya</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pelanggan['riwayat'] as $item)
            <tr>
                <td>{{ $item['bulan'] }}</td>
                <td style="text-align: right;">{{ $item['kubik'] }} m³</td>
                <td style="text-align: right;">Rp {{ number_format($item['rupiah'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="text-align: center; color: #999; padding: 20px; border: 1px dashed #ccc;">Belum ada data riwayat transaksi.</p>
    @endif

    <div class="stamp">
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>
        <br><br><br>
        <p>( Pengelola KP-SPAMS )</p>
    </div>

    <div class="footer">
        <p>Dokumen ini dicetak otomatis oleh sistem KP-SPAMS Desa Digital.</p>
    </div>
</body>
</html>
