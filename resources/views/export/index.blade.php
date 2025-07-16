@extends('Layouts.layout')

@section('content')
<div class="art-box">
    <h4 class="mb-3">Export Data Tanaman & Hewan</h4>

    {{-- FORM EXPORT PRODUKSI --}}
    <form method="GET" action="{{ route('export.produksi') }}" class="row g-3 align-items-end mb-4">
        <div class="col-md-12"><h6>Export Produksi (Data dari Items)</h6></div>

        <div class="col-md-2">
            <label for="tipe_produksi" class="form-label">Tipe</label>
            <select name="tipe" id="tipe_produksi" class="form-control">
                <option value="">Semua</option>
                <option value="tanaman">Tanaman</option>
                <option value="hewan">Hewan</option>
            </select>
        </div>

        <div class="col-md-2">
            <label for="kategori_id_produksi" class="form-label">Kategori</label>
            <select name="kategori_id" id="kategori_id_produksi" class="form-control">
                <option value="">Semua</option>
                @foreach($kategoriList as $kategori)
                    <option value="{{ $kategori->id }}">{{ $kategori->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label for="bulan_produksi" class="form-label">Bulan</label>
            <select name="bulan" id="bulan_produksi" class="form-control">
                <option value="">Semua</option>
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}">{{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                @endfor
            </select>
        </div>

        <div class="col-md-2">
            <label for="tahun_produksi" class="form-label">Tahun</label>
            <select name="tahun" id="tahun_produksi" class="form-control">
                <option value="">Semua</option>
                @for ($i = now()->year; $i >= 2020; $i--)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>

        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Export Produksi</button>
        </div>
    </form>

    {{-- FORM EXPORT DISTRIBUSI --}}
    <form method="GET" action="{{ route('export.distribusi') }}" class="row g-3 align-items-end">
        <div class="col-md-12"><h6>Export Distribusi (Data dari Distribusi per Kelurahan)</h6></div>

        <div class="col-md-2">
            <label for="tipe_distribusi" class="form-label">Tipe</label>
            <select name="tipe" id="tipe_distribusi" class="form-control">
                <option value="">Semua</option>
                <option value="tanaman">Tanaman</option>
                <option value="hewan">Hewan</option>
            </select>
        </div>

        <div class="col-md-2">
            <label for="kategori_id_distribusi" class="form-label">Kategori</label>
            <select name="kategori_id" id="kategori_id_distribusi" class="form-control">
                <option value="">Semua</option>
                @foreach($kategoriList as $kategori)
                    <option value="{{ $kategori->id }}">{{ $kategori->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label for="kecamatan_id" class="form-label">Kecamatan</label>
            <select name="kecamatan_id" id="kecamatan_id" class="form-control">
                <option value="">Semua</option>
                @foreach($kecamatanList as $kecamatan)
                    <option value="{{ $kecamatan->id }}">{{ $kecamatan->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label for="bulan_distribusi" class="form-label">Bulan</label>
            <select name="bulan" id="bulan_distribusi" class="form-control">
                <option value="">Semua</option>
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}">{{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                @endfor
            </select>
        </div>

        <div class="col-md-2">
            <label for="tahun_distribusi" class="form-label">Tahun</label>
            <select name="tahun" id="tahun_distribusi" class="form-control">
                <option value="">Semua</option>
                @for ($i = now()->year; $i >= 2020; $i--)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>

        <div class="col-md-2">
            <button type="submit" class="btn btn-success w-100">Export Distribusi</button>
        </div>
    </form>
</div>
@endsection
