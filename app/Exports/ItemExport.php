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
        $previousItems = Item::with('category')
            ->when($this->tipe, fn($q) =>
                $q->whereHas('category', fn($q) =>
                    $q->where('type', $this->tipe)))
            ->when($this->kategori_id, fn($q) =>
                $q->where('category_id', $this->kategori_id))
            ->where(function ($q) use ($previousDate) {
                $q->whereMonth('created_at', $previousDate->month)
                  ->whereYear('created_at', $previousDate->year)
                  ->orWhere(function ($q) use ($previousDate) {
                      $q->whereMonth('updated_at', $previousDate->month)
                        ->whereYear('updated_at', $previousDate->year);
                  });
            })
            ->get();

        // Ambil item yang diupdate bulan ini
        $currentUpdatedItems = Item::with(['category', 'kelurahan.kecamatan'])
            ->when($this->tipe, fn($q) =>
                $q->whereHas('category', fn($q) =>
                    $q->where('type', $this->tipe)))
            ->when($this->kategori_id, fn($q) =>
                $q->where('category_id', $this->kategori_id))
            ->whereMonth('updated_at', $currentDate->month)
            ->whereYear('updated_at', $currentDate->year)
            ->get();

        // Ambil sisa bulan lalu yang belum diupdate
        $notUpdatedItems = $previousItems->filter(function ($item) use ($currentUpdatedItems) {
            return !$currentUpdatedItems->pluck('id')->contains($item->id);
        });

        // Gabungkan item yang update + yang tidak update (sisa)
        $finalItems = $currentUpdatedItems->concat($notUpdatedItems);

        $mergedItems = collect();

        foreach ($finalItems as $item) {
            $previous = $previousItems->firstWhere('id', $item->id);

            $sisa = 0;
            if (!empty($previous) && is_object($previous)) {
                $sisa = isset($previous->sisa) ? (int) $previous->sisa : 0;
            }

            $mergedItems->push((object)[
                'name' => $item->name,
                'category' => $item->category,
                'sisa_bulan_kemarin' => $sisa,
                'produksi' => isset($item->produksi) ? (int) $item->produksi : 0,
            ]);
        }

        return view('export.excel-view', [
            'mergedItems' => $mergedItems,
            'totalSisaBulanKemarin' => $mergedItems->sum('sisa_bulan_kemarin'),
            'totalProduksi' => $mergedItems->sum('produksi'),
        ]);
    }
}
