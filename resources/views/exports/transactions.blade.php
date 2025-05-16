<table border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>Produk</th>
            <th>Jenis</th>
            <th>Jumlah</th>
            <th>Gudang</th>
            <th>Catatan</th>
            <th>Waktu</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $t)
            <tr>
                <td>{{ $t->product->name ?? '-' }}</td>
                <td>{{ $t->type == 'in' ? 'Masuk' : 'Keluar' }}</td>
                <td>{{ $t->quantity }}</td>
                <td>{{ $t->warehouse->name ?? '-' }}</td>
                <td>{{ $t->note }}</td>
                <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
