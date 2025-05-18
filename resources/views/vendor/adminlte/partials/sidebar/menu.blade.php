{{-- Menu Inventaris --}}
<li class="nav-header">Inventory</li>

<li class="nav-item">
    <a href="{{ route('warehouses.index') }}" class="nav-link">
        <i class="nav-icon fas fa-warehouse"></i>
        <p>Gudang</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('products.index') }}" class="nav-link">
        <i class="nav-icon fas fa-box"></i>
        <p>Barang</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('transactions.index') }}" class="nav-link">
        <i class="nav-icon fas fa-exchange-alt"></i>
        <p>Transaksi</p>
    </a>
</li>

{{-- Menu Pengaturan (hanya admin) --}}
@auth
    @if (auth()->user()->role === 'admin')
        <li class="nav-header">Pengaturan</li>

        <li class="nav-item">
            <a href="{{ route('users.index') }}" class="nav-link">
                <i class="nav-icon fas fa-user"></i>
                <p>User</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('settings.edit') }}" class="nav-link">
                <i class="nav-icon fas fa-cogs"></i>
                <p>Perusahaan</p>
            </a>
        </li>
    @endif
@endauth
