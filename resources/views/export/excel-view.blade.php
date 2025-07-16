<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Kategori</th>
            <th>Sisa Bulan Kemarin</th>
            <th>Produksi</th>
            <th>Sisa</th>
            <th>Distribusi</th>
            <th>Mati</th>
        </tr>
    </thead>
    <tbody>
        @foreach($mergedItems as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->name }}</td>
            <td>{{ $item->category->name }}</td>
            <td>{{ $item->sisa_bulan_kemarin }}</td>
            <td>{{ $item->produksi }}</td>
            <td>{{ $item->sisa }}</td>
            <td>{{ $item->distribusi }}</td>
            <td>{{ $item->mati }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="3"><strong>Total</strong></td>
            <td><strong>{{ $totalSisaBulanKemarin }}</strong></td>
            <td><strong>{{ $totalProduksi }}</strong></td>
            <td><strong>{{ $totalSisa }}</strong></td>
            <td><strong>{{ $totalDistribusi }}</strong></td>
            <td><strong>{{ $totalMati }}</strong></td>
        </tr>
    </tbody>
</table>