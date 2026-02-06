<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Struk Pembayaran - {{ $pembayaran['pelanggan_nama'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            color: #000;
            line-height: 1.4;
            padding: 20px;
            max-width: 400px;
            margin: 0 auto;
        }
        .struk-container {
            border: 2px dashed #333;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 11px;
            margin: 2px 0;
        }
        .struk-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 15px 0;
            text-transform: uppercase;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
            font-size: 12px;
        }
        .info-label {
            display: table-cell;
            width: 40%;
            font-weight: bold;
        }
        .info-value {
            display: table-cell;
            width: 60%;
        }
        .divider {
            border-bottom: 1px dashed #333;
            margin: 10px 0;
        }
        .total-section {
            background-color: #f0f0f0;
            padding: 10px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        .total-label {
            display: table-cell;
            font-size: 12px;
        }
        .total-value {
            display: table-cell;
            text-align: right;
            font-size: 12px;
        }
        .grand-total {
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #333;
        }
        .grand-total .total-label {
            font-size: 14px;
        }
        .grand-total .total-value {
            font-size: 16px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #000;
            font-size: 11px;
        }
        .footer p {
            margin: 3px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            color: white;
            background-color: #2aa64c;
        }
        .info-box {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="struk-container">
        <!-- Header -->
        <div class="header">
            <h1>KP-SPAMS "DAMAR WULAN"</h1>
            <p>Air Bersih untuk Kehidupan Sehat</p>
            <p>Desa Ciwuni, Kec. Kesugihan, Kab. Cilacap</p>
        </div>

        <!-- Struk Title -->
        <div class="struk-title">
            *** STRUK PEMBAYARAN ***
        </div>

        <!-- Info Pelanggan -->
        <div class="info-box">
            <div class="info-row">
                <div class="info-label">ID Pelanggan</div>
                <div class="info-value">: {{ $pembayaran['pelanggan_id'] }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Nama</div>
                <div class="info-value">: {{ $pembayaran['pelanggan_nama'] }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Alamat</div>
                <div class="info-value">: RT {{ $pembayaran['rt'] }} / RW {{ $pembayaran['rw'] }}</div>
            </div>
            @if($pembayaran['no_whatsapp'])
            <div class="info-row">
                <div class="info-label">No. WhatsApp</div>
                <div class="info-value">: {{ $pembayaran['no_whatsapp'] }}</div>
            </div>
            @endif
        </div>

        <div class="divider"></div>

        <!-- Info Transaksi -->
        <div class="info-row">
            <div class="info-label">No. Transaksi</div>
            <div class="info-value">: #{{ str_pad($pembayaran['id'], 6, '0', STR_PAD_LEFT) }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal Bayar</div>
            <div class="info-value">: {{ $pembayaran['tanggal_bayar'] }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Bulan Tagihan</div>
            <div class="info-value">: {{ $pembayaran['bulan_bayar'] }}</div>
        </div>

        <div class="divider"></div>

        <!-- Rincian Pembayaran -->
        <div class="total-section">
            @if(!is_null($pembayaran['meteran_sebelum']) && !is_null($pembayaran['meteran_sesudah']))
            <div class="total-row">
                <div class="total-label">Meteran Sebelum</div>
                <div class="total-value">{{ number_format($pembayaran['meteran_sebelum'], 0, ',', '.') }} m³</div>
            </div>
            <div class="total-row">
                <div class="total-label">Meteran Sesudah</div>
                <div class="total-value">{{ number_format($pembayaran['meteran_sesudah'], 0, ',', '.') }} m³</div>
            </div>
            <div class="divider"></div>
            @endif

            @if(!is_null($pembayaran['jumlah_kubik']))
            <div class="total-row">
                <div class="total-label">Pemakaian Air</div>
                <div class="total-value">{{ number_format($pembayaran['jumlah_kubik'], 2, ',', '.') }} m³</div>
            </div>
            <div class="total-row">
                <div class="total-label">Tarif per m³</div>
                <div class="total-value">Rp {{ number_format($pembayaran['tarif_per_kubik'] ?? 2000, 0, ',', '.') }}</div>
            </div>
            <div class="total-row">
                <div class="total-label">Subtotal</div>
                <div class="total-value">Rp {{ number_format($pembayaran['jumlah_kubik'] * ($pembayaran['tarif_per_kubik'] ?? 2000), 0, ',', '.') }}</div>
            </div>
            <div class="divider"></div>
            @endif

            @if($pembayaran['abunemen'])
            <div class="total-row">
                <div class="total-label">Abunemen</div>
                <div class="total-value">Rp {{ number_format($pembayaran['biaya_abunemen'] ?? 3000, 0, ',', '.') }}</div>
            </div>
            @endif

            @if($pembayaran['tunggakan'] > 0)
            <div class="total-row">
                <div class="total-label">Tunggakan</div>
                <div class="total-value">Rp {{ number_format($pembayaran['tunggakan'], 0, ',', '.') }}</div>
            </div>
            @endif

            <!-- Grand Total -->
            <div class="grand-total">
                <div class="total-row">
                    <div class="total-label">TOTAL BAYAR</div>
                    <div class="total-value">Rp {{ number_format($pembayaran['jumlah_bayar'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        @if($pembayaran['keterangan'])
        <div class="divider"></div>
        <div class="info-row">
            <div class="info-label">Keterangan</div>
            <div class="info-value">: {{ $pembayaran['keterangan'] }}</div>
        </div>
        @endif

        <div class="divider"></div>

        <!-- Status -->
        <div style="text-align: center; margin: 15px 0;">
            <span class="status-badge">✓ LUNAS</span>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Terima kasih atas pembayaran Anda!</strong></p>
            <p>Struk ini adalah bukti pembayaran yang sah</p>
            <p>Simpan struk ini dengan baik</p>
            <p style="margin-top: 10px; font-size: 10px;">
                Dicetak pada: {{ now()->format('d/m/Y H:i') }} WIB
            </p>
        </div>
    </div>
</body>
</html>
