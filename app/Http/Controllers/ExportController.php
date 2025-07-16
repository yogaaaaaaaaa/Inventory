<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ItemExport;
use App\Exports\DistribusiExport;

class ExportController extends Controller
{
public function index()
{
    return view('export.index', [
        'kategoriList' => Category::all(),
        'kecamatanList' => Kecamatan::all()
    ]);
}

public function exportProduksi(Request $r)
{
    return Excel::download(new ItemExport(
        $r->tipe,
        $r->kategori_id,
        $r->bulan,
        $r->tahun
    ), 'data-produksi.xlsx');
}

public function exportDistribusi(Request $r)
{
    return Excel::download(new DistribusiExport(
        $r->kecamatan_id,
        $r->bulan,
        $r->tahun
    ), 'data-distribusi.xlsx');
}


}