@extends('adminlte::page')



@section('title', 'Dashboard CICD | ' . \App\Helpers\SettingHelper::companyName())

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
<div class="row">
    @php
        $box = [
            ['color' => 'info', 'value' => $totalProducts, 'label' => 'Total Produk', 'icon' => 'fas fa-box'],
            ['color' => 'warning', 'value' => $totalWarehouses, 'label' => 'Total Gudang', 'icon' => 'fas fa-warehouse'],
            ['color' => 'success', 'value' => $totalTransIn, 'label' => 'Transaksi Masuk', 'icon' => 'fas fa-arrow-circle-down'],
            ['color' => 'danger', 'value' => $totalTransOut, 'label' => 'Transaksi Keluar', 'icon' => 'fas fa-arrow-circle-up'],
        ];
    @endphp
    @foreach ($box as $b)
        <div class="col-md-3">
            <div class="small-box bg-{{ $b['color'] }}">
                <div class="inner">
                    <h3>{{ $b['value'] }}</h3>
                    <p>{{ $b['label'] }}</p>
                </div>
                <div class="icon"><i class="{{ $b['icon'] }}"></i></div>
            </div>
        </div>
    @endforeach
</div>

@if($lowStockProducts->count())
<div class="alert alert-danger">
    <strong>⚠ Stok Menipis!</strong> Beberapa produk di bawah batas minimum:
    <ul class="mb-0">
        @foreach($lowStockProducts as $p)
            <li>{{ $p->name }} (Stok: {{ $p->stock }}, Minimum: {{ $p->minimum_stock }})</li>
        @endforeach
    </ul>
</div>
@endif

{{-- Grafik Ringkasan Transaksi + Pie Gudang --}}
<div class="row">
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                <span>Ringkasan Transaksi</span>
                <select id="rangeFilter" class="form-select w-auto bg-light text-dark">
                    <option value="daily">Harian</option>
                    <option value="weekly">Mingguan</option>
                    <option value="monthly">Bulanan</option>
                </select>
            </div>
            <div class="card-body chart-container">
                <canvas id="summaryChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header bg-secondary text-white">Stok Total per Gudang</div>
            <div class="card-body chart-container">
                <canvas id="stokChart"></canvas>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.chart-container {
    position: relative;
    height: 350px;
}
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let summaryChart, stokChart;

function loadSummaryChart(range = 'daily') {
    fetch(`{{ route('dashboard.summary-chart') }}?range=${range}`)
        .then(res => res.json())
        .then(data => {
            const ctx = document.getElementById('summaryChart').getContext('2d');
            if (summaryChart) summaryChart.destroy();
            summaryChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Masuk',
                            data: data.in,
                            borderColor: 'rgba(40, 167, 69, 1)',
                            backgroundColor: 'rgba(40, 167, 69, 0.2)',
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Keluar',
                            data: data.out,
                            borderColor: 'rgba(220, 53, 69, 1)',
                            backgroundColor: 'rgba(220, 53, 69, 0.2)',
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        });
}

function loadStokChart() {
    fetch('{{ route('dashboard.stok-chart') }}')
        .then(res => res.json())
        .then(data => {
            const ctx = document.getElementById('stokChart').getContext('2d');
            if (stokChart) stokChart.destroy();
            stokChart = new Chart(ctx, {
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
}

document.getElementById('rangeFilter').addEventListener('change', e => loadSummaryChart(e.target.value));

loadSummaryChart(); // default load
loadStokChart();
</script>
@stop
