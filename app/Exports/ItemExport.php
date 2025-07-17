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

        $notUpdatedItems = $previousItems->filter(function ($item) use ($currentUpdatedItems) {
            return !$currentUpdatedItems->pluck('id')->contains($item->id);
        });

        $finalItems = $currentUpdatedItems->concat($notUpdatedItems);

        $mergedItems = collect();

        foreach ($finalItems as $item) {
            $previous = $previousItems->firstWhere('id', $item->id);
            $sisaBulanLalu = (!empty($previous) && isset($previous->sisa)) ? (int) $previous->sisa : 0;

            $isUpdatedThisMonth = $currentUpdatedItems->pluck('id')->contains($item->id);

            $produksi = $isUpdatedThisMonth ? (int) ($item->produksi ?? 0) : null;
            $distribusi = $isUpdatedThisMonth ? (int) ($item->distribusi ?? 0) : null;
            $mati = $isUpdatedThisMonth ? (int) ($item->mati ?? 0) : null;

            $sisa = $isUpdatedThisMonth
                ? ($sisaBulanLalu + $produksi - $distribusi - $mati)
                : null;

            $mergedItems->push((object)[
                'name' => $item->name,
                'category' => $item->category,
                'sisa_bulan_kemarin' => $sisaBulanLalu,
                'produksi' => $produksi,
                'distribusi' => $distribusi,
                'mati' => $mati,
                'sisa' => $sisa,
            ]);
        }

        // Hitung total kolom
        return view('export.excel-view', [
            'mergedItems' => $mergedItems,
            'totalSisaBulanKemarin' => $mergedItems->sum('sisa_bulan_kemarin'),
            'totalProduksi' => $mergedItems->filter(fn($i) => isset($i->produksi))->sum('produksi'),
            'totalDistribusi' => $mergedItems->filter(fn($i) => isset($i->distribusi))->sum('distribusi'),
            'totalMati' => $mergedItems->filter(fn($i) => isset($i->mati))->sum('mati'),
            'totalSisa' => $mergedItems->filter(fn($i) => isset($i->sisa))->sum('sisa'),
        ]);
    }
}
