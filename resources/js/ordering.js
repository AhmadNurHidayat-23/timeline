export function setupOrdering() {
    const list = document.querySelector('[data-reorder-url]');
    if (!list) return;
    const status = document.querySelector('[data-order-status]');
    let busy = false;
    let drag = null;

    const items = () => [...list.querySelectorAll('[data-event-id]')];
    const restore = (order) => order.forEach((item) => list.append(item));
    const lock = (locked) => {
        busy = locked;
        list.querySelectorAll('button').forEach((button) => { button.disabled = locked; });
    };
    const save = async (previous) => {
        if (items().every((item, index) => item === previous[index])) return;
        lock(true);
        status.textContent = 'Menyimpan urutan…';
        try {
            const response = await fetch(list.dataset.reorderUrl, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ ids: items().map((item) => Number(item.dataset.eventId)) }),
            });
            if (!response.ok) throw new Error('Urutan gagal disimpan. Muat ulang halaman lalu coba lagi.');
            status.textContent = 'Urutan manual tersimpan.';
        } catch (error) {
            restore(previous);
            status.textContent = error.message === 'Failed to fetch'
                ? 'Koneksi terputus. Urutan dikembalikan; silakan coba lagi.' : error.message;
        } finally {
            lock(false);
        }
    };

    list.addEventListener('click', (event) => {
        const button = event.target.closest('[data-move]');
        if (!button || busy || drag) return;
        const item = button.closest('[data-event-id]');
        const previous = items();
        if (button.dataset.move === 'up' && item.previousElementSibling) {
            list.insertBefore(item, item.previousElementSibling);
        } else if (button.dataset.move === 'down' && item.nextElementSibling) {
            list.insertBefore(item.nextElementSibling, item);
        }
        button.focus();
        save(previous);
    });

    list.addEventListener('pointerdown', (event) => {
        const handle = event.target.closest('[data-drag-handle]');
        if (!handle || busy || event.button !== 0) return;
        const item = handle.closest('[data-event-id]');
        drag = { item, pointerId: event.pointerId, previous: items() };
        // Capture on the stable list: moving the item would release its capture.
        list.setPointerCapture(event.pointerId);
        item.classList.add('is-dragging');
        status.textContent = 'Geser peristiwa ke posisi yang diinginkan.';
        event.preventDefault();
    });

    list.addEventListener('pointermove', (event) => {
        if (!drag || event.pointerId !== drag.pointerId) return;
        const target = items().find((item) => {
            if (item === drag.item) return false;
            const rect = item.getBoundingClientRect();
            return event.clientY < rect.top + rect.height / 2;
        });
        list.insertBefore(drag.item, target || null);
        if (event.clientY < 90) window.scrollBy(0, -14);
        if (event.clientY > window.innerHeight - 90) window.scrollBy(0, 14);
    });

    const finish = (event, cancelled = false) => {
        if (!drag || event.pointerId !== drag.pointerId) return;
        const { item, previous, pointerId } = drag;
        drag = null;
        item.classList.remove('is-dragging');
        if (list.hasPointerCapture(pointerId)) list.releasePointerCapture(pointerId);
        if (cancelled) {
            restore(previous);
            status.textContent = 'Perubahan urutan dibatalkan.';
        } else {
            status.textContent = 'Urutan tidak berubah.';
            save(previous);
        }
    };
    list.addEventListener('pointerup', (event) => finish(event));
    list.addEventListener('pointercancel', (event) => finish(event, true));
    list.addEventListener('lostpointercapture', (event) => finish(event, true));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && drag) finish({ pointerId: drag.pointerId }, true);
    });
}
