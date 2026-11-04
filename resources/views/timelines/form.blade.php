@extends('layouts.app')
@section('title', $timeline->exists ? 'Edit timeline' : 'Buat timeline')
@section('content')
    <a class="back-link" href="{{ $timeline->exists ? route('timelines.show', $timeline) : route('timelines.index') }}">← Kembali</a>
    <div class="form-page">
        <header><p class="eyebrow">{{ $timeline->exists ? 'SUNTING CERITA' : 'MULAI SEBUAH CERITA' }}</p>
            <h1>{{ $timeline->exists ? 'Edit timeline.' : 'Beri perjalanan Anda nama.' }}</h1>
            <p>Sebuah ruang untuk menyusun peristiwa yang berarti.</p>
        </header>
        <div>
            @include('partials.errors')
            <form class="stack-form" method="POST" action="{{ $timeline->exists ? route('timelines.update', $timeline) : route('timelines.store') }}">
                @csrf
                @if($timeline->exists) @method('PUT') @endif
                <div class="field"><label for="name">Nama timeline <span>Wajib</span></label>
                    <input id="name" name="name" value="{{ old('name', $timeline->name) }}" maxlength="120" required placeholder="Misalnya, Perjalanan studio kami" autofocus>
                </div>
                <div class="field"><label for="description">Deskripsi singkat <span>Opsional</span></label>
                    <textarea id="description" name="description" rows="5" maxlength="500" placeholder="Apa yang ingin Anda ceritakan?">{{ old('description', $timeline->description) }}</textarea>
                    <p class="field-hint">Maksimal 500 karakter.</p>
                </div>
                <div class="form-actions"><button class="button">{{ $timeline->exists ? 'Simpan perubahan' : 'Buat timeline' }} <span aria-hidden="true">↗</span></button>
                    <a href="{{ $timeline->exists ? route('timelines.show', $timeline) : route('timelines.index') }}">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
