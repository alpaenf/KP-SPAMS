<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran - {{ $pembayaran['pelanggan_nama'] }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        /* ===== SCREEN BASE ===== */
        body {
            font-family: 'Courier New', Courier, monospace;
            color: #000;
            line-height: 1.5;
            padding: 20px;
            background-color: #f0f0f0;
        }

        /* ===== CONTROL PANEL (screen only) ===== */
        .control-panel {
            max-width: 600px;
            margin: 0 auto 16px;
            background: #fff;
            border-radius: 10px;
            padding: 16px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }
        .control-panel h3 {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #555;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .size-pills {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }
        .size-pill {
            font-family: Arial, sans-serif;
            font-size: 13px;
            padding: 6px 16px;
            border: 2px solid #ccc;
            border-radius: 20px;
            cursor: pointer;
            background: #fff;
            transition: all .2s;
            user-select: none;
        }
        .size-pill:hover { border-color: #1565c0; color: #1565c0; }
        .size-pill.active { background: #1565c0; color: #fff; border-color: #1565c0; font-weight: bold; }
        .size-pill .sub { font-size: 10px; opacity: .85; }
        .btn-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .print-button {
            font-family: Arial, sans-serif;
            color: white;
            padding: 10px 20px;
            font-size: 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            transition: all 0.2s;
            white-space: nowrap;
        }
        .print-button:hover { opacity: .88; box-shadow: 0 4px 8px rgba(0,0,0,0.25); }
        .btn-print  { background: #2e7d32; }
        .btn-bt     { background: #1565c0; }
        .btn-bt:disabled { background: #90a4ae; cursor: not-allowed; }
        .btn-close  { background: #c62828; }
        #bt-status {
            font-family: Arial, sans-serif;
            margin-top: 8px;
            font-size: 13px;
            font-weight: bold;
            min-height: 18px;
        }

        /* ===== STRUK WRAPPER ===== */
        .print-container {
            margin: 0 auto;
            background: white;
            padding: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
            transition: max-width .25s;
        }
        .struk-container {
            border: 2px dashed #555;
            padding: 12px;
        }

        /* ===== PAPER SIZE VARIANTS (screen preview) ===== */
        /* --- Normal --- */
        body.font-normal.size-58  .print-container { max-width: 220px;  font-size: 11px; }
        body.font-normal.size-80  .print-container { max-width: 304px;  font-size: 13px; }
        body.font-normal.size-a4  .print-container { max-width: 210mm;  font-size: 16px; }
        /* --- Besar (default untuk lansia) --- */
        body.font-besar.size-58   .print-container { max-width: 220px;  font-size: 14px; }
        body.font-besar.size-80   .print-container { max-width: 304px;  font-size: 16px; }
        body.font-besar.size-a4   .print-container { max-width: 210mm;  font-size: 20px; }

        /* ===== FONT SIZE TOGGLE PILLS ===== */
        .font-size-pill.active { background: #6a1b9a; color: #fff; border-color: #6a1b9a; font-weight: bold; }
        .font-size-pill:hover  { border-color: #6a1b9a; color: #6a1b9a; }

        /* ===== BASE TYPOGRAPHY — tebal & kontras tinggi untuk lansia ===== */
        .print-container { font-weight: 700; letter-spacing: .01em; line-height: 1.3; }

        /* ===== INNER ELEMENTS (em-based so they scale with container font-size) ===== */
        .header { text-align: center; margin-bottom: .5em; border-bottom: 3px solid #000; padding-bottom: .35em; }
        .header h1 { font-size: 1.5em; font-weight: 900; margin-bottom: .15em; letter-spacing: .03em; }
        .header p  { font-size: .95em; margin: .1em 0; font-weight: 700; }
        .struk-title { text-align: center; font-size: 1.25em; font-weight: 900; margin: .45em 0; text-transform: uppercase; letter-spacing: .05em; }
        .info-row    { display: table; width: 100%; margin-bottom: .2em; }
        .info-label  { display: table-cell; width: 42%; font-weight: 900; }
        .info-value  { display: table-cell; width: 58%; font-weight: 700; }
        .divider     { border-bottom: 2px dashed #222; margin: .35em 0; }
        .total-section { background: #efefef; padding: .45em .6em; margin: .4em 0; border-radius: 4px; border: 1px solid #bbb; }
        .total-row   { display: table; width: 100%; margin-bottom: .2em; }
        .total-label { display: table-cell; font-weight: 700; }
        .total-value { display: table-cell; text-align: right; font-weight: 900; }
        .grand-total { font-weight: 900; margin-top: .4em; padding-top: .35em; border-top: 4px double #000; border-bottom: 4px double #000; }
        .grand-total .total-label { font-size: 1.2em; font-weight: 900; letter-spacing: .03em; }
        .grand-total .total-value { font-size: 1.25em; font-weight: 900; }
        .footer { text-align: center; margin-top: .5em; padding-top: .4em; border-top: 3px solid #000; }
        .footer p { margin: .1em 0; font-weight: 700; }
        .status-badge { display: inline-block; padding: .2em 1em; border-radius: 4px; font-weight: 900; color: #000; background: transparent; border: 3px solid #000; font-size: 1.2em; letter-spacing: .1em; }
        .info-box { background: #f0f0f0; border: 2px solid #bbb; padding: .4em .6em; margin: .3em 0; border-radius: 4px; }

        /* ===== PRINT MEDIA ===== */
        @media print {
            body { background: white !important; padding: 0 !important; }
            .control-panel { display: none !important; visibility: hidden !important; height: 0 !important; overflow: hidden !important; pointer-events: none !important; }
            .print-container { box-shadow: none !important; padding: 0 !important; max-width: 100% !important; font-size: inherit !important; font-weight: 700; }
            .struk-container { border: 2px solid #000; }
        }
    </style>

    <!-- dynamic @page injected by JS based on selected size -->
    <style id="page-style">
        @page { size: 80mm auto; margin: 4mm; }
    </style>
</head>
<body class="size-{{ $ukuranKertas }} {{ $fontKelas }}">

    <!-- ===== CONTROL PANEL (disembunyikan saat share mode) ===== -->
    @unless($shareMode ?? false)
    <div class="control-panel">
        <h3 style="margin-bottom:6px;">Ukuran Huruf</h3>
        <div class="size-pills">
            <div class="size-pill font-size-pill" data-font="normal" onclick="setFontSize('normal')">
                Normal <span class="sub">(standar)</span>
            </div>
            <div class="size-pill font-size-pill active" data-font="besar" onclick="setFontSize('besar')">
                Besar <span class="sub">(lansia)</span>
            </div>
        </div>
        <div class="btn-row">
            <button class="print-button btn-print" onclick="window.print()">
                Cetak Struk
            </button>
            <button class="print-button btn-bt" id="bt-print-btn" onclick="printViaBluetooth()">
                Print Bluetooth
            </button>
            <button class="print-button btn-close" onclick="window.close()">
                Tutup
            </button>
        </div>
        <div id="bt-status"></div>
    </div>
    @endunless

    <div class="print-container">
        <div class="struk-container">
            <!-- Header -->
            <div class="header">
                <h1>KP-SPAMS</h1>
                <h1>"DAMAR WULAN"</h1>
            </div>

            <!-- Struk Title -->
            <div class="struk-title">
                STRUK PEMBAYARAN
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
                @if($pembayaran['meteran_sebelum'] && $pembayaran['meteran_sesudah'])
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

                <div class="total-row">
                    <div class="total-label">Pemakaian Air</div>
                    <div class="total-value">{{ number_format($pembayaran['jumlah_kubik'] ?? 0, 2, ',', '.') }} m³</div>
                </div>
                <div class="total-row">
                    <div class="total-label">Tarif per m³</div>
                    <div class="total-value">Rp {{ number_format($pembayaran['tarif_per_kubik'] ?? 2000, 0, ',', '.') }}</div>
                </div>
                <div class="total-row">
                    <div class="total-label">Subtotal</div>
                    <div class="total-value">Rp {{ number_format(($pembayaran['jumlah_kubik'] ?? 0) * ($pembayaran['tarif_per_kubik'] ?? 2000), 0, ',', '.') }}</div>
                </div>
                <div class="divider"></div>

                <div class="total-row">
                    <div class="total-label">Biaya Abonemen</div>
                    <div class="total-value">Rp {{ number_format($pembayaran['biaya_abunemen'] ?? 3000, 0, ',', '.') }}</div>
                </div>

                @if($pembayaran['tunggakan'] > 0)
                <div class="total-row">
                    <div class="total-label">Tunggakan</div>
                    <div class="total-value">Rp {{ number_format($pembayaran['tunggakan'], 0, ',', '.') }}</div>
                </div>
                @endif

                @if($pembayaran['keterangan'])
                <div class="total-row">
                    <div class="total-label">Keterangan</div>
                    <div class="total-value">{{ $pembayaran['keterangan'] }}</div>
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

            <div class="divider"></div>

            <!-- Status -->
            <div style="text-align: center; margin: 5px 0;">
                <span class="status-badge">{{ strtoupper($pembayaran['status_struk'] ?? 'BELUM BAYAR') }}</span>
            </div>

            @if(!empty($pembayaran['status_note']))
            <div style="text-align: center; margin: 0 0 6px 0; font-size: .85em; color: #b91c1c; font-weight: 700; font-family: 'Courier New', monospace;">
                {{ $pembayaran['status_note'] }}
            </div>
            @endif

            <!-- Tanggal Cetak -->
            <div style="text-align: center; margin-top: .4em; font-size: .85em; opacity: .75; font-family: 'Courier New', monospace;">
                Dicetak pada: {{ now()->format('d/m/Y H:i') }} WIB
            </div>
        </div>
    </div>
<script>
// ============================================================
// UKURAN KERTAS
// ============================================================
const PAGE_CONFIG = {
    '58':  { pageSize: '58mm auto',  margin: '3mm', btCols: 32 },
    '80':  { pageSize: '80mm auto',  margin: '4mm', btCols: 42 },
    'a4':  { pageSize: 'A4',         margin: '15mm', btCols: 48 },
};
let currentSize = '{{ $ukuranKertas }}';
let currentFont = 'besar';

// Apply the server-provided paper size on load
(function() {
    const cfg = PAGE_CONFIG[currentSize] || PAGE_CONFIG['80'];
    document.getElementById('page-style').textContent =
        `@page { size: ${cfg.pageSize}; margin: ${cfg.margin}; }`;
})();

// Sembunyikan control panel saat print (backup selain CSS @media print)
window.addEventListener('beforeprint', function() {
    const panel = document.querySelector('.control-panel');
    if (panel) panel.style.setProperty('display', 'none', 'important');
});
window.addEventListener('afterprint', function() {
    const panel = document.querySelector('.control-panel');
    if (panel) panel.style.removeProperty('display');
});

function setSize(size) {
    currentSize = size;
    document.body.classList.remove('size-58', 'size-80', 'size-a4');
    document.body.classList.add('size-' + size);
    const cfg = PAGE_CONFIG[size];
    document.getElementById('page-style').textContent =
        `@page { size: ${cfg.pageSize}; margin: ${cfg.margin}; }`;
    document.querySelectorAll('.size-pill:not(.font-size-pill)').forEach(el => {
        el.classList.toggle('active', el.dataset.size === size);
    });
}

function setFontSize(size) {
    currentFont = size;
    document.body.classList.remove('font-normal', 'font-besar');
    document.body.classList.add('font-' + size);
    document.querySelectorAll('.font-size-pill').forEach(el => {
        el.classList.toggle('active', el.dataset.font === size);
    });
}

// ============================================================
// BLUETOOTH THERMAL PRINTER (ESC/POS) via Web Bluetooth
// ============================================================
async function printViaBluetooth() {
    const statusEl = document.getElementById('bt-status');
    const btnEl    = document.getElementById('bt-print-btn');

    if (!navigator.bluetooth) {
        statusEl.style.color = '#c62828';
        statusEl.textContent = 'Browser tidak mendukung Bluetooth. Gunakan Chrome/Edge versi terbaru.';
        return;
    }

    const SERVICE_UUIDS = [
        '000018f0-0000-1000-8000-00805f9b34fb',
        'e7810a71-73ae-499d-8c15-faa9aef0c3f2',
        '49535343-fe7d-4ae5-8fa9-9fafd205e455',
        '0000ff00-0000-1000-8000-00805f9b34fb',
    ];
    const CHAR_UUIDS = {
        '000018f0-0000-1000-8000-00805f9b34fb': '00002af1-0000-1000-8000-00805f9b34fb',
        'e7810a71-73ae-499d-8c15-faa9aef0c3f2': 'bef8d6c9-9c21-4c9e-b632-bd58c1009f9f',
        '49535343-fe7d-4ae5-8fa9-9fafd205e455': '49535343-8841-43f4-a8d4-ecbe34729bb3',
        '0000ff00-0000-1000-8000-00805f9b34fb': '0000ff02-0000-1000-8000-00805f9b34fb',
    };

    btnEl.disabled       = true;
    statusEl.style.color = '#1565c0';
    statusEl.textContent = 'Mencari printer Bluetooth... (pilih printer di dialog browser)';

    let device;
    try {
        device = await navigator.bluetooth.requestDevice({
            acceptAllDevices: true,
            optionalServices: SERVICE_UUIDS,
        });
    } catch (err) {
        btnEl.disabled       = false;
        statusEl.style.color = '#c62828';
        statusEl.textContent = err.name === 'NotFoundError' ? 'Tidak ada printer dipilih.' : 'Error: ' + err.message;
        return;
    }

    try {
        statusEl.textContent = 'Menghubungkan ke "' + device.name + '"...';
        const server = await device.gatt.connect();

        let characteristic = null;
        const services = await server.getPrimaryServices();
        for (const svc of services) {
            const knownChar = CHAR_UUIDS[svc.uuid];
            try {
                if (knownChar) {
                    characteristic = await svc.getCharacteristic(knownChar);
                } else {
                    const chars = await svc.getCharacteristics();
                    for (const c of chars) {
                        if (c.properties.write || c.properties.writeWithoutResponse) {
                            characteristic = c; break;
                        }
                    }
                }
                if (characteristic) break;
            } catch (_) {}
        }

        if (!characteristic) {
            throw new Error('Karakteristik write printer tidak ditemukan. Pastikan printer ESC/POS kompatibel.');
        }

        statusEl.textContent = 'Mengirim data ke printer...';

        let cols = PAGE_CONFIG[currentSize].btCols || 42;
        const n = String(device.name || '').toLowerCase();
        if (n.includes('58')) cols = 32;
        else if (n.includes('80')) cols = 42;
        
        const data  = buildEscPosData(cols);
        const CHUNK = 100;
        for (let i = 0; i < data.length; i += CHUNK) {
            const chunk = data.slice(i, i + CHUNK);
            if (characteristic.properties.writeWithoutResponse) {
                await characteristic.writeValueWithoutResponse(chunk);
            } else {
                await characteristic.writeValue(chunk);
            }
            await new Promise(r => setTimeout(r, 100));
        }

        device.gatt.disconnect();
        statusEl.style.color = '#2e7d32';
        statusEl.textContent = 'Berhasil dicetak via Bluetooth!';

    } catch (err) {
        if (device && device.gatt.connected) device.gatt.disconnect();
        statusEl.style.color = '#c62828';
        statusEl.textContent = 'Error: ' + err.message;
    } finally {
        btnEl.disabled = false;
    }
}

// ============================================================
// ESC/POS DATA BUILDER
// cols = total character columns (32 for 58mm, 42 for 80mm, 48 for A4 mode)
// ============================================================
function buildEscPosData(cols = 42) {
    const ESC = 0x1B, GS = 0x1D, LF = 0x0A;
    const enc  = new TextEncoder();
    const parts = [];

    const cmd  = (...b) => parts.push(new Uint8Array(b));
    const txt  = t => parts.push(enc.encode(t));
    const line = (t = '') => { txt(t); cmd(LF); };
    const dash = (ch = '-') => line(ch.repeat(cols));
    // right-align value in same row (label left, value right)
    const row2 = (label, value) => {
        const space = cols - label.length - value.length;
        line(label + ' '.repeat(Math.max(1, space)) + value);
    };

    cmd(ESC, 0x40);            // Init
    cmd(ESC, 0x61, 0x01);      // Center
    cmd(ESC, 0x45, 0x01);      // Bold
    cmd(GS, 0x21, 0x11);       // Double size
    line('KP-SPAMS');
    cmd(GS, 0x21, 0x00);
    line('DAMAR WULAN');
    cmd(ESC, 0x45, 0x00);
    line();
    dash('=');
    cmd(ESC, 0x45, 0x01);
    line('*** STRUK PEMBAYARAN ***');
    cmd(ESC, 0x45, 0x00);
    dash('=');
    line();
    cmd(ESC, 0x61, 0x00);      // Left align
    line('ID Pelanggan : {{ $pembayaran['pelanggan_id'] }}');
    line('Nama         : {{ $pembayaran['pelanggan_nama'] }}');
    line('Alamat       : RT {{ $pembayaran['rt'] }}/RW {{ $pembayaran['rw'] }}');
    @if($pembayaran['no_whatsapp'])
    line('WhatsApp     : {{ $pembayaran['no_whatsapp'] }}');
    @endif
    line();
    line('No. Transaksi: #{{ str_pad($pembayaran['id'], 6, '0', STR_PAD_LEFT) }}');
    line('Tanggal Bayar: {{ $pembayaran['tanggal_bayar'] }}');
    line('Bulan Tagihan: {{ $pembayaran['bulan_bayar'] }}');
    line();
    dash();
    @if($pembayaran['meteran_sebelum'] && $pembayaran['meteran_sesudah'])
    row2('Mtr Sebelum', '{{ number_format($pembayaran['meteran_sebelum'], 0, ',', '.') }} m3');
    row2('Mtr Sesudah', '{{ number_format($pembayaran['meteran_sesudah'], 0, ',', '.') }} m3');
    dash();
    @endif
    row2('Pemakaian Air', '{{ number_format($pembayaran['jumlah_kubik'] ?? 0, 2, ',', '.') }} m3');
    row2('Tarif per m3', 'Rp {{ number_format($pembayaran['tarif_per_kubik'] ?? 2000, 0, ',', '.') }}');
    row2('Subtotal', 'Rp {{ number_format(($pembayaran['jumlah_kubik'] ?? 0) * ($pembayaran['tarif_per_kubik'] ?? 2000), 0, ',', '.') }}');
    row2('Biaya Abonemen', 'Rp {{ number_format($pembayaran['biaya_abunemen'] ?? 3000, 0, ',', '.') }}');
    @if($pembayaran['tunggakan'] > 0)
    row2('Tunggakan', 'Rp {{ number_format($pembayaran['tunggakan'], 0, ',', '.') }}');
    @endif
    @if($pembayaran['keterangan'])
    line('Keterangan: {{ $pembayaran['keterangan'] }}');
    @endif
    dash('=');
    cmd(ESC, 0x45, 0x01);
    cmd(GS, 0x21, 0x01);       // Double height for total
    row2('TOTAL BAYAR', 'Rp {{ number_format($pembayaran['jumlah_bayar'], 0, ',', '.') }}');
    cmd(GS, 0x21, 0x00);
    cmd(ESC, 0x45, 0x00);
    line();
    cmd(ESC, 0x61, 0x01);
    dash('=');
    cmd(ESC, 0x45, 0x01);
    line('*** LUNAS ***');
    cmd(ESC, 0x45, 0x00);
    dash('=');
    line();
    line('Dicetak: {{ now()->format('d/m/Y H:i') }} WIB');
    line(); line(); line();
    cmd(GS, 0x56, 0x00);       // Full paper cut

    const total = parts.reduce((s, p) => s + p.length, 0);
    const buf   = new Uint8Array(total);
    let   off   = 0;
    for (const p of parts) { buf.set(p, off); off += p.length; }
    return buf;
}
</script>
</body>
</html>
