<?php




use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionExportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;

Route::get('/', fn () => redirect('/login'));

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])->name('dashboard');

Route::get('/home', fn () => redirect()->route('dashboard'));

// Authenticated User Routes
Route::middleware(['auth'])->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Inventory (accessible to all logged-in users)
    Route::resource('warehouses', WarehouseController::class);
    Route::resource('products', ProductController::class);
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
    Route::resource('transactions', TransactionController::class);

    // Export
    Route::get('transactions/export/excel', [TransactionExportController::class, 'exportExcel'])->name('transactions.export.excel');
    Route::get('transactions/export/pdf', [TransactionExportController::class, 'exportPdf'])->name('transactions.export.pdf');

    // Dashboard charts
    Route::get('/dashboard/chart', [DashboardController::class, 'chart'])->name('dashboard.chart');
    Route::get('/dashboard/stok-chart', [DashboardController::class, 'stokChart'])->name('dashboard.stok-chart');
    Route::get('/dashboard/transaction-summary', [DashboardController::class, 'summaryChart'])->name('dashboard.summary-chart');
});


Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});


require __DIR__ . '/auth.php';
