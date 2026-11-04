@extends('layouts.app')
@section('title', $timeline->name)
@section('content')
    @unless($readOnly)<a class="back-link" href="{{ route('timelines.index') }}">← Koleksi saya</a>@endunless
    <section class="story-heading {{ $readOnly ? 'public-heading' : '' }}">
        <p class="eyebrow">{{ $readOnly ? 'SEBUAH CERITA, DIBAGIKAN.' : 'SATU PERJALANAN. BANYAK PERISTIWA.' }}</p>
        <h1>{{ $timeline->name }}</h1>
        @if($timeline->description)<p class="story-description">{{ $timeline->description }}</p>@endif
        <div class="story-meta"><span>{{ $events->count() }} peristiwa</span><span>Diperbarui {{ $timeline->updated_at->translatedFormat('d F Y') }}</span>
            @unless($readOnly)<a href="{{ route('timelines.edit', $timeline) }}">Edit timeline ↗</a>@endunless
        </div>
    </section>
    <div class="story-layout {{ $readOnly ? 'is-public' : '' }}">
        <section class="story-events" aria-labelledby="events-title">
            <div class="events-toolbar">
                <h2 id="events-title">Rangkaian peristiwa</h2>
                @unless($readOnly)
                    <form action="{{ route('timelines.sort', $timeline) }}" method="POST" class="sort-form">
                        @csrf @method('PATCH')
                        <label class="sr-only" for="sort_mode">Mode pengurutan</label>
                        <select name="sort_mode" id="sort_mode">
                            <option value="chronological" @selected($timeline->sort_mode === 'chronological')>Kronologis</option>
                            <option value="manual" @selected($timeline->sort_mode === 'manual')>Urutan manual</option>
                        </select><button class="text-button">Terapkan</button>
                    </form>
                @else
                    <span class="eyebrow">{{ $timeline->sort_mode === 'manual' ? 'URUTAN MANUAL' : 'KRONOLOGIS' }}</span>
                @endunless
            </div>
            @if($events->isNotEmpty())
                <div class="event-navigation" data-event-navigation>
                    <p>{{ !$readOnly && $timeline->sort_mode === 'manual' ? 'Tarik pegangan ⋮⋮ atau gunakan tombol ↑ ↓.' : 'Ikuti cerita, satu peristiwa demi satu.' }}</p>
                    <div><button class="icon-button" type="button" data-previous aria-label="Peristiwa sebelumnya">↑</button>
                        <button class="icon-button" type="button" data-next aria-label="Peristiwa berikutnya">↓</button></div>
                </div>
            @endif
            <p class="save-status" role="status" aria-live="polite" data-order-status></p>
            <ol class="event-list" @if(!$readOnly && $timeline->sort_mode === 'manual') data-reorder-url="{{ route('events.reorder', $timeline) }}" @endif>
                @forelse($events as $item)
                    @include('events.item')
                @empty
                    <li class="empty-events"><span class="empty-marker" aria-hidden="true"></span>
                        <p class="eyebrow">TITIK AWAL</p><h3>Belum ada peristiwa.</h3>
                        <p>{{ $readOnly ? 'Cerita ini sedang disusun. Kembali lagi nanti.' : 'Tambahkan peristiwa pertama melalui formulir. Cerita Anda dimulai di sini.' }}</p>
                    </li>
                @endforelse
            </ol>
        </section>
        @unless($readOnly)
            <aside class="editor-aside">
                <section class="event-editor" aria-labelledby="add-title">
                    <p class="eyebrow">LANJUTKAN CERITANYA</p><h2 id="add-title">Tambah peristiwa</h2>
                    @include('partials.errors') @include('events.form')
                </section>
                <section class="share-panel" aria-labelledby="share-title">
                    <h2 id="share-title">Cerita untuk dibagikan.</h2>
                    <p>Siapa pun dengan tautan ini dapat membaca timeline Anda. Pengunjung hanya bisa melihat.</p>
                    <label class="sr-only" for="share-url">Tautan berbagi</label>
                    <input id="share-url" readonly value="{{ route('timelines.shared', $timeline->share_token) }}">
                    <div class="share-actions"><button type="button" class="text-button" data-copy="share-url">Salin tautan ↗</button>
                        <a target="_blank" rel="noopener" href="{{ route('timelines.shared', $timeline->share_token) }}">Lihat publik ↗</a></div>
                    <p class="field-hint" data-copy-status role="status"></p>
                </section>
            </aside>
        @endunless
    </div>
@endsection
