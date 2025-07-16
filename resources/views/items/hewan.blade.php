@extends('Layouts.layout')

@section('content')
<div class="art-box">
    <h4 class="mb-3">Data Hewan</h4>

    <!-- Filter dan Search -->
    <div class="row mb-3 align-items-center justify-content-between">
        <div class="col-md-5">
            <form method="GET" action="{{ route('items.hewan') }}" class="row g-2 align-items-center">
                <!-- Kategori Filter -->
                <div class="col-md-4">
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" 
                                data-bs-toggle="dropdown" style="max-width: 200px;">
                            {{ ucfirst(request('kategori') ?? 'Semua Kategori') }}
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item {{ request('kategori') == null ? 'active' : '' }}" 
                                  href="{{ route('items.hewan') }}">Semua</a></li>
                            <li><a class="dropdown-item {{ request('kategori') == 'Hewan Ternak' ? 'active' : '' }}" 
                                  href="{{ route('items.hewan', ['kategori' => 'Hewan Ternak']) }}">Ternak</a></li>
                            <li><a class="dropdown-item {{ request('kategori') == 'Hewan Budidaya' ? 'active' : '' }}" 
                                  href="{{ route('items.hewan', ['kategori' => 'Hewan Budidaya']) }}">Budidaya</a></li>
                        </ul>
                    </div>
                </div>
                <!-- Search Bar -->
                <div class="col-md-5">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari hewan/kategori..." value="{{ request('search') }}">
                    </div>
                </div>
                <!-- Reset Button -->
                <div class="col-md-3">
                    <a href="{{ route('items.hewan') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Tombol Tambah Data 
        @auth
        @if (auth()->user()->role == 'admin')
        <div class="col-md-4 mt-2 mt-md-0">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahHewanModal">
                Tambah Data
            </button>
        </div>
        @endif
        @endauth-->
    </div>

    <div class="card p-3 shadow rounded bg-white">
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Produksi</th>
                <th>Distribusi</th>
                <th>Mati</th>
                <th>Sisa</th>
                 @if (auth()->user()->role == 'admin')<th>Aksi</th>@endif
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->category->name }}</td>
                <td>{{ $item->produksi }}</td>
                <td>{{ $item->distribusi }}</td>
                <td>{{ $item->mati }}</td>
                <td>{{ $item->sisa }}</td>
                 @if (auth()->user()->role == 'admin')
                <td>
                    <!-- Tombol Edit Mati -->
                    <button class="btn btn-sm btn-warning mb-1" data-bs-toggle="modal"
                            data-bs-target="#editMatiModal{{ $item->id }}">Edit Mati</button>

                    <!-- Tombol Hapus -->
                    <form id="form-hapus-{{ $item->id }}" action="{{ route('items.destroy', $item->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusData({{ $item->id }}, '{{ $item->name }}')">Hapus</button>
                    </form>
                </td>
                @endif
            </tr>

            <!-- Modal Edit Mati -->
            @if (auth()->user()->role == 'admin')
            <div class="modal fade" id="editMatiModal{{ $item->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('items.updateMati', $item->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Mati: {{ $item->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <label>Mati</label>
                                <input type="number" name="mati" class="form-control"
                                       value="{{ $item->mati }}" required>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Simpan</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        </div>
            @endforeach
        </tbody>
         <tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-center">Total</th>
                    <td>{{ $total['produksi'] }}</td>
                    <td>{{ $total['distribusi'] }}</td>
                    <td>{{ $total['mati'] }}</td>
                    <td>{{ $total['sisa'] }}</td>
                    @if (auth()->user()->role == 'admin')<td></td>@endif
                </tr>
            </tfoot>
        </tbody>
    </table>

    <!-- Modal Tambah Data Hewan
    <div class="modal fade" id="tambahHewanModal" tabindex="-1" aria-labelledby="tambahHewanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('items.store') }}" method="POST">
                @csrf
        <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tambahTanamanModalLabel">Tambah Data Hewan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        
        <div class="modal-body">
          <div class="mb-3">
            <label for="name" class="form-label">Nama Hewan</label>
            <input type="text" class="form-control" name="name" required>
          </div>

          <div class="mb-3">
            <label for="category_id" class="form-label">Kategori</label>
            <select name="category_id" class="form-select" required>
              <option value="">-- Pilih Kategori --</option>
              <option value="5">Hewan Ternak</option>
              <option value="6">Hewan Budidaya</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="produksi" class="form-label">Produksi</label>
            <input type="number" class="form-control" name="produksi" required>
          </div>

          <div class="mb-3">
            <label for="sisa" class="form-label">Sisa</label>
            <input type="number" class="form-control" name="sisa" value="0" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Simpan</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </form>
  </div>
</div>
 -->
 <!-- Pagination dengan tetap mempertahankan total -->
        @if($items->hasPages())
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="text-muted small">
                    Menampilkan {{ $items->firstItem() }} - {{ $items->lastItem() }} dari {{ $items->total() }} item
                </div>
            </div>
            <div class="col-md-6">
                <nav class="float-end">
                    <ul class="pagination pagination-sm mb-0">
                        {{-- Previous Button --}}
                        <li class="page-item {{ $items->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $items->previousPageUrl() }}" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        {{-- Page Numbers --}}
                        @foreach ($items->getUrlRange(1, $items->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $items->currentPage() ? 'active' : '' }}">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach

                        {{-- Next Button --}}
                        <li class="page-item {{ !$items->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $items->nextPageUrl() }}" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        @endif
</div>
@endsection
