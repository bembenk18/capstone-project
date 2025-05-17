@extends('adminlte::page')

@section('title', 'Transaksi')

@section('content_header')
    <h1>Histori Transaksi Barang</h1>
@stop

@section('content')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('transactions.create') }}" class="btn btn-primary mb-3">+ Tambah Transaksi</a>

        {{-- Filter Form --}}
        <form method="GET" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label>Produk</label>
                    <select name="product_id" class="form-control">
                        <option value="">-- Semua Produk --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Gudang</label>
                    <select name="warehouse_id" class="form-control">
                        <option value="">-- Semua Gudang --</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>
                                {{ $w->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label>Jenis</label>
                    <select name="type" class="form-control">
                        <option value="">-- Semua Jenis --</option>
                        <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Masuk</option>
                        <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Keluar</option>
                    </select>
                </div>

                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success w-100">Filter</button>
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary w-100">Reset</a>
                </div>
            </div>
        </form>

        {{-- Export --}}
        <div class="mb-3 d-flex gap-2">
            <a href="{{ route('transactions.export.excel', request()->query()) }}" class="btn btn-success">
                Export Excel
            </a>
            <a href="{{ route('transactions.export.pdf', request()->query()) }}" class="btn btn-danger">
                Export PDF
            </a>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Produk</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Catatan</th>
                        <th>Waktu</th>
                        <th>Gudang</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                        <tr>
                            <td>{{ $t->product->name ?? '-' }}</td>
                            <td>
                                @if ($t->type === 'in')
                                    <span class="badge bg-success">Masuk</span>
                                @else
                                    <span class="badge bg-danger">Keluar</span>
                                @endif
                            </td>
                            <td>{{ $t->quantity }}</td>
                            <td>{{ $t->note ?? '-' }}</td>
                            <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $t->warehouse->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
