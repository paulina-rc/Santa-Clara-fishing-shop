document.addEventListener('DOMContentLoaded', function () {
    var boton = document.getElementById('botonMenuMovil');
    var nav = document.getElementById('navPrincipal');

    if (!boton || !nav) {
        return;
    }

    boton.addEventListener('click', function () {
        var abierto = nav.classList.toggle('nav-abierto');
        boton.setAttribute('aria-expanded', abierto ? 'true' : 'false');
    });

    nav.querySelectorAll('a').forEach(function (enlace) {
        enlace.addEventListener('click', function () {
            nav.classList.remove('nav-abierto');
            boton.setAttribute('aria-expanded', 'false');
        });
    });
});
