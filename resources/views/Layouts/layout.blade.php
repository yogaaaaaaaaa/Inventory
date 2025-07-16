<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body { background-color: #f5f6fa; }
        .sidebar { width: 220px; background-color: #fff; height: 100vh; position: fixed; top: 0; left: 0; border-right: 1px solid #ddd; padding-top: 60px; }
        .sidebar a { display: block; padding: 10px 20px; color: #333; text-decoration: none; }
        .sidebar a:hover, .sidebar a.active { background-color: #00ff88; font-weight: bold; }
        .topbar { height: 60px; background-color: #fff; border-bottom: 1px solid #ddd; padding: 10px 20px; margin-left: 220px; display: flex; justify-content: space-between; align-items: center; }
        .main-content { margin-left: 220px; padding: 20px; }
        .chart-box { background-color: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

        /* Alert transition */
        .alert-dismiss-auto {
            transition: opacity 0.5s ease;
        }
        .pagination {
        --bs-pagination-padding-x: 0.5rem;
        --bs-pagination-padding-y: 0.25rem;
        --bs-pagination-font-size: 0.75rem;
        margin-bottom: 0;
    }
    .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    .page-link {
        color: #0d6efd;
        min-width: 30px;
        text-align: center;
    }
    .pagination-info {
        font-size: 0.75rem;
    }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="text-center mb-4">
            <img src="{{ asset('Images/logo-dkp.png') }}" width="100" alt="logo">
        </div>
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('items.barangMasuk', ['filter' => 'masuk']) }}" class="{{ request()->routeIs('items.barangMasuk') ? 'active' : '' }}">Barang Masuk</a>
        <a href="{{ route('items.tanaman', ['filter' => 'tanaman']) }}" class="{{ request()->routeIs('items.tanaman') ? 'active' : '' }}">Tanaman</a>
        <a href="{{ route('items.hewan', ['filter' => 'hewan']) }}" class="{{ request()->routeIs('items.hewan') ? 'active' : '' }}">Hewan</a>
        <a href="{{ route('wilayah.index', ['filter' => 'kecamatan']) }}" class="{{ request()->routeIs('wilayah.index') ? 'active' : '' }}">Wilayah</a>
        <a href="{{ route('items.barangkeluar', ['filter' => 'keluar']) }}" class="{{ request()->routeIs('items.barangkeluar') ? 'active' : '' }}">Barang Keluar</a>
        <a href="{{ route('export.index', ['filter' => 'export']) }}" class="{{ request()->routeIs('export.index') ? 'active' : '' }}">Export Excel</a>

        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>

    <div class="topbar">
        <div></div>
        <div>
            <p>{{ Auth::user()->name }}</p>
        </div>
    </div>

    <div class="main-content">
        {{-- Flash Message --}}
        @if(session('success'))
            <div id="flash-alert" class="alert alert-success alert-dismiss-auto alert-dismissible fade show" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div id="flash-alert" class="alert alert-danger alert-dismiss-auto alert-dismissible fade show" role="alert">
                {{ session('error') }}
            </div>
        @endif
        @if(session('info'))
            <div id="flash-alert" class="alert alert-info alert-dismiss-auto alert-dismissible fade show" role="alert">
                {{ session('info') }}
            </div>
        @endif

        @yield('content')
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const alertBox = document.getElementById('flash-alert');
        if (alertBox) {
            setTimeout(() => {
                alertBox.style.opacity = '0';
                setTimeout(() => {
                    alertBox.remove();
                }, 500);
            }, 3000); // hilang dalam 3 detik
        }
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function hapusData(id, nama) {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data " + nama + " akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-hapus-' + id).submit();
            }
        });
    }
</script>
    @yield('scripts')
</body>
</html>
