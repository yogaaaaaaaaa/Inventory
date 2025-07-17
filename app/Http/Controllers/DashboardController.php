<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use ArielMejiaDev\LarapexCharts\LarapexChart;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        // Get filter input with fallback default
        $mainCategory = $request->input('main_category', 'all');
        $subCategory = $request->input('sub_category', 'all');
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        // Prepare category filter options
        $categories = Category::all();
        $plantSubCategories = $categories->where('type', 'tanaman')->pluck('name');
        $animalSubCategories = $categories->where('type', 'hewan')->pluck('name');

        // Build item query
        $itemsQuery = Item::with('category')
            ->where(function ($q) use ($month, $year) {
                $q->whereMonth('created_at', $month)
                  ->whereYear('created_at', $year)
                  ->orWhereMonth('updated_at', $month)
                  ->whereYear('updated_at', $year);
            });

        // Apply main category filter
        if ($mainCategory !== 'all') {
            $itemsQuery->whereHas('category', fn ($q) => $q->where('type', $mainCategory));

            // Apply subcategory filter if selected
            if ($subCategory !== 'all') {
                $itemsQuery->whereHas('category', fn ($q) => $q->where('name', $subCategory));
            }
        }

        $filteredItems = $itemsQuery->get();

        // Extract data for chart
        $itemNames = $filteredItems->pluck('name')->toArray();
        $produksiData = $filteredItems->pluck('produksi')->toArray();
        $sisaData = $filteredItems->pluck('sisa')->toArray();

        // Create bar chart
        $chart = (new LarapexChart)->barChart()
            ->setTitle("Produksi & Sisa Produk (Bulan: $month/$year)")
            ->setXAxis($itemNames)
            ->addData('Produksi', $produksiData)
            ->addData('Sisa', $sisaData)
            ->setColors(['#1E90FF', '#FFA500'])
            ->setOptions([
                'plotOptions' => [
                    'bar' => [
                        'borderRadius' => [8, 8, 0, 0],
                        'columnWidth' => '45%',
                    ]
                ]
            ]);

        return view('dashboard.index', [
            'chart' => $chart,
            'mainCategory' => $mainCategory,
            'subCategory' => $subCategory,
            'month' => $month,
            'year' => $year,
            'plantSubCategories' => $plantSubCategories,
            'animalSubCategories' => $animalSubCategories,
            'filteredItems' => $filteredItems,
        ]);
    }
}
