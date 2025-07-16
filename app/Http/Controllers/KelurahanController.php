<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelurahan;
use App\Models\Kecamatan;

class KelurahanController extends Controller
{
    public function kelurahanIndex(Request $request)
    {
        $query = Kelurahan::with('kecamatan')->orderBy('name');

        // Filter berdasarkan kecamatan
        if ($request->has('kecamatan_id') && $request->kecamatan_id) {
            $query->where('kecamatan_id', $request->kecamatan_id);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                  ->orWhereHas('kecamatan', function($q) use ($search) {
                      $q->where('name', 'like', '%'.$search.'%');
                  });
            });
        }

        // Pagination
        $kelurahans = $query->paginate(10);
        $kecamatans = Kecamatan::all();
        
        return view('pages.kecamatan.index', [
            'kelurahans' => $kelurahans,
            'kecamatans' => $kecamatans,
            'currentKecamatanId' => $request->kecamatan_id,
            'searchQuery' => $request->search
        ]);
    }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'kecamatan_id' => 'required|exists:kecamatans,id',
    ]);

    Kelurahan::create([
        'name' => $request->name,
        'kecamatan_id' => $request->kecamatan_id,
    ]);

    return redirect()->route('wilayah.index')->with('success', 'Kelurahan berhasil ditambahkan.');
}

public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'kecamatan_id' => 'required|exists:kecamatans,id',
    ]);

    $kelurahan = Kelurahan::findOrFail($id);
    $kelurahan->update([
        'name' => $request->name,
        'kecamatan_id' => $request->kecamatan_id,
    ]);

    return redirect()->route('wilayah.index')->with('success', 'Kelurahan berhasil diperbarui.');
}

public function destroy($id)
{
    $kelurahan = Kelurahan::findOrFail($id);
    $kelurahan->delete();

    return redirect()->route('wilayah.index')->with('success', 'Kelurahan berhasil dihapus.');
}

}
