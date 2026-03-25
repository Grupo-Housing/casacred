@extends('layouts.web')

@section('header')
<title>Sitemap - CasaCredito</title>
<meta name="robots" content="noindex, nofollow" />
<style>
    body  { font-family: sans-serif; font-size: 14px; color: #222; background: #fff; }
    .wrap { max-width: 860px; margin: 0 auto; padding: 32px 20px 60px; }
    h1    { font-size: 20px; margin-bottom: 20px; border-bottom: 2px solid #222; padding-bottom: 8px; }
    h2    { font-size: 13px; margin: 26px 0 6px; text-transform: uppercase;
            letter-spacing: .05em; color: #555; border-left: 3px solid #1a3a5c;
            padding-left: 8px; }
    ul    { list-style: none; padding: 0; margin: 0; }
    li    { padding: 4px 0; border-bottom: 1px solid #f0f0f0; display: flex;
            align-items: baseline; gap: 10px; flex-wrap: wrap; }
    a     { color: #1a3a5c; text-decoration: none; font-size: 13px; }
    a:hover { text-decoration: underline; color: #c8442a; }
    .lbl  { color: #999; font-size: 11px; }
    .cnt  { font-size: 11px; color: #aaa; }
    .h-count { font-size: 12px; color: #999; font-weight: normal; margin-left: 6px; }
    input[type=text] {
        width: 100%; max-width: 380px; padding: 7px 12px;
        border: 1px solid #ccc; border-radius: 4px;
        font-size: 13px; margin-bottom: 16px; outline: none;
    }
    input[type=text]:focus { border-color: #1a3a5c; }
</style>
@endsection

@section('content')
@php
    $mainPages = [
        ['label' => 'Inicio',                       'url' => route('web.index')],
        ['label' => 'Nosotros',                     'url' => route('about.page')],
        ['label' => 'Contacto',                     'url' => route('contact.page')],
        ['label' => 'Equipo',                       'url' => route('team.index')],
        ['label' => 'Nuestros Servicios',           'url' => route('web.nuestros-servicios')],
        ['label' => 'Creditos',                     'url' => route('web.creditos')],
        ['label' => 'Blog',                         'url' => route('web.blog')],
        ['label' => 'Politicas de Privacidad',      'url' => route('web.politicas')],
        ['label' => 'Propiedades (todas)',           'url' => url('/propiedades-en-general')],
        ['label' => 'Landing Credito',              'url' => route('web.landing.credito')],
        ['label' => 'Notaria Queens NY',            'url' => route('web.notariausa')],
    ];

    $totalUrls = count($mainPages) + count($categoryUrls) + $listings->count();
@endphp

<div class="wrap">
    <h1>Sitemap &mdash; <span style="font-weight:normal;font-size:15px;color:#888">{{ $totalUrls }} URLs</span></h1>

    <input type="text" id="smSearch" placeholder="Buscar URL o nombre..." oninput="filterAll(this.value)">

    {{-- PAGINAS PRINCIPALES --}}
    <h2>Paginas principales <span class="h-count">({{ count($mainPages) }})</span></h2>
    <ul>
        @foreach($mainPages as $p)
        <li data-t="{{ strtolower($p['label'].' '.$p['url']) }}">
            <a href="{{ $p['url'] }}" target="_blank">{{ $p['url'] }}</a>
            <span class="lbl">{{ $p['label'] }}</span>
        </li>
        @endforeach
    </ul>

    {{-- CATEGORIAS DESDE LISTINGS REALES --}}
    <h2>Categorias de propiedades <span class="h-count">({{ count($categoryUrls) }})</span></h2>
    <ul>
        @foreach($categoryUrls as $cat)
        <li data-t="{{ strtolower($cat['label'].' '.$cat['url']) }}">
            <a href="{{ $cat['url'] }}" target="_blank">{{ $cat['url'] }}</a>
            <span class="lbl">{{ $cat['label'] }}</span>
        </li>
        @endforeach
    </ul>

    {{-- PROPIEDADES INDIVIDUALES --}}
    <h2>Propiedades individuales <span class="h-count">({{ $listings->count() }})</span></h2>
    <ul>
        @forelse($listings as $listing)
        @php $propUrl = route('web.detail', $listing->slug); @endphp
        <li data-t="{{ strtolower($listing->product_code.' '.$listing->listing_title.' '.$propUrl) }}">
            <a href="{{ $propUrl }}" target="_blank">{{ $propUrl }}</a>
            <span class="lbl">{{ $listing->product_code }} &mdash; {{ $listing->listing_title }}</span>
        </li>
        @empty
        <li><span class="lbl">Sin propiedades activas.</span></li>
        @endforelse
    </ul>
</div>

<script>
function filterAll(val) {
    val = val.toLowerCase().trim();
    document.querySelectorAll('li[data-t]').forEach(function(li) {
        li.style.display = (!val || li.dataset.t.includes(val)) ? '' : 'none';
    });
}
</script>
@endsection