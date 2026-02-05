<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class LaporanExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithDrawings
{
    protected $data;
    protected $summary;
    protected $detail;
    protected $filters;

    public function __construct($data, $summary, $detail, $filters)
    {
        $this->data = $data;
        $this->summary = $summary;
        $this->detail = $detail;
        $this->filters = $filters;
    }

    public function collection()
    {
        $rows = collect();
        
        // Header Info
        $rows->push(['KP-SPAMS DAMMAR WULAN']);
        $rows->push(['LAPORAN KEUANGAN & OPERASIONAL']);
        $rows->push(['']);
        
        // Filter Info
        $bulanText = $this->filters['bulan'] === 'semua' ? 'Semua Bulan' : $this->getBulanName($this->filters['bulan']);
        $rows->push(['Periode', ': ' . $bulanText . ' ' . $this->filters['tahun']]);
        $rows->push(['Wilayah', ': ' . ($this->filters['wilayah'] === 'semua' ? 'Semua Wilayah' : $this->filters['wilayah'])]);
        $rows->push(['']);
        
        // Summary
        $rows->push(['RINGKASAN KEUANGAN']);
        $rows->push(['Total Pemasukan', 'Rp ' . number_format($this->summary['pemasukan'], 0, ',', '.')]);
        $rows->push(['Total Kubikasi', number_format($this->summary['kubikasi'], 2, ',', '.') . ' m³']);
        $rows->push(['Total Transaksi', number_format($this->summary['transaksi'], 0, ',', '.') . ' transaksi']);
        $rows->push(['']);
        
        // Detail Keuangan
        $rows->push(['DETAIL KEUANGAN']);
        $rows->push(['Total Tarikan (Kotor)', 'Rp ' . number_format($this->detail['totalTarikan'], 0, ',', '.')]);
        $rows->push(['20% Jasa Penarik', 'Rp ' . number_format($this->detail['tarik20Persen'], 0, ',', '.')]);
        $rows->push(['Biaya Operasional', 'Rp ' . number_format($this->detail['biayaOperasional'], 0, ',', '.')]);
        $rows->push(['Total Honor Penarik', 'Rp ' . number_format($this->detail['honorPenarik'], 0, ',', '.')]);
        $rows->push(['Total Tarikan Bersih', 'Rp ' . number_format($this->detail['totalTarikanBersih'], 0, ',', '.')]);
        $rows->push(['']);
        
        // Statistik SR
        $rows->push(['STATISTIK SAMBUNGAN RUMAH (SR)']);
        $rows->push(['Total SR Aktif', number_format($this->detail['totalSR'], 0, ',', '.')]);
        $rows->push(['SR Sudah Bayar', number_format($this->detail['srSudahBayar'], 0, ',', '.')]);
        $rows->push(['SR Belum Bayar', number_format($this->detail['srBelumBayar'], 0, ',', '.')]);
        $rows->push(['']);
        $rows->push(['']);
        
        // Data Pembayaran Table Header
        $rows->push(['DATA PEMBAYARAN PELANGGAN']);
        $rows->push([
            'No',
            'Tanggal Bayar',
            'ID Pelanggan',
            'Nama',
            'Alamat',
            'RT/RW',
            'Wilayah',
            'Bulan Bayar',
            'Jumlah Kubik',
            'Tarif/Kubik',
            'Abunemen',
            'Total Bayar'
        ]);
        
        // Data Pembayaran Rows
        $no = 1;
        foreach ($this->data as $pembayaran) {
            $pelanggan = $pembayaran->pelanggan;
            $rows->push([
                $no++,
                $pembayaran->tanggal_bayar->format('d/m/Y'),
                $pelanggan->id_pelanggan,
                $pelanggan->nama,
                $pelanggan->alamat,
                'RT ' . $pelanggan->rt . ' / RW ' . $pelanggan->rw,
                $pelanggan->wilayah ?? '-',
                \Carbon\Carbon::parse($pembayaran->bulan_bayar)->locale('id')->isoFormat('MMMM Y'),
                number_format($pembayaran->jumlah_kubik ?? 0, 2, ',', '.'),
                'Rp ' . number_format($pembayaran->tarif_per_kubik ?? 0, 0, ',', '.'),
                'Rp ' . number_format($pembayaran->abunemen ?? 0, 0, ',', '.'),
                'Rp ' . number_format($pembayaran->jumlah_bayar, 0, ',', '.')
            ]);
        }
        
        return $rows;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => 'center']
            ],
            3 => ['font' => ['bold' => true]],
            4 => ['font' => ['bold' => true]],
            7 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E8F5E9']
                ]
            ],
            12 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E3F2FD']
                ]
            ],
            19 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFF3E0']
                ]
            ],
        ];
    }

    public function title(): string
    {
        return 'Laporan Keuangan';
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo KP-SPAMS');
        $drawing->setPath(public_path('images/logo.png'));
        $drawing->setHeight(50);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(10);
        $drawing->setOffsetY(5);

        return $drawing;
    }

    private function getBulanName($bulan)
    {
        $bulanNames = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        return $bulanNames[$bulan] ?? $bulan;
    }
}
