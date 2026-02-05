<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PelangganExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected $pelanggan;
    protected $filters;

    public function __construct($pelanggan, $filters)
    {
        $this->pelanggan = $pelanggan;
        $this->filters = $filters;
    }

    public function collection()
    {
        $rows = collect();
        
        // Header
        $rows->push(['KP-SPAMS DAMAR WULAN']);
        $rows->push(['DATA PELANGGAN PAMSIMAS']);
        $rows->push(['']);
        
        // Filter info
        if (isset($this->filters['wilayah']) && $this->filters['wilayah'] !== 'semua') {
            $rows->push(['Wilayah', ': ' . $this->filters['wilayah']]);
        }
        if (isset($this->filters['status'])) {
            $rows->push(['Status', ': ' . ($this->filters['status'] === 'aktif' ? 'Aktif' : 'Nonaktif')]);
        }
        $rows->push(['']);
        
        // Column headers
        $rows->push([
            'No',
            'ID Pelanggan',
            'Nama',
            'Alamat',
            'RT/RW',
            'Wilayah',
            'No WhatsApp',
            'Kategori',
            'Status',
            'Tanggal Pasang',
            'Status Bayar Bulan Ini',
            'Tanggal Bayar',
            'Jumlah Bayar',
        ]);
        
        // Data rows
        $this->pelanggan->each(function ($p, $index) use ($rows) {
            $rows->push([
                $index + 1,
                $p->id_pelanggan,
                $p->nama,
                $p->alamat,
                'RT ' . $p->rt . ' / RW ' . $p->rw,
                $p->wilayah ?? '-',
                $p->no_whatsapp ?? '-',
                $p->kategori ?? '-',
                $p->status_aktif ? 'Aktif' : 'Nonaktif',
                $p->tanggal_pasang ? \Carbon\Carbon::parse($p->tanggal_pasang)->format('d/m/Y') : '-',
                $p->sudah_bayar ? 'Sudah Bayar' : 'Belum Bayar',
                $p->tanggal_bayar ? \Carbon\Carbon::parse($p->tanggal_bayar)->format('d/m/Y') : '-',
                $p->jumlah_bayar ? 'Rp ' . number_format($p->jumlah_bayar, 0, ',', '.') : '-',
            ]);
        });
        
        return $rows;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells for header
        $sheet->mergeCells('A1:M1');
        $sheet->mergeCells('A2:M2');
        
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => 'center']
            ],
            2 => [
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => 'center']
            ],
            5 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4CAF50']
                ],
                'alignment' => ['horizontal' => 'center']
            ],
        ];
    }

    public function title(): string
    {
        return 'Data Pelanggan';
    }
}
