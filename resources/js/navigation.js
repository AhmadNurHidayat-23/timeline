export function setupNavigation() {
    const navigation = document.querySelector('[data-event-navigation]');
    const list = document.querySelector('.event-list');
    if (!navigation || !list) return;
    let currentId = null;

    const move = (direction) => {
        const items = [...list.querySelectorAll('[data-event-id]')];
        const previousIndex = items.findIndex((item) => item.dataset.eventId === currentId);
        const index = previousIndex < 0
            ? (direction > 0 ? 0 : items.length - 1)
            : Math.max(0, Math.min(items.length - 1, previousIndex + direction));
        const item = items[index];
        if (!item) return;
        items.forEach((entry) => entry.classList.remove('is-active'));
        currentId = item.dataset.eventId;
        item.classList.add('is-active');
        item.focus({ preventScroll: true });
        item.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth', block: 'center' });
    };

    navigation.querySelector('[data-previous]').addEventListener('click', () => move(-1));
    navigation.querySelector('[data-next]').addEventListener('click', () => move(1));
}
