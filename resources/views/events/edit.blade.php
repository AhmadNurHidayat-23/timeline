@extends('layouts.app')
@section('title', 'Edit peristiwa')
@section('content')
    <a class="back-link" href="{{ route('timelines.show', $timeline) }}">← Kembali ke timeline</a>
    <div class="form-page">
        <header><p class="eyebrow">{{ $timeline->name }}</p><h1>Edit peristiwa.</h1>
            <p>Perbarui detailnya. Setiap bagian cerita berarti.</p>
        </header>
        <div>@include('partials.errors') @include('events.form')
            <a class="cancel-link" href="{{ route('timelines.show', $timeline) }}">Batal</a>
        </div>
    </div>
@endsection
