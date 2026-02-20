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
        
        @php
            // Filter dengan case-insensitive dan trim whitespace
            $pembayaranUmum = $pembayarans->filter(function($p) {
                $kategori = strtolower(trim($p->pelanggan->kategori ?? 'umum'));
                return $kategori === 'umum';
            });
            $pembayaranSosial = $pembayarans->filter(function($p) {
                $kategori = strtolower(trim($p->pelanggan->kategori ?? 'umum'));
                return $kategori === 'sosial';
            });
            $totalUmum = $pembayaranUmum->sum('jumlah_bayar');
            $totalSosial = $pembayaranSosial->sum('jumlah_bayar');
        @endphp
        
        @if(count($pembayarans) > 0)
            <!-- Ringkasan Per Kategori -->
            <table style="margin-bottom: 15px;">
                <thead>
                    <tr>
                        <th style="width: 50%;">Kategori</th>
                        <th style="width: 25%;" class="text-right">Jumlah Transaksi</th>
                        <th style="width: 25%;" class="text-right">Total Pemasukan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="badge badge-success">Umum</span></td>
                        <td class="text-right">{{ $pembayaranUmum->count() }} transaksi</td>
                        <td class="text-right font-semibold">Rp {{ number_format($totalUmum, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-info">Sosial</span></td>
                        <td class="text-right">{{ $pembayaranSosial->count() }} transaksi</td>
                        <td class="text-right font-semibold">Rp {{ number_format($totalSosial, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="background-color: #dbeafe; font-weight: bold;">
                        <td>TOTAL KESELURUHAN</td>
                        <td class="text-right">{{ $pembayarans->count() }} transaksi</td>
                        <td class="text-right">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
            
            <!-- Tabel Pelanggan Kategori UMUM -->
            @if($pembayaranUmum->count() > 0)
            <h4 style="font-size: 12px; font-weight: bold; color: #059669; margin: 15px 0 8px 0;">Pelanggan Kategori UMUM ({{ $pembayaranUmum->count() }} transaksi)</h4>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 12%;">Tanggal</th>
                        <th style="width: 12%;">ID Pelanggan</th>
                        <th style="width: 28%;">Nama Pelanggan</th>
                        <th style="width: 15%;">Bulan Bayar</th>
                        <th style="width: 13%;" class="text-right">Pemakaian</th>
                        <th style="width: 15%;" class="text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pembayaranUmum as $index => $p)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal_bayar)->format('d/m/Y') }}</td>
                            <td>{{ $p->pelanggan->id_pelanggan }}</td>
                            <td>{{ $p->pelanggan->nama_pelanggan }}</td>
                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $p->bulan_bayar)->locale('id')->isoFormat('MMMM Y') }}</td>
                            <td class="text-right">{{ number_format($p->jumlah_kubik ?? 0, 2, ',', '.') }} m³</td>
                            <td class="text-right font-semibold">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr style="background-color: #d1fae5; font-weight: bold;">
                        <td colspan="6" class="text-right">SUBTOTAL UMUM:</td>
                        <td class="text-right">Rp {{ number_format($totalUmum, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
            @endif
            
            <!-- Tabel Pelanggan Kategori SOSIAL -->
            @if($pembayaranSosial->count() > 0)
            <h4 style="font-size: 12px; font-weight: bold; color: #1e40af; margin: 15px 0 8px 0;">Pelanggan Kategori SOSIAL ({{ $pembayaranSosial->count() }} transaksi)</h4>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 12%;">Tanggal</th>
                        <th style="width: 12%;">ID Pelanggan</th>
                        <th style="width: 28%;">Nama Pelanggan</th>
                        <th style="width: 15%;">Bulan Bayar</th>
                        <th style="width: 13%;" class="text-right">Pemakaian</th>
                        <th style="width: 15%;" class="text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pembayaranSosial as $index => $p)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal_bayar)->format('d/m/Y') }}</td>
                            <td>{{ $p->pelanggan->id_pelanggan }}</td>
                            <td>{{ $p->pelanggan->nama_pelanggan }}</td>
                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $p->bulan_bayar)->locale('id')->isoFormat('MMMM Y') }}</td>
                            <td class="text-right">{{ number_format($p->jumlah_kubik ?? 0, 2, ',', '.') }} m³</td>
                            <td class="text-right font-semibold">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr style="background-color: #dbeafe; font-weight: bold;">
                        <td colspan="6" class="text-right">SUBTOTAL SOSIAL:</td>
                        <td class="text-right">Rp {{ number_format($totalSosial, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
            @endif
        @else
            <div class="no-data">Tidak ada data pembayaran untuk periode ini</div>
        @endif
    </div>

    @if(isset($distribusiWilayah) && count($distribusiWilayah) > 0)
    <div class="detail-section" style="page-break-before: auto;">
        <div class="section-title">🗺️ Tunggakan per Wilayah</div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">Wilayah</th>
                    <th style="width: 15%;" class="text-right">Total Pelanggan</th>
                    <th style="width: 15%;" class="text-right">Sudah Bayar</th>
                    <th style="width: 15%;" class="text-right">Belum Bayar</th>
                    <th style="width: 15%;" class="text-right">Tunggakan</th>
                    <th style="width: 10%;" class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($distribusiWilayah as $index => $wilayah)
                    @php
                        $persenLunas = $wilayah['jumlah'] > 0 ? round(($wilayah['sudah_bayar'] / $wilayah['jumlah']) * 100) : 0;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td><strong>{{ $wilayah['wilayah'] }}</strong></td>
                        <td class="text-right">{{ number_format($wilayah['jumlah'], 0, ',', '.') }}</td>
                        <td class="text-right"><span class="badge badge-success">{{ number_format($wilayah['sudah_bayar'], 0, ',', '.') }}</span></td>
                        <td class="text-right"><span class="badge" style="background-color: #fef3c7; color: #92400e;">{{ number_format($wilayah['belum_bayar'], 0, ',', '.') }}</span></td>
                        <td class="text-right"><span class="badge badge-danger">{{ number_format($wilayah['tunggakan'], 0, ',', '.') }}</span></td>
                        <td class="text-center">
                            @if($persenLunas >= 80)
                                <span class="badge badge-success">{{ $persenLunas }}%</span>
                            @elseif($persenLunas >= 50)
                                <span class="badge" style="background-color: #fef3c7; color: #92400e;">{{ $persenLunas }}%</span>
                            @else
                                <span class="badge badge-danger">{{ $persenLunas }}%</span>
                            @endif
                        </td>
                    </tr>
                    @if($wilayah['tunggakan'] > 0 && isset($wilayah['detail_tunggakan']) && count($wilayah['detail_tunggakan']) > 0)
                    <tr>
                        <td colspan="7" style="padding: 8px; background-color: #fef2f2;">
                            <div style="margin-left: 20px;">
                                <strong style="color: #991b1b; font-size: 9px;">Detail Pelanggan yang Menunggak:</strong>
                                <table style="width: 100%; margin-top: 5px; font-size: 8px;" cellpadding="4">
                                    <thead style="background-color: #fee2e2;">
                                        <tr>
                                            <th style="width: 5%; text-align: left; padding: 4px; border: 1px solid #fecaca;">No</th>
                                            <th style="width: 15%; text-align: left; padding: 4px; border: 1px solid #fecaca;">ID Pelanggan</th>
                                            <th style="width: 45%; text-align: left; padding: 4px; border: 1px solid #fecaca;">Nama Pelanggan</th>
                                            <th style="width: 15%; text-align: right; padding: 4px; border: 1px solid #fecaca;">Jumlah Bulan</th>
                                            <th style="width: 20%; text-align: right; padding: 4px; border: 1px solid #fecaca;">Total Tunggakan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($wilayah['detail_tunggakan'] as $idx => $detail)
                                        <tr style="background-color: {{ $idx % 2 == 0 ? '#fff' : '#fef2f2' }};">
                                            <td style="padding: 3px; border: 1px solid #fecaca; text-align: left;">{{ $idx + 1 }}</td>
                                            <td style="padding: 3px; border: 1px solid #fecaca; font-weight: bold;">{{ $detail['id_pelanggan'] }}</td>
                                            <td style="padding: 3px; border: 1px solid #fecaca;">{{ $detail['nama_pelanggan'] }}</td>
                                            <td style="padding: 3px; border: 1px solid #fecaca; text-align: right;">{{ $detail['jumlah_bulan'] }} bulan</td>
                                            <td style="padding: 3px; border: 1px solid #fecaca; text-align: right; font-weight: bold; color: #991b1b;">Rp {{ number_format($detail['total_tunggakan'], 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                        <tr style="background-color: #fee2e2; font-weight: bold;">
                                            <td colspan="4" style="padding: 4px; border: 1px solid #fecaca; text-align: right;">TOTAL:</td>
                                            <td style="padding: 4px; border: 1px solid #fecaca; text-align: right; color: #991b1b;">
                                                Rp {{ number_format(array_sum(array_column($wilayah['detail_tunggakan'], 'total_tunggakan')), 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    @endif
                @endforeach
                <tr style="background-color: #f1f5f9; font-weight: bold;">
                    <td colspan="2" class="text-right">TOTAL:</td>
                    <td class="text-right">{{ number_format(array_sum(array_column($distribusiWilayah, 'jumlah')), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format(array_sum(array_column($distribusiWilayah, 'sudah_bayar')), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format(array_sum(array_column($distribusiWilayah, 'belum_bayar')), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format(array_sum(array_column($distribusiWilayah, 'tunggakan')), 0, ',', '.') }}</td>
                    <td class="text-center">-</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

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
