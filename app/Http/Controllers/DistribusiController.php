<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Kelurahan;
use App\Models\Kecamatan;
use App\Models\Distribusi;

class DistribusiController extends Controller
{
    public function index(Request $request)
{
    $query = Distribusi::with(['item.category', 'kelurahan.kecamatan'])
                ->orderBy('created_at', 'desc');

    // Filter berdasarkan kecamatan jika dipilih
    if ($request->has('kecamatan_id') && $request->kecamatan_id) {
        $query->whereHas('kelurahan', function($q) use ($request) {
            $q->where('kecamatan_id', $request->kecamatan_id);
        });
    }

    // Search functionality
    if ($request->has('search') && $request->search) {
        $searchTerm = $request->search;
        $query->where(function($q) use ($searchTerm) {
            $q->whereHas('item', function($q) use ($searchTerm) {
                $q->where('name', 'like', '%'.$searchTerm.'%');
            })
            ->orWhereHas('item.category', function($q) use ($searchTerm) {
                $q->where('name', 'like', '%'.$searchTerm.'%');
            })
            ->orWhereHas('kelurahan.kecamatan', function($q) use ($searchTerm) {
                $q->where('name', 'like', '%'.$searchTerm.'%');
            })
            ->orWhereHas('kelurahan', function($q) use ($searchTerm) {
                $q->where('name', 'like', '%'.$searchTerm.'%');
            });
        });
    }

    $distribusis = $query->paginate(10);
    $kecamatans = Kecamatan::all();

    return view('items.barangkeluar', compact('distribusis', 'kecamatans'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'jumlah' => 'required|integer|min:1',
    ]);

    $distribusi = Distribusi::findOrFail($id);
    $item = $distribusi->item;

    // Update distribusi dan sesuaikan kembali sisa, distribusi, produksi
    // Hitung selisih jumlah distribusi baru dengan yang lama
    $selisih = $request->jumlah - $distribusi->jumlah;

    // Cek apakah stok mencukupi
    if ($selisih > $item->sisa) {
        return back()->withErrors(['jumlah' => 'Jumlah distribusi baru melebihi sisa stok']);
    }

    // Update nilai distribusi
    $distribusi->jumlah = $request->jumlah;
    $distribusi->save();

    // Update stok item
    $item->distribusi += $selisih;
    $item->produksi -= $selisih;
    $item->sisa = $item->produksi - $item->distribusi - $item->mati;
    $item->save();

    return redirect()->back()->with('success', 'Distribusi berhasil diperbarui.');
}

public function destroy($id)
{
    $distribusi = Distribusi::findOrFail($id);
    $item = $distribusi->item;

    // Kembalikan jumlah ke stok
    $item->distribusi -= $distribusi->jumlah;
    $item->produksi += $distribusi->jumlah;
    $item->sisa = $item->produksi - $item->distribusi - $item->mati;
    $item->save();

    $distribusi->delete();

    return redirect()->back()->with('success', 'Distribusi berhasil dihapus.');
}


public function store(Request $request)
{
    $request->validate([
        'item_id' => 'required|exists:items,id',
        'jumlah' => 'required|integer|min:1',
    ]);

    $items = Item::findOrFail($request->item_id);

    // Validasi apakah stok mencukupi
    if ($request->jumlah > $items->sisa) {
        return back()->withErrors(['jumlah' => 'Jumlah distribusi melebihi sisa stok']);
    }

    // Simpan distribusi baru
    Distribusi::create([
        'item_id' => $items->id,
        'jumlah' => $request->jumlah,
        'kecamatan_id' => $request->kecamatan_id,
        'kelurahan_id' => $request->kelurahan_id,
    ]);

    // Update data item
    $items->distribusi += $request->jumlah;
    $items->produksi -= $request->jumlah;

    // Jika kamu menyimpan sisa di kolom tabel
    $items->sisa = $items->produksi - $items->distribusi - $items->mati;

    $items->save();

    return redirect()->back()->with('success', 'Distribusi berhasil ditambahkan.');
}

public function getKelurahan($kecamatan_id)
{
    // Pastikan kolom di tabel kelurahans memang bernama kecamatan_id
    $kelurahans = Kelurahan::where('kecamatan_id', $kecamatan_id)->get();
    return response()->json($kelurahans);
}

public function getItemsByKategori($kategori)
{
    // Cari kategori berdasarkan nama lengkap
    $category = Category::where('name', $kategori)->first();

    if ($category) {
        $items = Item::where('category_id', $category->id)->select('id', 'name')->get();
    } else {
        $items = [];
    }

    return response()->json($items);
}





}
