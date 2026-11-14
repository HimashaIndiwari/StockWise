import * as bootstrap from 'bootstrap';
import 'admin-lte';

window.bootstrap = bootstrap;

/**
 * Confirmation dialog for destructive actions.
 * Usage: <form data-confirm="Archive this product?" ...>
 * Uses the shared Bootstrap modal (#confirmModal) rendered in the app layout.
 */
document.addEventListener('DOMContentLoaded', () => {
    const modalEl = document.getElementById('confirmModal');
    if (!modalEl) {
        return;
    }

    const modal = new bootstrap.Modal(modalEl);
    const messageEl = modalEl.querySelector('[data-confirm-message]');
    const confirmBtn = modalEl.querySelector('[data-confirm-accept]');
    let pendingForm = null;

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.confirmed === 'true') {
                return;
            }
            event.preventDefault();
            pendingForm = form;
            messageEl.textContent = form.dataset.confirm;
            modal.show();
        });
    });

    confirmBtn.addEventListener('click', () => {
        if (pendingForm) {
            pendingForm.dataset.confirmed = 'true';
            confirmBtn.blur(); // don't leave focus inside a modal that becomes aria-hidden
            modal.hide();
            pendingForm.requestSubmit();
        }
    });
});
