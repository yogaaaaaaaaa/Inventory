@extends('Layouts.layout')

@section('content')
    <h4 class="mb-4">Data Wilayah</h4>

    {{-- Filter dan Search --}}
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <div class="col-md-6">
            <form method="GET" action="{{ route('wilayah.index') }}" class="row g-2 align-items-center">
                <!-- Kecamatan Filter -->
                <div class="col-md-4">
                    <select name="kecamatan_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Kecamatan</option>
                        @foreach($kecamatans as $kecamatan)
                        <option value="{{ $kecamatan->id }}" 
                            {{ request('kecamatan_id') == $kecamatan->id ? 'selected' : '' }}>
                            {{ $kecamatan->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Search Bar dan Reset -->
                <div class="col-md-6 d-flex align-items-center gap-2">
                    <div class="input-group flex-grow-1">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari kecamatan/kelurahan..." value="{{ request('search') }}">
                    </div>
                    <div>
                        <a href="{{ route('wilayah.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
        
        {{-- Tombol Tambah --}}
        @auth
        @if (auth()->user()->role == 'admin')
        <div class="col-md-4">
            <div class="d-flex gap-2 justify-content-end">
                <button class="btn btn-primary" data-bs-toggle="modal" 
                        data-bs-target="#modalTambahKecamatan">
                    + Kecamatan
                </button>
                <button class="btn btn-primary text-white" data-bs-toggle="modal" 
                        data-bs-target="#modalTambahKelurahan">
                    + Kelurahan
                </button>
            </div>
        </div>
        @endif
        @endauth
    </div>

    <div class="card p-3 shadow rounded bg-white">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Kecamatan</th>
                        <th>Kelurahan</th>
                        @if (auth()->user()->role == 'admin')
                        <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kelurahans as $kelurahan)
                        <tr>
                            <td>{{ ($kelurahans->currentPage() - 1) * $kelurahans->perPage() + $loop->iteration }}</td>
                            <td>{{ $kelurahan->kecamatan->name }}</td>
                            <td>{{ $kelurahan->name }}</td>
                            @if (auth()->user()->role == 'admin')
                            <td>
                                <!-- Tombol Edit -->
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" 
                                        data-bs-target="#modalEditKelurahan{{ $kelurahan->id }}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <!-- Tombol Hapus -->
                                <form id="form-hapus-{{ $kelurahan->id }}" action="{{ route('kelurahan.destroy', $kelurahan->id) }}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm" onclick="hapusData('{{ $kelurahan->id }}', '{{ $kelurahan->name }}')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                                </form>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Tidak ada data ditemukan</td>
                        </tr>
                    
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($kelurahans->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Menampilkan {{ $kelurahans->firstItem() }} - {{ $kelurahans->lastItem() }} 
                dari {{ $kelurahans->total() }} data
            </div>
            <div>
                {{ $kelurahans->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>

    <!-- Modal Tambah Kecamatan -->
    @auth
    @if (auth()->user()->role == 'admin')
<div class="modal fade" id="modalTambahKecamatan" tabindex="-1" aria-labelledby="modalTambahKecamatanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('kecamatan.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kecamatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="nama_kecamatan" class="form-label">Nama Kecamatan</label>
                    <input type="text" name="name" id="nama_kecamatan" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>


    <!-- Modal Tambah Kelurahan -->
    <div class="modal fade" id="modalTambahKelurahan" tabindex="-1" aria-labelledby="modalTambahKelurahanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('kelurahan.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kelurahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="kecamatan_kelurahan" class="form-label">Kecamatan</label>
                    <select name="kecamatan_id" id="kecamatan_kelurahan" class="form-select" required>
                        <option value="">-- Pilih Kecamatan --</option>
                        @foreach($kecamatans as $kecamatan)
                            <option value="{{ $kecamatan->id }}">{{ $kecamatan->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="nama_kelurahan" class="form-label">Nama Kelurahan</label>
                    <input type="text" name="name" id="nama_kelurahan" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning text-white">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kelurahan -->
@foreach($kelurahans as $kelurahan)
   <div class="modal fade" id="modalEditKelurahan{{ $kelurahan->id }}" tabindex="-1" aria-labelledby="editKelurahanLabel{{ $kelurahan->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('kelurahan.update', $kelurahan->id) }}" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Kelurahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="kecamatan_edit{{ $kelurahan->id }}" class="form-label">Kecamatan</label>
                    <select name="kecamatan_id" class="form-select" required>
                        @foreach($kecamatans as $kecamatan)
                            <option value="{{ $kecamatan->id }}" {{ $kelurahan->kecamatan_id == $kecamatan->id ? 'selected' : '' }}>
                                {{ $kecamatan->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="nama_kelurahan_edit{{ $kelurahan->id }}" class="form-label">Nama Kelurahan</label>
                    <input type="text" name="name" class="form-control" value="{{ $kelurahan->name }}" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-info">Simpan Perubahan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>
@endforeach

    @endif
    @endauth

    <style>
        .pagination {
            margin-bottom: 0;
        }
        .table td, .table th {
            vertical-align: middle;
        }
    </style>
@endsection