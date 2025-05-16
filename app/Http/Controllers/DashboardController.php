<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Warehouse;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalWarehouses = Warehouse::count();

        $now = Carbon::now();
        $totalTransIn = Transaction::where('type', 'in')
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        $totalTransOut = Transaction::where('type', 'out')
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        return view('dashboard', compact(
            'totalProducts',
            'totalWarehouses',
            'totalTransIn',
            'totalTransOut'
        ));
    }

    public function chartData(Request $request): JsonResponse
    {
        $year = $request->year ?? date('Y');

        $months = collect(range(1, 12))->map(fn($m) => Carbon::create()->month($m)->format('F'));
        $dataIn = [];
        $dataOut = [];

        foreach (range(1, 12) as $m) {
            $dataIn[] = Transaction::where('type', 'in')
                ->whereMonth('created_at', $m)
                ->whereYear('created_at', $year)
                ->count();

            $dataOut[] = Transaction::where('type', 'out')
                ->whereMonth('created_at', $m)
                ->whereYear('created_at', $year)
                ->count();
        }

        return response()->json([
            'labels' => $months,
            'in' => $dataIn,
            'out' => $dataOut
        ]);
    }

    public function stokChart(): JsonResponse
    {
        $data = Warehouse::with('products')->get();

        $labels = [];
        $values = [];

        foreach ($data as $warehouse) {
            $labels[] = $warehouse->name;
            $values[] = $warehouse->products->sum('stock');
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values,
        ]);
    }
}
