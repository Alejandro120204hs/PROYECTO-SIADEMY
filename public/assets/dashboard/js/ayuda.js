document.addEventListener('DOMContentLoaded', function () {

    // ── Tabs ──────────────────────────────────────────────────────────────────
    document.querySelectorAll('.help-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            var tabName = this.getAttribute('data-tab');

            document.querySelectorAll('.help-content').forEach(function (c) {
                c.classList.remove('active');
            });
            document.querySelectorAll('.help-tab').forEach(function (t) {
                t.classList.remove('active');
            });

            document.getElementById(tabName).classList.add('active');
            this.classList.add('active');
        });
    });

    // ── FAQ acordeón ──────────────────────────────────────────────────────────
    document.querySelectorAll('.faq-question').forEach(function (question) {
        question.addEventListener('click', function () {
            this.parentElement.classList.toggle('open');
        });
    });

    // ── Formulario de contacto ────────────────────────────────────────────────
    var contactForm = document.getElementById('contactForm');
    if (!contactForm) return;

    contactForm.addEventListener('submit', function (event) {
        event.preventDefault();

        var btn = contactForm.querySelector('button[type="submit"]');
        var originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="ri-loader-4-line"></i> Enviando...';

        var data = new FormData();
        data.append('nombre',  contactForm.querySelector('[name="nombre"]').value.trim());
        data.append('correo',  contactForm.querySelector('[name="correo"]').value.trim());
        data.append('asunto',  contactForm.querySelector('[name="asunto"]').value.trim());
        data.append('mensaje', contactForm.querySelector('[name="mensaje"]').value.trim());

        fetch(window.BASE_URL + '/contacto-soporte', {
            method: 'POST',
            body: data
        })
        .then(function (res) { return res.json(); })
        .then(function (json) {
            if (json.ok) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Mensaje enviado!',
                    text: 'Gracias por tu mensaje. Nos pondremos en contacto pronto.',
                    confirmButtonColor: '#6366f1',
                    background: '#11193a',
                    color: '#e2e5f0',
                    iconColor: '#4ade80',
                    confirmButtonText: 'Aceptar'
                });
                contactForm.reset();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'No se pudo enviar',
                    text: json.msg || 'Intenta de nuevo.',
                    confirmButtonColor: '#6366f1',
                    background: '#11193a',
                    color: '#e2e5f0'
                });
            }
        })
        .catch(function () {
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor. Revisa tu conexión.',
                confirmButtonColor: '#6366f1',
                background: '#11193a',
                color: '#e2e5f0'
            });
        })
        .finally(function () {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    });

});
