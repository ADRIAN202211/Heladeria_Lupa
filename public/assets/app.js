/* Heladería Lupa · pequeñas mejoras de interfaz. El sitio funciona sin JavaScript. */
(function () {
    'use strict';

    // Botón para volver arriba.
    var subir = document.getElementById('subir');
    if (subir) {
        var mostrar = function () { subir.classList.toggle('visible', window.scrollY > 400); };
        window.addEventListener('scroll', mostrar, { passive: true });
        subir.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });
        mostrar();
    }

    // Pide confirmación antes de borrar o vaciar (formularios con data-confirm).
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (ev) {
            if (!window.confirm(form.dataset.confirm)) { ev.preventDefault(); }
        });
    });

    // Pedido: muestra la dirección solo si es domicilio y recalcula el total.
    var pedido = document.getElementById('form-pedido');
    if (pedido) {
        var bloque = document.getElementById('bloque-domicilio');
        var resDom = document.getElementById('resumen-domicilio');
        var resTotal = document.getElementById('resumen-total');
        var subtotal = pedido.parentElement.querySelector('[data-subtotal]');
        var base = subtotal ? parseInt(subtotal.dataset.subtotal, 10) : 0;

        var pesos = function (n) { return '$' + String(n).replace(/\B(?=(\d{3})+(?!\d))/g, '.'); };

        var actualizar = function () {
            var elegido = pedido.querySelector('input[name="entrega"]:checked');
            var costo = elegido ? parseInt(elegido.dataset.costo || '0', 10) : 0;
            var esDomicilio = elegido && elegido.value === 'domicilio';
            if (bloque) { bloque.hidden = !esDomicilio; }
            if (resDom) { resDom.textContent = costo > 0 ? pesos(costo) : 'No aplica'; }
            if (resTotal) { resTotal.textContent = pesos(base + costo); }
        };

        pedido.querySelectorAll('input[name="entrega"]').forEach(function (r) {
            r.addEventListener('change', actualizar);
        });
        actualizar();
    }

    // Códigos QR del panel (la librería se carga antes que este archivo).
    if (window.QRCode) {
        document.querySelectorAll('canvas[data-qr]').forEach(function (canvas) {
            window.QRCode.toCanvas(canvas, canvas.dataset.qr, { width: 160, margin: 1 }, function (err) {
                if (err) { canvas.insertAdjacentText('afterend', 'No se pudo generar el QR.'); }
            });
        });
    }
})();
