
document.addEventListener('DOMContentLoaded', () => {

    const okBox = document.querySelector('.success-box, .mensaje-ok');
    const errorBox = document.querySelector('.error-box, .mensaje-error');

    if (okBox) {
        const msg = okBox.textContent.trim();
        if (msg !== '') {
            alert(msg);   
        }
    }

    if (errorBox) {
        const msg = errorBox.textContent.trim();
        if (msg !== '') {
            alert(msg); 
        }
    }

    document.querySelectorAll('[data-confirm]').forEach((el) => {
        el.addEventListener('click', (e) => {
            const mensaje = el.getAttribute('data-confirm') || '¿Estás seguro?';
            const continuar = confirm(mensaje);
            if (!continuar) {
                e.preventDefault();
            }
        });
    });

    const btnVerPanel = document.getElementById('btnVerPanel');
    if (btnVerPanel) {
        const estaLogueado = btnVerPanel.dataset.logueado === '1';

        btnVerPanel.addEventListener('click', (e) => {
            if (!estaLogueado) {
                e.preventDefault(); // no ir al "#"
                alert('Debes iniciar sesión antes de ver el panel de inventario.');
                // Después del OK en el alert, lo mandamos a login.php
                window.location.href = 'login.php';
            }
            // Si SÍ está logueado, no hacemos nada y el href normal (panel.php) funciona
        });
    }

});

