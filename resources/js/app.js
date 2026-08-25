import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Swal = Swal;

/**
 * Global styled confirm dialog (SweetAlert2).
 * Reads data-confirm* from the clicked submit button OR from the form itself:
 *   <button type="submit" form="delete-x" data-confirm="Delete?" ...>
 *   <form data-confirm="Delete?" ...>
 */
document.addEventListener('submit', (e) => {
    const form = e.target.closest('form');
    if (!form) return;

    const source = [e.submitter, form]
        .filter((el) => el && el.getAttribute)
        .find((el) => el.hasAttribute('data-confirm'));

    if (!source) return;

    e.preventDefault();

    Swal.fire({
        title: source.getAttribute('data-confirm-title') || 'Are you sure?',
        text: source.getAttribute('data-confirm'),
        icon: source.getAttribute('data-confirm-icon') || 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: source.getAttribute('data-confirm-button') || 'Yes, delete it',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}, true);

Alpine.start();
