import { setupOrdering } from './ordering';
import { setupNavigation } from './navigation';

document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm(form.dataset.confirm)) event.preventDefault();
    });
});

document.querySelectorAll('[data-copy]').forEach((button) => {
    button.addEventListener('click', async () => {
        const input = document.getElementById(button.dataset.copy);
        const status = document.querySelector('[data-copy-status]');
        try {
            await navigator.clipboard.writeText(input.value);
            status.textContent = 'Tautan berhasil disalin.';
        } catch {
            input.focus();
            input.select();
            status.textContent = 'Pilih dan salin tautan di atas secara manual.';
        }
    });
});

const errors = document.querySelector('[data-errors]');
if (errors) errors.focus();
setupOrdering();
setupNavigation();
