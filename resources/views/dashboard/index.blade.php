@extends('Layouts.layout')

@section('content')
<div class="container">
    <h2 class="mb-4">Dashboard</h2>
    
    <div class="row mb-4">
    <div class="col-12">
        <div class="p-3 shadow rounded bg-white">
            <form method="GET" action="{{ route('dashboard') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label for="mainCategory">Kategori Utama:</label>
                        <select class="form-control" id="mainCategory" name="main_category">
                            <option value="all">Semua Kategori</option>
                            <option value="tanaman" {{ $mainCategory === 'tanaman' ? 'selected' : '' }}>Tanaman</option>
                            <option value="hewan" {{ $mainCategory === 'hewan' ? 'selected' : '' }}>Hewan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="subCategory">Sub Kategori:</label>
                        <select class="form-control" id="subCategory" name="sub_category">
                            <option value="all">Semua Sub Kategori</option>
                            @foreach($mainCategory === 'hewan' ? $animalSubCategories : $plantSubCategories as $category)
                                <option value="{{ $category }}" {{ $subCategory === $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="month">Bulan:</label>
                        <select class="form-control" id="month" name="month">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="year">Tahun:</label>
                        <select class="form-control" id="year" name="year">
                            @for($i = date('Y'); $i >= 2020; $i--)
                                <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


    

    <!-- Chart Section -->
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="p-3 shadow rounded bg-white">
            <div id="chart-container" style="height: 400px; overflow: hidden;">
                {!! $chart->container() !!}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    {{ $chart->script() }}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Update sub category dropdown
            document.getElementById('mainCategory').addEventListener('change', function() {
                // ... existing code ...
            });

            // Auto-submit form saat bulan/tahun berubah (opsional)
            document.getElementById('month').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
            document.getElementById('year').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
        });
    </script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof chart !== 'undefined') {
                // Update chart options
                chart.updateOptions({
                    chart: {
                        background: '#fff',
                        height: 350, // Set fixed height
                        width: '100%', // Responsive width
                        toolbar: {
                            show: true,
                            tools: {
                                download: true,
                                selection: false,
                                zoom: false,
                                zoomin: false,
                                zoomout: false,
                                pan: false,
                                reset: false
                            }
                        }
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 8,
                            columnWidth: '45%', // Lebar kolom lebih kecil
                            endingShape: 'rounded',
                            dataLabels: {
                                position: 'top'
                            }
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        labels: {
                            style: {
                                fontSize: '12px',
                                fontWeight: 600
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                fontSize: '12px'
                            }
                        }
                    },
                    grid: {
                        show: true,
                        borderColor: '#f0f0f0',
                        strokeDashArray: 3
                    },
                    responsive: [{
                        breakpoint: 768,
                        options: {
                            plotOptions: {
                                bar: {
                                    columnWidth: '60%'
                                }
                            }
                        }
                    }]
                });

                // Force chart to resize
                setTimeout(() => {
                    window.dispatchEvent(new Event('resize'));
                }, 200);
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (typeof chart !== 'undefined') {
                setTimeout(() => {
                    chart.updateOptions({
                        chart: {
                            width: document.getElementById('chart-container').offsetWidth
                        }
                    });
                }, 100);
            }
        });
    </script>
@endsection