<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $warehouse = Warehouse::firstOrCreate(['name' => $row['nama_gudang']]);

            $product = Product::firstOrCreate(
                ['sku' => $row['sku']],
                ['name' => $row['nama_produk'], 'stock' => 0]
            );

            $product->warehouses()->syncWithoutDetaching([
                $warehouse->id => ['stock' => $row['stok'] ?? 0]
            ]);
        }
    }
}
