@extends('Layouts.layout')

@section('content')

<h4 class="mb-3">Data Barang Keluar</h4>

{{-- Filter Kecamatan + Search + Reset --}}
<div class="row mb-3 align-items-center justify-content-between">
    <div class="col-auto d-flex align-items-center gap-2">
        <form method="GET" action="{{ route('items.barangkeluar') }}" class="d-flex align-items-center gap-2">
            {{-- Filter Kecamatan --}}
            <select name="kecamatan_id" class="form-select" style="max-width: 200px;" onchange="this.form.submit()">
                <option value="">Semua Kecamatan</option>
                @foreach($kecamatans as $kecamatan)
                    <option value="{{ $kecamatan->id }}" {{ request('kecamatan_id') == $kecamatan->id ? 'selected' : '' }}>
                        {{ $kecamatan->name }}
                    </option>
                @endforeach
            </select>
            
            {{-- Search Bar --}}
            <div class="input-group" style="width: 200px;">
                <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
            </div>
            
            {{-- Tombol Reset --}}
            <a href="{{ route('items.barangkeluar') }}" class="btn btn-secondary">Reset</a>
        </form>
    </div>

    {{-- Tombol Tambah --}}
    @auth
    @if (auth()->user()->role == 'admin')
    <div class="col-md-auto d-flex gap-2 mt-2 mt-md-0">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#distribusiModal">
            Tambah Distribusi
        </button>
    </div>
    @endif
    @endauth
</div>

{{-- Tabel Data --}}
<div class="card p-3 ">
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Kecamatan</th>
                <th>Kelurahan</th>
                <th>Nama Item</th>
                <th>Kategori</th>
                <th>Jumlah Distribusi</th>
                @if (auth()->user()->role == 'admin')
                <th>Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($distribusis as $index => $distribusi)
                <tr>
                    <td>{{ $distribusis->firstItem() + $index }}</td>
                    <td>{{ $distribusi->kelurahan->kecamatan->name }}</td>
                    <td>{{ $distribusi->kelurahan->name }}</td>
                    <td>{{ $distribusi->item->name }}</td>
                    <td>{{ $distribusi->item->category->name }}</td>
                    <td>{{ $distribusi->jumlah }}</td>
                    @if (auth()->user()->role == 'admin')
                    <td>
                        {{-- Tombol Edit --}}
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editDistribusiModal{{ $distribusi->id }}">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        {{-- Tombol Hapus --}}
                        <form id="form-hapus-{{ $distribusi->id }}" action="{{ route('distribusi.destroy', $distribusi->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-danger" onclick="hapusData('{{ $distribusi->id }}', '{{ $distribusi->item->name }}')">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                        </form>
                    </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="d-flex justify-content-center mt-3">
    {{ $distribusis->appends(request()->query())->links() }}
</div>



{{-- Modal Tambah Distribusi --}}
@auth
@if (auth()->user()->role == 'admin')
<div class="modal fade" id="distribusiModal" tabindex="-1" aria-labelledby="distribusiModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('distribusi.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="distribusiModalLabel">Tambah Distribusi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger m-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="modal-body">
                {{-- Kecamatan --}}
                <div class="mb-3">
                    <label for="kecamatan_id" class="form-label">Kecamatan</label>
                    <select name="kecamatan_id" id="kecamatan_id" class="form-select" required>
                        <option value="">-- Pilih Kecamatan --</option>
                        @foreach($kecamatans as $kecamatan)
                            <option value="{{ $kecamatan->id }}">{{ $kecamatan->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Kelurahan --}}
                <div class="mb-3">
                    <label for="kelurahan_id" class="form-label">Kelurahan</label>
                    <select name="kelurahan_id" id="kelurahan_id" class="form-select" required>
                        <option value="">-- Pilih Kelurahan --</option>
                    </select>
                </div>

                {{-- Kategori --}}
                <div class="mb-3">
                    <label for="kategori" class="form-label">Kategori</label>
                    <select name="kategori" id="kategori" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Tanaman Sayur">Tanaman Sayur</option>
                        <option value="Tanaman Obat">Tanaman Obat</option>
                        <option value="Tanaman Buah">Tanaman Buah</option>
                        <option value="Tanaman Hias">Tanaman Hias</option>
                        <option value="Hewan Ternak">Hewan Ternak</option>
                        <option value="Hewan Budidaya">Hewan Budidaya</option>
                    </select>
                </div>

                {{-- Item --}}
                <div class="mb-3">
                    <label for="item" class="form-label">Tanaman / Hewan</label>
                    <select name="item_id" id="item" class="form-select" required>
                        <option value="">-- Pilih Item --</option>
                    </select>
                </div>

                {{-- Jumlah --}}
                <div class="mb-3">
                    <label for="jumlah" class="form-label">Jumlah Distribusi</label>
                    <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" required>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>
@foreach ($distribusis as $distribusi)
<!-- Modal Edit Distribusi -->
<div class="modal fade" id="editDistribusiModal{{ $distribusi->id }}" tabindex="-1" aria-labelledby="editDistribusiModalLabel{{ $distribusi->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('distribusi.update', $distribusi->id) }}" method="POST" class="modal-content">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit Distribusi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Jumlah Distribusi</label>
          <input type="number" name="jumlah" value="{{ $distribusi->jumlah }}" class="form-control" min="1" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>
@endforeach

@endif
@endauth

{{-- Auto-show Modal jika error --}}
@if ($errors->any())
<script>
    $(document).ready(function () {
        $('#distribusiModal').modal('show');
    });
</script>
@endif

{{-- jQuery --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

{{-- AJAX Dropdown --}}
<script>
$(document).ready(function () {
    $('#kecamatan_id').on('change', function () {
        let id = $(this).val();
        if (id) {
            $.get('/getKelurahan/' + id, function (data) {
                $('#kelurahan_id').empty().append('<option value="">-- Pilih Kelurahan --</option>');
                data.forEach(kel => $('#kelurahan_id').append(`<option value="${kel.id}">${kel.name}</option>`));
            }).fail(err => console.error('Gagal ambil kelurahan', err));
        } else {
            $('#kelurahan_id').html('<option value="">-- Pilih Kelurahan --</option>');
        }
    });

    $('#kategori').on('change', function () {
        let kategori = $(this).val();
        if (kategori) {
            $.get('/items-by-kategori/' + kategori, function (data) {
                $('#item').empty().append('<option value="">-- Pilih Item --</option>');
                data.forEach(item => $('#item').append(`<option value="${item.id}">${item.name}</option>`));
            }).fail(err => console.error('Gagal ambil item', err));
        } else {
            $('#item').html('<option value="">-- Pilih Item --</option>');
        }
    });
});
</script>

@endsection