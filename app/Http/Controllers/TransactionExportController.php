<?php


namespace App\Http\Controllers;

use PDF;
use Excel;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Exports\TransactionsExport;

class TransactionExportController extends Controller
{
    public function exportExcel(Request $request)
    {
        $transactions = $this->getFilteredTransactions($request);
        return \Excel::download(new TransactionsExport($transactions), 'laporan-transaksi.xlsx');
    }
    
    public function exportPdf(Request $request)
    {
        $transactions = $this->getFilteredTransactions($request);
        $pdf = \PDF::loadView('exports.transactions', compact('transactions'));
        return $pdf->download('laporan-transaksi.pdf');
    }
    
    private function getFilteredTransactions(Request $request)
    {
        $query = \App\Models\Transaction::with(['product', 'warehouse'])->latest();
    
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
    
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }
    
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }
    
        return $query->get();
    }
}    
