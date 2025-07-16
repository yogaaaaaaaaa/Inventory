<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Kecamatan extends Model
{
     protected $fillable = ['name'];

     public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255'
    ]);

    Kecamatan::create([
        'name' => $request->name
    ]);

    return back()->with('success', 'Kecamatan berhasil ditambahkan.');
}

    public function kelurahans()
    {
        return $this->hasMany(Kelurahan::class);
    }
    public function distribusis()
    {
        return $this->hasMany(Distribusi::class);
    }
}
