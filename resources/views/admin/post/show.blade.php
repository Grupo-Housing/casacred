@extends('layouts.web')
@section('header')
    <title>{{$post->title_google}}</title>
    <meta name="description" content="{{$post->metadescription}}">
    @if($post->keywords!=null) <meta name="keywords" content="{{$post->keywords}}"> @endif
    <meta property="og:title" content="{{$post->title_google}}">
    <meta property="og:description" content="{{$post->metadescription}}">
    <meta property="og:image" content="{{asset('uploads/posts/'.$post->first_image)}}">
    <link rel="canonical" href="{{ Request::url() }}" />
    <meta name="robots" content="INDEX, FOLLOW, SNIPPET" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand:       #1b3460;
            --brand-mid:   #2d4f8a;
            --brand-light: #e8edf5;
            --text:        #1a1a2e;
            --muted:       #6b7280;
            --border:      rgba(27,52,96,0.12);
            --surface:     #ffffff;
            --bg:          #f5f6fa;
            --radius:      14px;
            --font-display: 'Playfair Display', Georgia, serif;
            --font-body:    'DM Sans', system-ui, sans-serif;
        }

        body {
            font-family: var(--font-body);
            background: var(--bg);
            color: var(--text);
            font-size: 15px;
            line-height: 1.7;
        }

        /* ── HERO ── */
        .gh-hero {
            position: relative;
            min-height: 480px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 0 0 32px 32px;
        }
        .gh-hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(10,22,40,0.55) 0%, rgba(27,52,96,0.75) 100%);
        }
        .gh-hero-content {
            position: relative;
            text-align: center;
            padding: 3rem 1.5rem;
            max-width: 720px;
        }
        .gh-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.22);
            color: rgba(255,255,255,0.9);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.07em;
            padding: 4px 14px;
            border-radius: 20px;
            margin-bottom: 1.2rem;
            text-transform: uppercase;
        }
        .gh-hero h1 {
            font-family: var(--font-display);
            font-size: clamp(1.8rem, 4vw, 2.7rem);
            color: #fff;
            line-height: 1.2;
            margin-bottom: 1rem;
        }
        .gh-hero p {
            color: rgba(255,255,255,0.72);
            font-size: 1rem;
            max-width: 540px;
            margin: 0 auto;
        }

        /* ── META BAR ── */
        .gh-meta-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2rem;
            background: var(--surface);
            border-radius: var(--radius);
            padding: 1rem 2rem;
            margin: -28px auto 2.5rem;
            max-width: 520px;
            box-shadow: 0 4px 24px rgba(27,52,96,0.10);
            border: 1px solid var(--border);
            position: relative;
            z-index: 10;
        }
        .gh-meta-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: var(--muted);
        }
        .gh-meta-item strong { color: var(--text); font-weight: 600; }
        .gh-meta-icon {
            width: 34px; height: 34px;
            border-radius: 9px;
            background: var(--brand-light);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .gh-meta-icon svg {
            width: 15px; height: 15px;
            stroke: var(--brand); fill: none;
            stroke-width: 2; stroke-linecap: round;
        }
        .gh-meta-divider {
            width: 1px; height: 36px;
            background: var(--border);
        }

        /* ── LAYOUT ── */
        .gh-layout {
            max-width: 1120px;
            margin: 0 auto;
            padding: 0 1.5rem 5rem;
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 2.5rem;
            align-items: start;
        }

        /* ── PROSE ── */
        .gh-prose {
            font-size: 15.5px;
            color: #2c3345;
            text-align: justify;
        }
        .gh-prose p { margin-bottom: 1.25rem; }
        .gh-prose h2, .gh-prose h3 {
            font-family: var(--font-display);
            color: var(--brand);
            margin: 2rem 0 .75rem;
        }

        /* ── CTA PORTAL ── */
        .gh-cta-portal {
            background: var(--brand);
            border-radius: var(--radius);
            padding: 1.75rem 2rem;
            margin-top: 2.5rem;
        }
        .gh-cta-portal h2 {
            font-family: var(--font-display);
            font-size: 1.3rem;
            color: #fff;
            margin-bottom: .5rem;
            line-height: 1.35;
        }
        .gh-cta-portal p {
            font-size: 13.5px;
            color: rgba(255,255,255,0.72);
            margin-bottom: 1.2rem;
        }
        #dynamic-text {
            color: #7eb8f7;
            font-style: italic;
            transition: opacity 0.6s ease;
            display: inline;
        }
        .gh-btn-portal {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            color: var(--brand);
            font-weight: 700;
            font-size: 14px;
            padding: 11px 24px;
            border-radius: 9px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: transform .15s, box-shadow .15s;
        }
        .gh-btn-portal:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.18);
            color: var(--brand);
            text-decoration: none;
        }
        .gh-btn-portal svg {
            width: 16px; height: 16px;
            stroke: var(--brand); fill: none;
            stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round;
        }

        /* ── SIDEBAR ── */
        .gh-sidebar-sticky {
            position: sticky;
            top: 16px;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .gh-sidebar-img {
            border-radius: var(--radius);
            overflow: hidden;
            aspect-ratio: 4 / 3;
            background: var(--brand-light);
        }
        .gh-sidebar-img img {
            width: 100%; height: 100%;
            object-fit: cover;
            display: block;
        }

        /* ── RELATED SECTION HEADER ── */
        .gh-related-title {
            font-family: var(--font-display);
            font-size: 1.1rem;
            color: var(--text);
            margin-bottom: 1rem;
            padding-bottom: .65rem;
            border-bottom: 2px solid var(--brand-light);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .gh-related-title::before {
            content: '';
            width: 4px; height: 18px;
            background: var(--brand);
            border-radius: 2px;
            display: inline-block;
            flex-shrink: 0;
        }

        /* ── RELATED CARD ── */
        .gh-related-card {
            display: flex;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            text-decoration: none;
            color: inherit;
            margin-bottom: .75rem;
            transition: box-shadow .2s, transform .2s, border-color .2s;
        }
        .gh-related-card:hover {
            box-shadow: 0 8px 28px rgba(27,52,96,0.13);
            transform: translateY(-2px);
            border-color: rgba(27,52,96,0.28);
            text-decoration: none;
            color: inherit;
        }
        .gh-related-card:hover .gh-rc-arrow {
            opacity: 1;
            transform: translateX(0);
        }

        .gh-rc-img {
            width: 90px;
            min-width: 90px;
            background: var(--brand-light);
            overflow: hidden;
            flex-shrink: 0;
        }
        .gh-rc-img img {
            width: 100%; height: 100%;
            object-fit: cover;
            display: block;
        }

        .gh-rc-body {
            flex: 1;
            padding: 10px 12px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 82px;
        }
        .gh-rc-title {
            font-size: 13px;
            font-weight: 500;
            line-height: 1.45;
            color: var(--text);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .gh-rc-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 6px;
            font-size: 11px;
            color: var(--muted);
        }
        .gh-rc-meta-chip {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .gh-rc-meta-chip svg {
            width: 11px; height: 11px;
            stroke: currentColor; fill: none;
            stroke-width: 2; stroke-linecap: round;
        }

        .gh-rc-arrow {
            display: flex;
            align-items: center;
            padding: 0 10px;
            color: var(--brand);
            opacity: 0;
            transform: translateX(-5px);
            transition: opacity .2s, transform .2s;
            flex-shrink: 0;
        }
        .gh-rc-arrow svg {
            width: 14px; height: 14px;
            stroke: currentColor; fill: none;
            stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round;
        }

        /* ── BTN VER TODOS ── */
        .gh-btn-all {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 11px;
            border-radius: 10px;
            background: var(--brand-light);
            color: var(--brand);
            font-size: 13px;
            font-weight: 600;
            border: 1.5px solid rgba(27,52,96,0.18);
            text-decoration: none;
            transition: background .2s, border-color .2s, color .2s;
            margin-top: .5rem;
        }
        .gh-btn-all:hover {
            background: var(--brand);
            color: #fff;
            border-color: var(--brand);
            text-decoration: none;
        }
        .gh-btn-all svg {
            width: 14px; height: 14px;
            stroke: currentColor; fill: none;
            stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round;
        }

        /* ── CTA CONTACT ── */
        .gh-cta-contact {
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-top: 3px solid var(--brand);
            border-radius: var(--radius);
            padding: 1.5rem;
        }
        .gh-cta-contact h3 {
            font-family: var(--font-display);
            font-size: 1.05rem;
            margin-bottom: .5rem;
            color: var(--text);
            line-height: 1.35;
        }
        .gh-cta-contact p {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 1rem;
            line-height: 1.55;
        }
        .gh-btn-contact {
            display: block;
            text-align: center;
            width: 100%;
            background: var(--brand);
            color: #fff;
            font-size: 13.5px;
            font-weight: 600;
            padding: 12px 16px;
            border-radius: 9px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            line-height: 1.45;
            transition: background .15s, transform .15s;
        }
        .gh-btn-contact:hover {
            background: var(--brand-mid);
            transform: translateY(-1px);
            color: #fff;
            text-decoration: none;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .gh-layout {
                grid-template-columns: 1fr;
            }
            .gh-sidebar-sticky {
                position: static;
            }
            .gh-meta-bar {
                gap: 1rem;
                padding: 1rem;
            }
        }
    </style>
@endsection

@section('content')

    {{-- HERO --}}
    <section class="gh-hero" style="background-image: url('{{ asset('uploads/posts/'.$post->first_image) }}')">
        <div class="gh-hero-overlay"></div>
        <div class="gh-hero-content">
            <div class="gh-hero-badge">
                <svg width="8" height="8" viewBox="0 0 8 8" style="fill:rgba(255,255,255,0.85)"><circle cx="4" cy="4" r="4"/></svg>
                Grupo Housing
            </div>
            <h1>{{ $post->publication_title }}</h1>
            <p>{{ $post->metadescription }}</p>
        </div>
    </section>

    {{-- META BAR --}}
    <div class="container px-3 px-md-4">
        <div class="gh-meta-bar">
            <div class="gh-meta-item">
                <div class="gh-meta-icon">
                    <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <div>
                    <div style="font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:1px;">Publicado</div>
                    <strong>{{ date('d M Y', strtotime($post->created_at)) }}</strong>
                </div>
            </div>
            <div class="gh-meta-divider"></div>
            <div class="gh-meta-item">
                <div class="gh-meta-icon">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                    <div style="font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:1px;">Lectura</div>
                    <strong>{{ $post->reading_time }} min</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN LAYOUT --}}
    <div class="gh-layout">

        {{-- CONTENIDO PRINCIPAL --}}
        <div>
            <div class="gh-prose">
                {!! $post->content !!}
            </div>

            {{-- CTA IR AL PORTAL --}}
            <div class="gh-cta-portal">
                <h2>Descubre <span id="dynamic-text">las propiedades</span> en venta que Grupo Housing tiene para ti</h2>
                <p>Entra y encuentra la propiedad que buscas: casas, departamentos, terrenos y más. ¡Tu espacio ideal a solo un clic!</p>
                <a href="/propiedades-en-general" class="gh-btn-portal">
                    <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Ir al portal
                </a>
            </div>
        </div>

        {{-- SIDEBAR --}}
        <div class="gh-sidebar-sticky">

            {{-- IMAGEN SECUNDARIA --}}
            <div class="gh-sidebar-img">
                <img src="{{ asset('uploads/posts/'.$post->second_image) }}" alt="{{ $post->publication_title }}">
            </div>

            {{-- ARTÍCULOS RELACIONADOS --}}
            @if(count($related_post) > 0)
            <div>
                <h2 class="gh-related-title">Artículos relacionados</h2>

                @foreach($related_post as $post_r)
                <a href="{{ route('web.show.post', $post_r->slug) }}" class="gh-related-card">
                    <div class="gh-rc-img">
                        <img src="{{ asset('uploads/posts/'.$post_r->first_image) }}" alt="{{ $post_r->publication_title }}">
                    </div>
                    <div class="gh-rc-body">
                        <div class="gh-rc-title">{{ $post_r->publication_title }}</div>
                        <div class="gh-rc-meta">
                            <div class="gh-rc-meta-chip">
                                <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                {{ date('d M Y', strtotime($post_r->created_at)) }}
                            </div>
                            <div class="gh-rc-meta-chip">
                                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                {{ $post_r->reading_time }} min
                            </div>
                        </div>
                    </div>
                    <div class="gh-rc-arrow">
                        <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
                    </div>
                </a>
                @endforeach

                <a href="/blog" class="gh-btn-all">
                    Ver todos los artículos
                    <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>
            @endif

            {{-- CTA CONTACTO --}}
            <div class="gh-cta-contact">
                <h3>¿Necesitas vender o rentar tu propiedad?</h3>
                <p>Contáctanos ahora y descubre la manera más <strong>rápida y sencilla</strong> de hacerlo posible.</p>
                <a href="/servicio/vende-tu-casa" class="gh-btn-contact">Contactarme con Grupo Housing</a>
            </div>

        </div>
    </div>

@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('dynamic-text');
        const words = ['las propiedades', 'las casas', 'los departamentos', 'los terrenos', 'las oficinas', 'los locales'];
        let i = 0;

        el.style.transition = 'opacity 0.6s ease';

        setInterval(function () {
            el.style.opacity = '0';
            setTimeout(function () {
                i = (i + 1) % words.length;
                el.textContent = words[i];
                el.style.opacity = '1';
            }, 350);
        }, 2500);
    });
</script>
@endsection