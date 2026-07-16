<?php

namespace App\Exports;

use App\Models\Registration;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RegistrationsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $jalurId;

    public function __construct($jalurId = null)
    {
        $this->jalurId = $jalurId;
    }

    public function collection()
    {
        $query = Registration::with(['student.user', 'jalurPendaftaran'])
            ->where('status', 'diterima')
            ->orderBy('nilai_rata_rata', 'desc');

        if ($this->jalurId) {
            $query->where('jalur_pendaftaran_id', $this->jalurId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No Pendaftaran',
            'NIK',
            'NISN',
            'Nama Lengkap',
            'Jalur Pendaftaran',
            'Nilai Rata-Rata',
            'Asal Sekolah',
            'No HP',
            'Alamat',
            'Status'
        ];
    }

    public function map($registration): array
    {
        return [
            $registration->no_pendaftaran,
            $registration->student->nik ?? '-',
            $registration->student->nisn ?? '-',
            $registration->student->user->name ?? '-',
            $registration->jalurPendaftaran->nama_jalur ?? '-',
            $registration->nilai_rata_rata,
            $registration->student->asal_sekolah ?? '-',
            $registration->student->no_hp ?? '-',
            $registration->student->alamat ?? '-',
            'Diterima'
        ];
    }
}
