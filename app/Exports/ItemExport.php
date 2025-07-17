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
        $previousDate = $currentDate->copy()->subMonth();

        // Ambil semua item dari bulan sebelumnya
        $previousItems = Item::with(['category'])
            ->when($this->tipe, function ($q) {
                $q->whereHas('category', function ($q) {
                    $q->where('type', $this->tipe);
                });
            })
            ->when($this->kategori_id, function ($q) {
                $q->where('category_id', $this->kategori_id);
            })
            ->where(function ($q) use ($previousDate) {
                $q->whereMonth('created_at', $previousDate->month)
                  ->whereYear('created_at', $previousDate->year)
                  ->orWhere(function ($q) use ($previousDate) {
                      $q->whereMonth('updated_at', $previousDate->month)
                        ->whereYear('updated_at', $previousDate->year);
                  });
            })
            ->get();

        // Ambil semua item yang diupdate bulan ini
        $currentUpdatedItems = Item::with(['category', 'kelurahan.kecamatan'])
            ->when($this->tipe, function ($q) {
                $q->whereHas('category', function ($q) {
                    $q->where('type', $this->tipe);
                });
            })
            ->when($this->kategori_id, function ($q) {
                $q->where('category_id', $this->kategori_id);
            })
            ->whereMonth('updated_at', $currentDate->month)
            ->whereYear('updated_at', $currentDate->year)
            ->get();

        // Ambil sisa bulan kemarin yang belum diupdate bulan ini
        $notUpdatedItems = $previousItems->filter(function ($item) use ($currentUpdatedItems) {
            return !$currentUpdatedItems->pluck('id')->contains($item->id);
        });

        // Gabungkan data bulan ini (update) + sisa bulan lalu (belum update)
        $finalItems = $currentUpdatedItems->concat($notUpdatedItems);

        // Bentuk akhir untuk view Excel
        $mergedItems = collect();

        foreach ($finalItems as $item) {
            // Cari sisa bulan lalu untuk item ini
            $previous = $previousItems->firstWhere('id', $item->id);

            $mergedItems->push((object)[
                'name' => $item->name,
                'category' => $item->category,
                'sisa_bulan_kemarin' => (int) ($previous?->sisa ?? 0),
                'produksi' => (int) $item->produksi,
            ]);
        }

        return view('export.excel-view', [
            'mergedItems' => $mergedItems,
            'totalSisaBulanKemarin' => $mergedItems->sum('sisa_bulan_kemarin'),
            'totalProduksi' => $mergedItems->sum('produksi'),
        ]);
    }
}
