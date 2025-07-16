<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

   // Item.php
protected $fillable = ['name', 'category_id', 'produksi', 'sisa'];



    protected static function booted()
    {
        static::saving(function ($items) {
            $items->sisa = max(($items->produksi - $items->distribusi - $items->mati), 0);
        });
    }
    // Di Model Item
public function scopeCurrentMonthChanges($query, $month, $year)
{
    return $query->where(function ($q) use ($month, $year) {
        $q->where(function ($q) use ($month, $year) {
            $q->whereMonth('created_at', $month)
              ->whereYear('created_at', $year);
        })->orWhere(function ($q) use ($month, $year) {
            $q->whereMonth('updated_at', $month)
              ->whereYear('updated_at', $year)
              ->whereColumn('updated_at', '>', 'created_at');
        });
    });
}

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function kelurahan()
{
    return $this->belongsTo(Kelurahan::class);
}

    public function distribusis()
    {
    return $this->hasMany(Distribusi::class);
    }

    public function totalDistribusi()
    {
    return $this->distribusis()->sum('jumlah');
    }

}

