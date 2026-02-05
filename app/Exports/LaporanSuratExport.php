<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class LaporanSuratExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = DB::table('surat_masuk as sm');

        // Filter tanggal mulai
        if (isset($this->filters['tanggal_mulai']) && $this->filters['tanggal_mulai']) {
            $query->whereDate('sm.tanggal_surat', '>=', $this->filters['tanggal_mulai']);
        }

        // Filter tanggal akhir
        if (isset($this->filters['tanggal_akhir']) && $this->filters['tanggal_akhir']) {
            $query->whereDate('sm.tanggal_surat', '<=', $this->filters['tanggal_akhir']);
        }

        // Filter perihal
        if (isset($this->filters['perihal']) && $this->filters['perihal']) {
            $query->where('sm.perihal', 'like', "%{$this->filters['perihal']}%");
        }

        // Filter status
        $status = $this->filters['status'] ?? 'semua';
        if ($status !== 'semua') {
            if ($status === 'Sudah Dibalas') {
                $query->whereNotNull('sm.file_balasan');
            } else {
                $query->whereNull('sm.file_balasan');
            }
        }

        return $query->orderByDesc('sm.tanggal_surat')->get();
    }

    public function headings(): array
    {
        return [
            '#',
            'TANGGAL MASUK',
            'ASAL SURAT',
            'PERIHAL',
            'TANGGAL BALASAN',
            'STATUS',
            'FILE SURAT MASUK',
            'FILE SURAT KELUAR'
        ];
    }

    public function map($surat): array
    {
        $status = !empty($surat->file_balasan) ? '? Sudah Dibalas' : '? Belum Dibalas';
        $tanggalKeluar = $surat->tanggal_balasan
            ? Carbon::parse($surat->tanggal_balasan)->format('d/m/Y H:i')
            : '-';

        $fileSuratLabel = $surat->file_surat ? basename($surat->file_surat) : '-';
        $fileBalasanLabel = $surat->file_balasan ? basename($surat->file_balasan) : '-';
        $fileSuratUrl = $surat->file_surat ? url('storage/' . $surat->file_surat) : null;
        $fileBalasanUrl = $surat->file_balasan ? url('storage/' . $surat->file_balasan) : null;

        return [
            '', // No akan auto-generate
            Carbon::parse($surat->tanggal_surat)->format('d/m/Y H:i'),
            $surat->asal_surat ?? '',
            $surat->perihal ?? '',
            $tanggalKeluar,
            $status,
            $fileSuratUrl ? $this->buildHyperlink($fileSuratUrl, $fileSuratLabel) : $fileSuratLabel,
            $fileBalasanUrl ? $this->buildHyperlink($fileBalasanUrl, $fileBalasanLabel) : $fileBalasanLabel
        ];
    }

    private function buildHyperlink(string $url, string $label): string
    {
        $safeUrl = str_replace('"', '""', $url);
        $safeLabel = str_replace('"', '""', $label);
        return '=HYPERLINK("' . $safeUrl . '","' . $safeLabel . '")';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header styling
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C3E50']],
                'borders' => ['outline' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['rgb' => '000000']]],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ],
            // Status column (highlight sudah/belum dibalas)
            'F:F' => [
                'font' => ['bold' => true],
            ],
        ];
    }

    public function title(): string
    {
        return 'Laporan Surat Masuk BBPBL Lampung';
    }
}
