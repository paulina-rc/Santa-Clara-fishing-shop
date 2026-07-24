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

document.querySelectorAll('a[href*="#"]').forEach(link => {
    link.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        const hashIndex = href.indexOf('#');
        if (hashIndex === -1) return;
        const targetId = href.substring(hashIndex + 1);
        if (!targetId) return;
        const target = document.getElementById(targetId);
        if (target && href.startsWith('#')) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});
