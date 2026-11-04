<form class="stack-form" method="POST" action="{{ isset($event) ? route('timelines.events.update', [$timeline, $event]) : route('timelines.events.store', $timeline) }}">
    @csrf
    @isset($event) @method('PUT') @endisset
    <div class="field"><label for="title">Judul peristiwa <span>Wajib</span></label>
        <input id="title" name="title" maxlength="160" required value="{{ old('title', $event->title ?? '') }}" placeholder="Apa yang terjadi?">
    </div>
    <div class="field"><label for="event_date">Tanggal <span>Wajib</span></label>
        <input id="event_date" name="event_date" type="date" min="0001-01-01" max="9999-12-31" required value="{{ old('event_date', isset($event) ? $event->event_date->format('Y-m-d') : '') }}">
    </div>
    <div class="field"><label for="description">Deskripsi singkat <span>Opsional</span></label>
        <textarea id="description" name="description" rows="4" maxlength="1000" placeholder="Tambahkan sedikit cerita di balik peristiwa ini.">{{ old('description', $event->description ?? '') }}</textarea>
        <p class="field-hint">Maksimal 1.000 karakter.</p>
    </div>
    <button class="button">{{ isset($event) ? 'Simpan perubahan' : 'Simpan peristiwa' }} <span aria-hidden="true">↗</span></button>
</form>
