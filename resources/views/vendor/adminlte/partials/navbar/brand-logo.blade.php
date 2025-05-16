@php
    $setting = \App\Models\Setting::first();
@endphp

@if($setting && $setting->logo)
    <img src="{{ asset('storage/' . $setting->logo) }}" height="35" alt="Logo">
@else
    <b>{{ $setting->company_name ?? 'App' }}</b>
@endif
@php
    $setting = \App\Models\Setting::first();
    dd(asset('storage/' . $setting->logo));
@endphp
