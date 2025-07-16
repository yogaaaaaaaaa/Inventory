<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/Distribusi.php
// app/Models/Distribusi.php
class Distribusi extends Model
{
    protected $fillable = ['kelurahan_id', 'item_id', 'kategori', 'jumlah'];

    public function kelurahan() {
        return $this->belongsTo(Kelurahan::class);
    }

    public function item() {
        return $this->belongsTo(Item::class);
    }
}


