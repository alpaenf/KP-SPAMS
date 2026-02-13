<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan KP-SPAMS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
        }
        
        .header img {
            width: 70px;
            height: auto;
            margin-bottom: 10px;
        }
        
        .header h1 {
            font-size: 20px;
            color: #1e40af;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .header h2 {
            font-size: 14px;
            color: #64748b;
            font-weight: normal;
        }
        
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            width: 150px;
            padding: 5px 10px;
            font-weight: bold;
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
        }
        
        .info-value {
            display: table-cell;
            padding: 5px 10px;
            border: 1px solid #e2e8f0;
        }
        
        .summary-cards {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        
        .summary-card {
            display: table-cell;
            width: 33.33%;
            padding: 15px;
            text-align: center;
            border: 2px solid #e2e8f0;
            background-color: #f8fafc;
        }
        
        .summary-card.pemasukan {
            border-color: #10b981;
            background-color: #ecfdf5;
        }
        
        .summary-card.pengeluaran {
            border-color: #ef4444;
            background-color: #fef2f2;
        }
        
        .summary-card.saldo {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
        
        .summary-card h3 {
            font-size: 10px;
            color: #64748b;
            margin-bottom: 8px;
            text-transform: uppercase;
            font-weight: normal;
        }
        
        .summary-card .amount {
            font-size: 18px;
            font-weight: bold;
            color: #1e293b;
        }
        
        .summary-card.pemasukan .amount {
            color: #059669;
        }
        
        .summary-card.pengeluaran .amount {
            color: #dc2626;
        }
        
        .summary-card.saldo .amount {
            color: #2563eb;
        }
        
        .detail-section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #2563eb;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        table thead {
            background-color: #1e40af;
            color: white;
        }
        
        table th {
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            border: 1px solid #1e3a8a;
        }
        
        table td {
            padding: 8px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        
        table tbody tr:hover {
            background-color: #f1f5f9;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .font-semibold {
            font-weight: 600;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
        
        .no-data {
            text-align: center;
            padding: 30px;
            color: #64748b;
            font-style: italic;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo KP-SPAMS">
        <h1>LAPORAN KEUANGAN KP-SPAMS</h1>
        <h2>Periode: {{ $filters['bulan'] === 'semua' ? 'Tahun ' . $filters['tahun'] : $bulanName . ' ' . $filters['tahun'] }}</h2>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Periode</div>
            <div class="info-value">{{ $filters['bulan'] === 'semua' ? 'Tahun ' . $filters['tahun'] : $bulanName . ' ' . $filters['tahun'] }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Wilayah</div>
            <div class="info-value">{{ $filters['wilayah'] === 'semua' ? 'Semua Wilayah' : $filters['wilayah'] }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal Cetak</div>
            <div class="info-value">{{ now()->format('d/m/Y H:i') }} WIB</div>
        </div>
        <div class="info-row">
            <div class="info-label">Total Pelanggan Aktif</div>
            <div class="info-value">{{ number_format($totalPelangganAktif) }} Pelanggan</div>
        </div>
    </div>

    <div class="summary-cards">
        <div class="summary-card pemasukan">
            <h3>Total Pemasukan</h3>
            <div class="amount">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card pengeluaran">
            <h3>Total Pengeluaran</h3>
            <div class="amount">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card saldo">
            <h3>Saldo Akhir</h3>
            <div class="amount">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="detail-section">
        <div class="section-title">📥 Detail Pemasukan dari Pembayaran</div>
        
        @if(count($pembayarans) > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 15%;">Tanggal</th>
                        <th style="width: 12%;">ID Pelanggan</th>
                        <th style="width: 25%;">Nama Pelanggan</th>
                        <th style="width: 13%;">Bulan Bayar</th>
                        <th style="width: 15%;" class="text-right">Jumlah</th>
                        <th style="width: 15%;" class="text-center">Kategori</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pembayarans as $index => $p)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal_bayar)->format('d/m/Y') }}</td>
                            <td>{{ $p->pelanggan->id_pelanggan }}</td>
                            <td>{{ $p->pelanggan->nama_pelanggan }}</td>
                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $p->bulan_bayar)->locale('id')->isoFormat('MMMM Y') }}</td>
                            <td class="text-right font-semibold">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @if($p->pelanggan->kategori === 'sosial')
                                    <span class="badge badge-info">Sosial</span>
                                @else
                                    <span class="badge badge-success">Umum</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    <tr style="background-color: #dbeafe; font-weight: bold;">
                        <td colspan="5" class="text-right">TOTAL PEMASUKAN:</td>
                        <td class="text-right">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        @else
            <div class="no-data">Tidak ada data pembayaran untuk periode ini</div>
        @endif
    </div>

    <div class="detail-section">
        <div class="section-title">📤 Detail Pengeluaran Operasional</div>
        
        @if(count($laporanBulanan) > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 12%;">Bulan</th>
                        <th style="width: 10%;">Wilayah</th>
                        <th style="width: 13%;" class="text-right">Ops Penarik</th>
                        <th style="width: 13%;" class="text-right">PAD Desa</th>
                        <th style="width: 13%;" class="text-right">Ops Lapangan</th>
                        <th style="width: 10%;" class="text-right">Lain-lain</th>
                        <th style="width: 12%;" class="text-right">CSR</th>
                        <th style="width: 12%;" class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($laporanBulanan as $index => $laporan)
                        @php
                            $totalBiaya = ($laporan->biaya_operasional_penarik ?? 0) 
                                + ($laporan->biaya_pad_desa ?? 0)
                                + ($laporan->biaya_operasional_lapangan ?? 0)
                                + ($laporan->biaya_lain_lain ?? 0)
                                + ($laporan->biaya_csr ?? 0);
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $laporan->bulan)->locale('id')->isoFormat('MMMM Y') }}</td>
                            <td>{{ $laporan->wilayah }}</td>
                            <td class="text-right">Rp {{ number_format($laporan->biaya_operasional_penarik ?? 0, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($laporan->biaya_pad_desa ?? 0, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($laporan->biaya_operasional_lapangan ?? 0, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($laporan->biaya_lain_lain ?? 0, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($laporan->biaya_csr ?? 0, 0, ',', '.') }}</td>
                            <td class="text-right font-semibold">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr style="background-color: #fee2e2; font-weight: bold;">
                        <td colspan="8" class="text-right">TOTAL PENGELUARAN (Termasuk Honor Penarik 20%):</td>
                        <td class="text-right">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        @else
            <div class="no-data">Tidak ada data pengeluaran operasional untuk periode ini</div>
        @endif
    </div>

    <div class="footer">
        <p><strong>KP-SPAMS - Kelompok Pengelola Sistem Penyediaan Air Minum dan Sanitasi</strong></p>
        <p>Dokumen ini dicetak secara otomatis pada {{ now()->format('d/m/Y H:i:s') }} WIB</p>
    </div>
</body>
</html>
