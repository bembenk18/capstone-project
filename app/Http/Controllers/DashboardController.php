<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalWarehouses = Warehouse::count();
        $totalTransIn = Transaction::whereRaw("`type` = 'in'")->count();
        $totalTransOut = Transaction::whereRaw("`type` = 'out'")->count();
        
        

        return view('dashboard', compact(
            'totalProducts',
            'totalWarehouses',
            'totalTransIn',
            'totalTransOut'
        ));
    }
}

