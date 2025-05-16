@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalProducts }}</h3>
                <p>Total Produk</p>
            </div>
            <div class="icon">
                <i class="fas fa-box"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $totalWarehouses }}</h3>
                <p>Total Gudang</p>
            </div>
            <div class="icon">
                <i class="fas fa-warehouse"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $totalTransIn }}</h3>
                <p>Transaksi Masuk (bulan ini)</p>
            </div>
            <div class="icon">
                <i class="fas fa-arrow-circle-down"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $totalTransOut }}</h3>
                <p>Transaksi Keluar (bulan ini)</p>
            </div>
            <div class="icon">
                <i class="fas fa-arrow-circle-up"></i>
            </div>
        </div>
    </div>
</div>

{{-- Filter tahun --}}
<div class="mb-3">
    <label for="chartYear">Tahun:</label>
    <select id="chartYear" class="form-control" style="width: 150px; display:inline-block;">
        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
            <option value="{{ $y }}">{{ $y }}</option>
        @endfor
    </select>
</div>

{{-- Grafik transaksi --}}
<div class="card mb-4">
    <div class="card-header bg-primary text-white">Grafik Transaksi Barang</div>
    <div class="card-body">
        <canvas id="transactionChart" height="90"></canvas>
    </div>
</div>

{{-- Grafik stok --}}
<div class="card mb-4">
    <div class="card-header bg-secondary text-white">Stok Total per Gudang</div>
    <div class="card-body" style="height: 400px;">
        <canvas id="stokChart"></canvas>
    </div>
</div>
@stop

@section('css')
<style>
    #stokChart {
        max-width: 400px;
        max-height: 400px;
        display: block;
        margin: auto;
    }

    .content-wrapper {
        min-height: 100vh !important;
    }

    .main-sidebar {
        background-color: #343a40 !important;
    }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function loadTransactionChart(year = new Date().getFullYear()) {
    fetch(`{{ route('dashboard.chart') }}?year=${year}`)
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('transactionChart').getContext('2d');
            if (window.transChart) window.transChart.destroy();
            window.transChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Masuk',
                            data: data.in,
                            borderColor: 'rgba(40, 167, 69, 0.9)',
                            fill: false
                        },
                        {
                            label: 'Keluar',
                            data: data.out,
                            borderColor: 'rgba(220, 53, 69, 0.9)',
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'top' } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        });
}

document.getElementById('chartYear').addEventListener('change', e => loadTransactionChart(e.target.value));
loadTransactionChart();

fetch('{{ route('dashboard.stok-chart') }}')
    .then(res => res.json())
    .then(data => {
        const ctx2 = document.getElementById('stokChart').getContext('2d');
        new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.values,
                    backgroundColor: [
                        '#007bff', '#28a745', '#ffc107', '#dc3545',
                        '#6610f2', '#20c997', '#fd7e14', '#6c757d',
                        '#17a2b8', '#e83e8c'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' }
                }
            }
        });
    });
</script>
@stop
