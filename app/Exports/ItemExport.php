<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Carbon\Carbon;

class ItemExport implements FromView
{
    protected $tipe, $kategori_id, $bulan, $tahun;

    public function __construct($tipe, $kategori_id, $bulan, $tahun)
    {
        $this->tipe = $tipe;
        $this->kategori_id = $kategori_id;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function view(): View
    {
        $currentDate = Carbon::create($this->tahun, $this->bulan, 1);
        $previousMonth = $currentDate->copy()->subMonth();

        // Ambil data BULAN SEBELUMNYA
        $previousItems = Item::with(['category'])
            ->when($this->tipe, function ($q) {
                $q->whereHas('category', function ($q) {
                    $q->where('type', $this->tipe);
                });
            })
            ->when($this->kategori_id, function ($q) {
                $q->where('category_id', $this->kategori_id);
            })
            ->whereMonth('created_at', $previousMonth->month)
            ->whereYear('created_at', $previousMonth->year)
            ->get();

        // Ambil data BULAN INI
        $currentMonthItems = Item::with(['category', 'kelurahan.kecamatan'])
            ->when($this->tipe, function ($q) {
                $q->whereHas('category', function ($q) {
                    $q->where('type', $this->tipe);
                });
            })
            ->when($this->kategori_id, function ($q) {
                $q->where('category_id', $this->kategori_id);
            })
            ->where(function ($q) { 
                $q->whereMonth('created_at', $this->bulan)
                  ->whereYear('created_at', $this->tahun)
                  ->orWhere(function ($q) {
                      $q->whereMonth('updated_at', $this->bulan)
                        ->whereYear('updated_at', $this->tahun);
                  });
            })
            ->get();

        // Koleksi untuk menggabungkan data
        $mergedItems = collect();

        // Hanya item yang memiliki catatan di bulan ini (dibuat atau diupdate) yang akan muncul.
        foreach ($currentMonthItems as $current) {
            $previous = $previousItems->firstWhere(fn($item) =>
                strtolower($item->name) == strtolower($current->name) &&
                $item->category_id == $current->category_id
            );

            $mergedItems->push((object)[
            'name' => $current->name,
            'category' => $current->category,
            'sisa_bulan_kemarin' => (int) ($previous?->sisa ?? 0),
            'produksi' => (int) $current->produksi,
            ]);
        }

        // Hitung total untuk semua kolom yang digabungkan
        return view('export.excel-view', [
            'mergedItems' => $mergedItems,
            'totalSisaBulanKemarin' => $mergedItems->sum('sisa_bulan_kemarin'),
            'totalProduksi' => $mergedItems->sum('produksi'),
        ]);
    }
}
