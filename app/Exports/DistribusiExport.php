<?php

namespace App\Exports;

use App\Models\Item;
use App\Models\Distribusi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DistribusiExport implements FromView
{
    protected $kecamatan_id, $bulan, $tahun;

    public function __construct($kecamatan_id, $bulan, $tahun)
    {
        $this->kecamatan_id = $kecamatan_id;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function view(): View
    {
        $query = Distribusi::with(['kelurahan.kecamatan', 'item.category']);

        if ($this->kecamatan_id) {
            $query->whereHas('kelurahan', function ($q) {
                $q->where('kecamatan_id', $this->kecamatan_id);
            });
        }

        if ($this->bulan) {
            $query->whereMonth('created_at', $this->bulan);
        }

        if ($this->tahun) {
            $query->whereYear('created_at', $this->tahun);
        }

        return view('export.distribusi', [
            'distribusis' => $query->get()
        ]);
    }
}




