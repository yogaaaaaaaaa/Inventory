<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'type' => 'required|in:tanaman,hewan'
    ]);

    Category::create([
        'name' => $request->name,
        'type' => $request->type
    ]);

    return back()->with('success', 'Kategori berhasil ditambahkan.');
}

}
