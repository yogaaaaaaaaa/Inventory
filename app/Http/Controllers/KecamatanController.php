<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kecamatan;

class KecamatanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        Kecamatan::create(['name' => $request->name]);

        return back()->with('success', 'Kecamatan berhasil ditambahkan.');
    }
}

