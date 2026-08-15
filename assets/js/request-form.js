document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.tenezer-request-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const msg = form.querySelector('.form-msg');
            const submitBtn = form.querySelector('button[type=submit]');
            const data = new FormData(form);

            if (submitBtn) submitBtn.disabled = true;
            if (msg) msg.classList.add('hidden');

            fetch('process_request.php', { method: 'POST', body: data })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    if (res.status === 'success') {
                        window.location.href = 'confirmation.php?id=' + res.id;
                    } else {
                        if (msg) {
                            msg.textContent = res.message || 'Une erreur est survenue.';
                            msg.classList.remove('hidden');
                        }
                        if (submitBtn) submitBtn.disabled = false;
                    }
                })
                .catch(function () {
                    if (msg) {
                        msg.textContent = 'Erreur réseau, veuillez réessayer.';
                        msg.classList.remove('hidden');
                    }
                    if (submitBtn) submitBtn.disabled = false;
                });
        });
    });
});
