document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.qty-minus, .qty-plus').forEach((button) => {
        button.addEventListener('click', () => {
            const input = button.parentElement.querySelector('input[type="number"]');
            if (!input) return;

            const current = Number(input.value || 0);
            const min = Number(input.min || 1);
            const max = Number(input.max || Number.MAX_SAFE_INTEGER);

            if (button.classList.contains('qty-minus')) {
                input.value = Math.max(min, current - 1);
            } else {
                input.value = Math.min(max, current + 1);
            }
        });
    });

    document.querySelectorAll('[data-qty]').forEach((input) => {
        const form = input.closest('form');
        if (!form) return;

        form.addEventListener('submit', (event) => {
            if (input.value === '' || Number(input.value) <= 0) {
                event.preventDefault();
                alert('Please enter a valid quantity.');
            }
        });
    });

    document.querySelectorAll('.toggle-password').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.target);
            if (!input) return;
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            button.innerHTML = isPassword ? '<i class="fa-solid fa-eye-slash"></i>' : '<i class="fa-solid fa-eye"></i>';
        });
    });

    document.querySelectorAll('.confirm-delete').forEach((button) => {
        button.addEventListener('click', (event) => {
            if (!confirm('Are you sure you want to delete this item?')) {
                event.preventDefault();
            }
        });
    });
});
