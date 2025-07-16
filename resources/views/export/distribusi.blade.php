<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kecamatan</th>
            <th>Kelurahan</th>
            <th>Nama Item</th>
            <th>Kategori</th>
            <th>Jumlah Distribusi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($distribusis as $i => $d)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $d->kelurahan->kecamatan->name ?? '-' }}</td>
                <td>{{ $d->kelurahan->name ?? '-' }}</td>
                <td>{{ $d->item->name ?? '-' }}</td>
                <td>{{ $d->item->category->name ?? '-' }}</td>
                <td>{{ $d->jumlah }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
