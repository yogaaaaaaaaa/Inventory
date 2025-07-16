<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Kelurahan extends Model
{
    protected $fillable = ['name', 'kecamatan_id'];


    public function store(Request $request)
{
    $request->validate([
        'kecamatan_id' => 'required|exists:kecamatans,id',
        'name' => 'required|string|max:255'
    ]);

    Kelurahan::create([
        'kecamatan_id' => $request->kecamatan_id,
        'name' => $request->name
    ]);

    return back()->with('success', 'Kelurahan berhasil ditambahkan.');
}


    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
