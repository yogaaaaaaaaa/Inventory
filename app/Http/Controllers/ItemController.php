<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ItemController extends Controller
{
    public function barangMasuk(Request $request)
{
    // Ambil kategori yang hanya bertipe 'tanaman' atau 'hewan'
    $categories = Category::where('type', 'tanaman')
                        ->orWhere('type', 'hewan')
                        ->get();

    // Query untuk items dengan eager loading category
    $query = Item::with('category')
                ->whereHas('category', function ($q) {
                    $q->where('type', 'tanaman')->orWhere('type', 'hewan');
                });

    // Filter berdasarkan kategori jika dipilih
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }

    // Filter berdasarkan pencarian
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', '%'.$search.'%')
              ->orWhereHas('category', function($q) use ($search) {
                  $q->where('name', 'like', '%'.$search.'%');
              });
        });
    }

    // Pagination dengan 10 item per halaman
    $items = $query->paginate(10)->appends(request()->query());

    return view('items.index', compact('items', 'categories'));
}

public function updateProduksi(Request $request, Item $item)
{
    // Jika hanya ingin tambah produksi
    if ($request->filled('tambahan_produksi')) {
        $request->validate([
            'tambahan_produksi' => 'required|numeric|min:1',
        ]);

        $item->produksi += $request->tambahan_produksi;
        $item->sisa += $request->tambahan_produksi;
        $item->save();

        return redirect()->route('items.barangMasuk')->with('success', 'Produksi berhasil ditambahkan');
    }

    // Jika ingin edit lengkap
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'produksi' => 'required|integer|min:0',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $selisih = $validated['produksi'] - $item->produksi;
    $item->name = $validated['name'];
    $item->category_id = $validated['category_id'];
    $item->produksi = $validated['produksi'];
    $item->sisa += $selisih;

    // Jika ada foto baru
    if ($request->hasFile('photo')) {
        $filename = time() . '_' . $request->file('photo')->getClientOriginalName();
        $path = $request->file('photo')->storeAs('uploads/items', $filename, 'public');
        $item->photo = $path;
    }

    $item->save();

    return redirect()->route('items.barangMasuk')->with('success', 'Data barang berhasil diperbarui');
}



//Tanaman
public function Tanaman(Request $request)
{
    $kategori = $request->query('kategori');
    $search = $request->query('search');

    // Query dasar untuk items
    $query = Item::with('category')
                ->whereHas('category', function ($query) {
                    $query->where('type', 'tanaman');
                });

    // Filter berdasarkan kategori jika ada
    if ($kategori) {
        $query->whereHas('category', function ($query) use ($kategori) {
            $query->where('name', $kategori);
        });
    }

    // Filter berdasarkan pencarian
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', '%'.$search.'%')
              ->orWhereHas('category', function($q) use ($search) {
                  $q->where('name', 'like', '%'.$search.'%');
              });
        });
    }
    // Pagination dengan 10 item per halaman
    $items = $query->paginate(10);

    // Hitung total dari data yang terlihat di halaman saat ini
$total = [
    'sisa' => collect($items->items())->sum('sisa'),
    'produksi' => collect($items->items())->sum('produksi'),
    'distribusi' => collect($items->items())->sum('distribusi'),
    'mati' => collect($items->items())->sum('mati'),
];

    return view('items.tanaman', [
        'items' => $items,
        'total' => $total,
        'kategoriList' => Category::where('type', 'tanaman')->get(),
        'searchQuery' => $search
    ]);
}


//Hewan
public function hewan(Request $request) {
    $kategori = $request->query('kategori');
    $search = $request->query('search');

    // Query dasar untuk items
    $query = Item::with('category')
                ->whereHas('category', function ($query) {
                    $query->where('type', 'hewan');
                });

    // Filter berdasarkan kategori jika ada
    if ($kategori) {
        $query->whereHas('category', function ($query) use ($kategori) {
            $query->where('name', $kategori);
        });
    }

    // Filter berdasarkan pencarian
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', '%'.$search.'%')
              ->orWhereHas('category', function($q) use ($search) {
                  $q->where('name', 'like', '%'.$search.'%');
              });
        });
    }

    // Pagination dengan 10 item per halaman
    $items = $query->paginate(10);

    // Hitung total dari data yang terlihat di halaman saat ini
    $total = [
        'sisa' => collect($items->items())->sum('sisa'),
        'produksi' => collect($items->items())->sum('produksi'),
        'distribusi' => collect($items->items())->sum('distribusi'),
        'mati' => collect($items->items())->sum('mati'),
    ];

    return view('items.hewan', [
        'items' => $items,
        'total' => $total,
        'kategoriList' => Category::where('type', 'hewan')->get(),
        'searchQuery' => $search
    ]);
}

public function updateMati(Request $request, Item $item)
{
    $request->validate([
        'mati' => 'required|numeric|min:0',
    ]);

    $item->mati = $request->mati;
    $item->sisa = $item->produksi - $item->distribusi - $item->mati;
    $item->save();

     if ($item->category->type === 'tanaman') {
        return redirect()->route('items.tanaman')->with('success', 'Data berhasil diperbarui');
    } else {
        return redirect()->route('items.hewan')->with('success', 'Data berhasil diperbarui');
    }
}

public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'produksi' => 'required|integer|min:0',
        'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $item = new Item();
    $item->name = $validated['name'];
    $item->category_id = $validated['category_id'];
    $item->produksi = $validated['produksi'];
    $item->sisa = $validated['produksi']; // default: sisa awal = produksi
    $item->distribusi = 0;
    $item->mati = 0;

    // Simpan foto jika diunggah
    if ($request->hasFile('photo')) {
        $filename = time() . '_' . $request->file('photo')->getClientOriginalName();
        $path = $request->file('photo')->storeAs('uploads/items', $filename, 'public');
        $item->photo = $path;
    }

    $item->save();

    return redirect()->back()->with('success', 'Data berhasil ditambahkan.');
}


public function destroy($id)
{
    $item = Item::findOrFail($id);
    $item->delete();

    return back()->with('success', 'Data berhasil dihapus.');
}


}
