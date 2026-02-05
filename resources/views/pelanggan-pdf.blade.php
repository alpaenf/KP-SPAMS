<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Pelanggan PAMSIMAS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header img {
            width: 60px;
            height: auto;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 16px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 11px;
            color: #666;
        }
        .info-section {
            margin-bottom: 15px;
            background: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
        }
        .info-row {
            display: flex;
            margin-bottom: 3px;
        }
        .info-label {
            width: 120px;
            font-weight: bold;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }
        .stat-card {
            background: #e8f5e9;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #c8e6c9;
        }
        .stat-card.blue {
            background: #e3f2fd;
            border-color: #bbdefb;
        }
        .stat-card.purple {
            background: #f3e5f5;
            border-color: #e1bee7;
        }
        .stat-card h3 {
            font-size: 10px;
            margin-bottom: 5px;
            color: #555;
        }
        .stat-card .value {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table thead {
            background: #4caf50;
            color: white;
        }
        table th {
            padding: 8px 5px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
        }
        table td {
            padding: 6px 5px;
            border-bottom: 1px solid #ddd;
            font-size: 9px;
        }
        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-success {
            background: #c8e6c9;
            color: #2e7d32;
        }
        .badge-danger {
            background: #ffcdd2;
            color: #c62828;
        }
        .badge-warning {
            background: #fff9c4;
            color: #f57f17;
        }
        .badge-info {
            background: #b3e5fc;
            color: #01579b;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">        <img src="{{ public_path('images/logo.png') }}" alt="Logo">
        <h1>KP-SPAMS DAMMAR WULAN</h1>        <h1>DATA PELANGGAN KP-SPAMS</h1>
        <p>Daftar Lengkap Pelanggan Air Bersih Desa</p>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Tanggal Cetak</div>
            <div>: {{ now()->locale('id')->isoFormat('dddd, D MMMM Y HH:mm') }} WIB</div>
        </div>
        @if($filters['search'])
        <div class="info-row">
            <div class="info-label">Pencarian</div>
            <div>: "{{ $filters['search'] }}"</div>
        </div>
        @endif
        @if($filters['status'] !== 'all')
        <div class="info-row">
            <div class="info-label">Status</div>
            <div>: {{ ucfirst($filters['status']) }}</div>
        </div>
        @endif
        @if($filters['wilayah'] !== 'all')
        <div class="info-row">
            <div class="info-label">Wilayah</div>
            <div>: {{ $filters['wilayah'] }}</div>
        </div>
        @endif
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Pelanggan</h3>
            <div class="value">{{ number_format($stats['total'], 0, ',', '.') }}</div>
        </div>
        <div class="stat-card blue">
            <h3>Pelanggan Aktif</h3>
            <div class="value">{{ number_format($stats['aktif'], 0, ',', '.') }}</div>
        </div>
        <div class="stat-card purple">
            <h3>Sudah Bayar Bulan Ini</h3>
            <div class="value">{{ number_format($stats['sudah_bayar'], 0, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th>ID</th>
                <th>Nama Pelanggan</th>
                <th>Alamat</th>
                <th class="text-center">RT/RW</th>
                <th>Wilayah</th>
                <th>No HP</th>
                <th class="text-center">Status</th>
                <th class="text-center">Bayar Bulan Ini</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pelanggan as $index => $p)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $p->id_pelanggan }}</td>
                <td>{{ $p->nama }}</td>
                <td>{{ $p->alamat }}</td>
                <td class="text-center">{{ $p->rt }}/{{ $p->rw }}</td>
                <td>{{ $p->wilayah ?? '-' }}</td>
                <td>{{ $p->no_whatsapp ?? '-' }}</td>
                <td class="text-center">
                    @if($p->status_aktif)
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-danger">Nonaktif</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($p->sudah_bayar)
                        <span class="badge badge-info">Sudah</span>
                    @else
                        <span class="badge badge-warning">Belum</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis dari Sistem Informasi KP-SPAMS</p>
        <p>© {{ date('Y') }} KP-SPAMS - Kelompok Pengelola Sistem Penyediaan Air Minum & Sanitasi</p>
    </div>
</body>
</html>
