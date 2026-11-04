@extends('layouts.app')
@section('title', 'Cerita dalam Garis Waktu.')
@section('content')
    <section class="home-hero" aria-labelledby="hero-title">
        <p class="eyebrow"><span class="small-dash" aria-hidden="true"></span> PERISTIWA, TERSUSUN.</p>
        <h1 id="hero-title">Cerita dalam Garis Waktu.</h1>
        <div class="hero-bottom">
            <p>Susun peristiwa menjadi perjalanan yang mudah dipahami.</p>
            <span class="hero-index" aria-hidden="true">01 — ∞</span>
        </div>
    </section>
    <section class="collection" aria-labelledby="collection-title">
        <div class="section-heading">
            <h2 id="collection-title">Koleksi timeline <span class="count">{{ $timelines->total() }}</span></h2>
            <p class="eyebrow">CERITA ANDA, DARI AWAL.</p>
        </div>
        @forelse($timelines as $timeline)
            <article class="collection-row">
                <span class="row-number" aria-hidden="true">{{ str_pad($timelines->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }}</span>
                <div class="row-story">
                    <h3><a href="{{ route('timelines.show', $timeline) }}">{{ $timeline->name }}</a></h3>
                    <p>{{ $timeline->description ?: 'Sebuah perjalanan, satu peristiwa pada satu waktu.' }}</p>
                </div>
                <div class="row-meta"><strong>{{ $timeline->events_count }} peristiwa</strong>
                    <span>Diperbarui {{ $timeline->updated_at->translatedFormat('d M Y') }}</span>
                </div>
                <div class="row-actions">
                    <a class="text-link open-link" href="{{ route('timelines.show', $timeline) }}">Buka <span aria-hidden="true">↗</span></a>
                    <div class="secondary-actions">
                        <a href="{{ route('timelines.edit', $timeline) }}" aria-label="Edit {{ $timeline->name }}">Edit</a>
                        <form action="{{ route('timelines.destroy', $timeline) }}" method="POST" data-confirm="Hapus timeline ini beserta seluruh peristiwanya?">
                            @csrf @method('DELETE')
                            <button class="text-button" aria-label="Hapus {{ $timeline->name }}">Hapus</button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="empty-collection">
                <div class="empty-line" aria-hidden="true"><i></i><i></i><i></i></div>
                <div><p class="eyebrow">HALAMAN PERTAMA MASIH KOSONG</p>
                    <h3>Perjalanan besar dimulai<br>dari satu peristiwa.</h3>
                    <p>Simpan kenangan, catat perkembangan, atau ceritakan sejarah.<br>Mulai dengan sebuah timeline.</p>
                    <a class="button" href="{{ route('timelines.create') }}">Buat timeline pertama <span aria-hidden="true">↗</span></a>
                </div>
            </div>
        @endforelse
        @if($timelines->hasPages())
            <nav class="pagination" aria-label="Halaman koleksi">
                @if($timelines->onFirstPage())<span>Sebelumnya</span>@else<a href="{{ $timelines->previousPageUrl() }}">← Sebelumnya</a>@endif
                <span>{{ $timelines->currentPage() }} / {{ $timelines->lastPage() }}</span>
                @if($timelines->hasMorePages())<a href="{{ $timelines->nextPageUrl() }}">Berikutnya →</a>@else<span>Berikutnya</span>@endif
            </nav>
        @endif
    </section>
@endsection
