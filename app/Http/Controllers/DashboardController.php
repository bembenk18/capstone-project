<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
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
            'totalProducts', 'totalWarehouses', 'totalTransIn', 'totalTransOut'
        ));
    }

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
        return response()->json([ 'labels' => $labels, 'in' => $in, 'out' => $out ]);
    }

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
}
