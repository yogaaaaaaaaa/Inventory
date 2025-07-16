@extends('Layouts.layout')

@section('content')
<h4 class="mb-3">Barang Masuk</h4>

<!-- Filter + Tambah Kategori -->
<div class="mb-3 d-flex justify-content-between align-items-center">
    <form method="GET" action="{{ route('items.barangMasuk') }}" class="d-flex flex-wrap gap-2 align-items-center">
        <!-- Category Filter -->
        <div class="me-2">
            <select name="category" class="form-select" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
                @endforeach
            </select>
        </div>

        <!-- Search Bar -->
        <div class="me-2">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
            </div>
        </div>

        <!-- Reset Button -->
        <div>
            <a href="{{ route('items.barangMasuk') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    @if (auth()->user()->role == 'admin')
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahItemModal">
            Tambah Data
        </button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahKategoriModal">
            Tambah Kategori
        </button>
    </div>
    @endif
</div>

<!-- Tabel -->
<div class="card p-3 shadow rounded bg-white">
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Produksi</th>
                    <th>Sisa</th>
                    @if (auth()->user()->role == 'admin')<th>Aksi</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td>{{ ($items->currentPage() - 1) * $items->perPage() + $loop->iteration }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category->name }}</td>
                    <td>{{ number_format($item->produksi) }}</td>
                    <td>{{ number_format($item->sisa) }}</td>
                    @if (auth()->user()->role == 'admin')
                    <td class="d-flex gap-2">
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#tambahProduksiModal{{ $item->id }}">
                            <i class="bi bi-plus-lg"></i> Tambah
                        </button>
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editProduksiModal{{ $item->id }}">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                    </td>
                    @endif
                </tr>

                <!-- Modal Tambah Produksi -->
                @if (auth()->user()->role == 'admin')
                <div class="modal fade" id="tambahProduksiModal{{ $item->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <form method="POST" action="{{ route('items.updateProduksi', $item->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Tambah Produksi: {{ $item->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label>Jumlah Tambahan Produksi</label>
                                        <input type="number" name="tambahan_produksi" class="form-control" min="1" required>
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
                @endif

                <!-- Modal Edit Produksi -->
                @if (auth()->user()->role == 'admin')
                <div class="modal fade" id="editProduksiModal{{ $item->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <form method="POST" action="{{ route('items.updateProduksi', $item->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Barang: {{ $item->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nama Barang</label>
                                        <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Kategori</label>
                                        <select name="category_id" class="form-select" required>
                                            @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $item->category_id == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="photo" class="form-label">Foto Barang (Kosongkan jika tidak diubah)</label>
                                        <input type="file" name="photo" class="form-control" accept="image/*">
                                        @if($item->photo)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/'.$item->photo) }}" width="80">
                                        </div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label>Total Produksi</label>
                                        <input type="number" name="produksi" class="form-control" value="{{ $item->produksi }}" min="0" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data tersedia.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if($items->hasPages())
<div class="d-flex justify-content-between align-items-center mt-3">
    <div class="text-muted small">
        Menampilkan {{ $items->firstItem() }} - {{ $items->lastItem() }} dari {{ $items->total() }} item
    </div>

    <ul class="pagination pagination-sm mb-0">
        <li class="page-item {{ $items->onFirstPage() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $items->previousPageUrl() }}" aria-label="Previous">
                <span aria-hidden="true">&lsaquo;</span>
            </a>
        </li>

        @foreach($items->getUrlRange(max(1, $items->currentPage()-2), min($items->lastPage(), $items->currentPage()+2)) as $page => $url)
        <li class="page-item {{ $page == $items->currentPage() ? 'active' : '' }}">
            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
        </li>
        @endforeach

        <li class="page-item {{ !$items->hasMorePages() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $items->nextPageUrl() }}" aria-label="Next">
                <span aria-hidden="true">&rsaquo;</span>
            </a>
        </li>
    </ul>
</div>
@endif

<!-- Modal Tambah Kategori -->
    @if (auth()->user()->role == 'admin')
    <div class="modal fade" id="tambahKategoriModal" tabindex="-1" aria-labelledby="tambahKategoriModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form action="{{ route('categories.store') }}" method="POST">
            @csrf
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="tambahKategoriModalLabel">Tambah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label for="kategori" class="form-label">Nama Kategori</label>
                  <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label for="type" class="form-label">Tipe</label>
                  <select name="type" class="form-select" required>
                    <option value="tanaman">Tanaman</option>
                    <option value="hewan">Hewan</option>
                  </select>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
              </div>
            </div>
        </form>
      </div>
    </div>
    @endif

    <!-- Modal Tambah Barang -->
    @if (auth()->user()->role == 'admin')
    <div class="modal fade" id="tambahItemModal" tabindex="-1" aria-labelledby="tambahItemModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="tambahItemModalLabel">Tambah Barang</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            {{-- Nama Barang --}}
            <div class="mb-3">
              <label for="name" class="form-label">Nama Barang</label>
              <input type="text" name="name" class="form-control" required>
            </div>

            {{-- Foto Barang --}}
            <div class="mb-3">
              <label for="photo" class="form-label">Foto Barang</label>
              <input type="file" name="photo" class="form-control" accept="image/*">
            </div>

            {{-- Kategori --}}
            <div class="mb-3">
              <label for="category_id" class="form-label">Kategori</label>
              <select name="category_id" class="form-select" required>
                <option value="">-- Pilih Kategori --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
              </select>
            </div>

            {{-- Produksi --}}
            <div class="mb-3">
              <label for="produksi" class="form-label">Produksi</label>
              <input type="number" name="produksi" class="form-control" required>
            </div>

            {{-- Sisa --}}
            <div class="mb-3">
              <label for="sisa" class="form-label">Sisa</label>
              <input type="number" class="form-control" name="sisa" value="0" required>
            </div>
          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Simpan</button>
          </div>
        </div>
    </form>
  </div>
</div>
    @endif

<!-- Style -->
<style>
    .pagination {
        margin-bottom: 0;
    }
    .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    .page-link {
        color: #0d6efd;
    }
</style>
@endsection
