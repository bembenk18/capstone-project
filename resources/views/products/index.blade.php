@extends('adminlte::page')

@section('title', 'Barang')

@section('content_header')
    <h1>Daftar Barang</h1>
@stop
@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endpush

@section('content')
    <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">+ Tambah Barang</a>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Kode SKU</th>
                <th>Stok</th>
                <th>Gudang</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $p)
            <tr>
                <td>{{ $p->name }}</td>
                <td>{{ $p->sku }}</td>
                <td>{{ $p->stock }}</td>
                <td>{{ $p->warehouse->name ?? '-' }}</td>
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
    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Tutup">
        <span aria-hidden="true">&times;</span>
    </button>
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
