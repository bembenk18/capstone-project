<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $lowStockProducts = Product::whereColumn('stock', '<', 'minimum_stock')->get();

        $totalProducts = Product::count();
        $totalWarehouses = Warehouse::count();
        $totalTransIn = Transaction::where('type', 'in')
                            ->whereMonth('created_at', date('m'))
                            ->whereYear('created_at', date('Y'))
                            ->count();
        $totalTransOut = Transaction::where('type', 'out')
                            ->whereMonth('created_at', date('m'))
                            ->whereYear('created_at', date('Y'))
                            ->count();

        return view('dashboard', compact(
            'totalProducts',
            'totalWarehouses',
            'totalTransIn',
            'totalTransOut',
            'lowStockProducts'
        ));
    }

    // Grafik bulanan (default)
    public function chart(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $labels = [];
        $in = [];
        $out = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = date('F', mktime(0, 0, 0, $i, 10));
            $in[] = Transaction::where('type', 'in')->whereYear('created_at', $year)->whereMonth('created_at', $i)->count();
            $out[] = Transaction::where('type', 'out')->whereYear('created_at', $year)->whereMonth('created_at', $i)->count();
        }

        return response()->json(['labels' => $labels, 'in' => $in, 'out' => $out]);
    }

    // Grafik stok pie chart
    public function stokChart()
    {
        $warehouses = Warehouse::with('products')->get();
        $labels = [];
        $values = [];

        foreach ($warehouses as $w) {
            $labels[] = $w->name;
            $values[] = $w->products->sum('stock');
        }

        return response()->json(['labels' => $labels, 'values' => $values]);
    }

    // Grafik dinamis: harian, mingguan, bulanan
    public function summaryChart(Request $request)
    {
        $range = $request->get('range', 'daily');
        $labels = [];
        $in = [];
        $out = [];

        if ($range === 'daily') {
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $labels[] = $date->format('d M');
                $in[] = Transaction::whereDate('created_at', $date)->where('type', 'in')->count();
                $out[] = Transaction::whereDate('created_at', $date)->where('type', 'out')->count();
            }
        } elseif ($range === 'weekly') {
            for ($i = 3; $i >= 0; $i--) {
                $start = Carbon::now()->subWeeks($i)->startOfWeek();
                $end = Carbon::now()->subWeeks($i)->endOfWeek();
                $labels[] = $start->format('d M') . ' - ' . $end->format('d M');
                $in[] = Transaction::whereBetween('created_at', [$start, $end])->where('type', 'in')->count();
                $out[] = Transaction::whereBetween('created_at', [$start, $end])->where('type', 'out')->count();
            }
        } else {
            for ($i = 1; $i <= 12; $i++) {
                $labels[] = Carbon::create()->month($i)->format('F');
                $in[] = Transaction::whereYear('created_at', date('Y'))->whereMonth('created_at', $i)->where('type', 'in')->count();
                $out[] = Transaction::whereYear('created_at', date('Y'))->whereMonth('created_at', $i)->where('type', 'out')->count();
            }
        }

        return response()->json(['labels' => $labels, 'in' => $in, 'out' => $out]);
    }
}
