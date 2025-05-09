@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-3 col-6">
            <x-adminlte-small-box title="Total Barang" text="{{ $totalProducts }}" theme="info" icon="fas fa-boxes"/>
        </div>
        <div class="col-lg-3 col-6">
            <x-adminlte-small-box title="Total Gudang" text="{{ $totalWarehouses }}" theme="success" icon="fas fa-warehouse"/>
        </div>
        <div class="col-lg-3 col-6">
            <x-adminlte-small-box title="Barang Masuk" text="{{ $totalTransIn }}" theme="primary" icon="fas fa-arrow-down"/>
        </div>
        <div class="col-lg-3 col-6">
            <x-adminlte-small-box title="Barang Keluar" text="{{ $totalTransOut }}" theme="danger" icon="fas fa-arrow-up"/>
        </div>
    </div>
@stop
