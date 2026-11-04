<li class="event-item" id="peristiwa-{{ $item->id }}" data-event-id="{{ $item->id }}" tabindex="-1">
    <span class="event-marker" aria-hidden="true"></span>
    <div class="event-date"><time datetime="{{ $item->event_date->format('Y-m-d') }}">{{ $item->event_date->translatedFormat('d M Y') }}</time></div>
    <div class="event-content">
        <h3>{{ $item->title }}</h3>
        @if($item->description)<p>{{ $item->description }}</p>@endif
        @unless($readOnly)
            <div class="event-actions">
                <a href="{{ route('timelines.events.edit', [$timeline, $item]) }}" aria-label="Edit {{ $item->title }}">Edit</a>
                <form method="POST" action="{{ route('timelines.events.destroy', [$timeline, $item]) }}" data-confirm="Hapus peristiwa ini?">
                    @csrf @method('DELETE')<button class="text-button" aria-label="Hapus {{ $item->title }}">Hapus</button>
                </form>
                @if($timeline->sort_mode === 'manual')
                    <div class="reorder-controls">
                        <button class="icon-button drag-handle" type="button" data-drag-handle aria-label="Tarik untuk memindahkan {{ $item->title }}">⋮⋮</button>
                        <button class="icon-button" type="button" data-move="up" aria-label="Pindahkan {{ $item->title }} ke atas">↑</button>
                        <button class="icon-button" type="button" data-move="down" aria-label="Pindahkan {{ $item->title }} ke bawah">↓</button>
                    </div>
                @endif
            </div>
        @endunless
    </div>
</li>
