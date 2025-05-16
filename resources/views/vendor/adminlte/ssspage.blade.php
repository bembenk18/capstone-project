@php
    try {
        $company = \App\Models\Setting::first()->company_name ?? 'App';
    } catch (\Throwable $e) {
        $company = 'App';
    }
@endphp
<title>@yield('title') | {{ $company }}</title>
