<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use ArielMejiaDev\LarapexCharts\LarapexChart;

class DashboardController extends Controller
{
    public function index(Request $request): View
{
    $mainCategory = $request->input('main_category', 'all');
    $subCategory = $request->input('sub_category', 'all');
    $month = $request->input('month', date('m')); // Default bulan sekarang
    $year = $request->input('year', date('Y')); // Default tahun sekarang

    // Ambil semua kategori untuk dropdown
    $categories = Category::all();
    $plantSubCategories = $categories->where('type', 'tanaman')->pluck('name');
    $animalSubCategories = $categories->where('type', 'hewan')->pluck('name');

    // Query berdasarkan filter
    $query = Item::with('category')
    ->where(function ($q) use ($month, $year) {
        $q->whereMonth('created_at', $month)->whereYear('created_at', $year)
          ->orWhereMonth('updated_at', $month)->whereYear('updated_at', $year);
    });

    if ($mainCategory !== 'all') {
        $query->whereHas('category', function($q) use ($mainCategory) {
            $q->where('type', $mainCategory);
        });
        
        if ($subCategory !== 'all') {
            $query->whereHas('category', function($q) use ($subCategory) {
                $q->where('name', $subCategory);
            });
        }
    }

    $filteredItems = $query->get();

    // Buat chart
    $chart = (new LarapexChart)->barChart()
        ->setTitle("Produksi & Sisa Produk (Bulan: $month/$year)")
        ->setXAxis($filteredItems->pluck('name')->toArray())
        ->addData('Produksi', $filteredItems->pluck('produksi')->toArray())
        ->addData('Sisa', $filteredItems->pluck('sisa')->toArray())
        ->setColors(['#1E90FF', '#FFA500'])
        ->setOptions([
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => [8, 8, 0, 0], // Atas melengkung, bawah rata
                    'columnWidth' => '45%',
                ]
            ],
            // ... opsi lainnya
        ]);

    return view('dashboard.index', [
        'chart' => $chart,
        'mainCategory' => $mainCategory,
        'subCategory' => $subCategory,
        'month' => $month,
        'year' => $year,
        'plantSubCategories' => $plantSubCategories,
        'animalSubCategories' => $animalSubCategories,
        'filteredItems' => $filteredItems
    ]);
}
}
