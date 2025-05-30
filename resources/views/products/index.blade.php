@extends('adminlte::page')

@section('title', 'Barang | ' . \App\Helpers\SettingHelper::companyName())

@section('content_header')
    <h1>Daftar Barang</h1>
@stop

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('products.create') }}" class="btn btn-primary">+ Tambah Barang</a>

    <!-- Button to trigger modal -->
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
        📥 Import Excel
    </button>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- Modal for Excel Upload --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="importModalLabel">Import Barang dari Excel</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="file" class="form-label">Pilih File Excel (.xlsx)</label>
            <input type="file" name="file" id="file" class="form-control" accept=".xlsx,.xls" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Import</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Kode SKU</th>
            <th>Total Stok</th>
            <th>Gudang</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $p)
        <tr>
            <td>{{ $p->name }}</td>
            <td>{{ $p->sku }}</td>
            <td>{{ $p->warehouses->sum('pivot.stock') }}</td>
            <td>
                @forelse($p->warehouses as $w)
                    <span class="badge bg-info">{{ $w->name }} ({{ $w->pivot->stock }})</span>
                @empty
                    <span class="text-muted">-</span>
                @endforelse
            </td>
            <td>
                <a href="{{ route('products.edit', $p->id) }}" class="btn btn-warning btn-sm">Edit</a>

                <a href="#" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#imageModal{{ $p->id }}">
                    Lihat Gambar
                </a>

                <form action="{{ route('products.destroy', $p->id) }}" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" onclick="return confirm('Yakin ingin hapus?')" class="btn btn-danger btn-sm">Hapus</button>
                </form>
            </td>
        </tr>

        <!-- Modal Gambar -->
        <div class="modal fade" id="imageModal{{ $p->id }}" tabindex="-1" aria-labelledby="imageModalLabel{{ $p->id }}" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Foto Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
              </div>
              <div class="modal-body text-center">
                @if($p->image)
                    <img src="{{ asset('storage/' . $p->image) }}" class="img-fluid rounded">
                @else
                    <p class="text-muted">Tidak ada gambar</p>
                @endif
              </div>
            </div>
          </div>
        </div>
        @endforeach
    </tbody>
</table>
@stop
