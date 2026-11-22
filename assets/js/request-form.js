document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.tenezer-request-form').forEach(function (form) {
        // Un choix « Autre » (data-reveal) affiche son champ de précision et ne l'exige que lorsqu'il est coché
        form.querySelectorAll('input[type=radio]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                form.querySelectorAll('input[type=radio][data-reveal]').forEach(function (r) {
                    if (r.name !== radio.name) return;
                    const target = document.getElementById(r.dataset.reveal);
                    target.hidden = !r.checked;
                    target.querySelectorAll('input').forEach(function (input) {
                        input.required = r.checked;
                        if (r.checked && r === radio) input.focus();
                    });
                });
            });
        });

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
