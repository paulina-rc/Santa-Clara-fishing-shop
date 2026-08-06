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

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.galeria').forEach(inicializarGaleria);
});

function inicializarGaleria(galeria) {
    const principal = galeria.querySelector('.galeria-principal');
    if (!principal) return;

    const total = parseInt(principal.dataset.total || '0', 10);
    if (total <= 1) return;

    const fotos = galeria.querySelectorAll('.galeria-foto');
    const miniaturas = galeria.querySelectorAll('.galeria-mini');
    const contadorEl = galeria.querySelector('.galeria-actual');
    const flechaAnt = galeria.querySelector('.galeria-anterior');
    const flechaSig = galeria.querySelector('.galeria-siguiente');
    let indice = 0;

    function mostrar(i) {
        indice = (i + total) % total;
        fotos.forEach((f, idx) => f.classList.toggle('activa', idx === indice));
        miniaturas.forEach((m, idx) => m.classList.toggle('activa', idx === indice));
        if (contadorEl) contadorEl.textContent = indice + 1;
    }

    if (flechaAnt) flechaAnt.addEventListener('click', () => mostrar(indice - 1));
    if (flechaSig) flechaSig.addEventListener('click', () => mostrar(indice + 1));

    miniaturas.forEach((mini) => {
        mini.addEventListener('click', () => {
            mostrar(parseInt(mini.dataset.index, 10));
        });
    });

    let touchStartX = 0;
    let touchEndX = 0;

    principal.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    principal.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        const diff = touchStartX - touchEndX;
        if (Math.abs(diff) > 50) {
            if (diff > 0) mostrar(indice + 1);
            else mostrar(indice - 1);
        }
    }, { passive: true });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') mostrar(indice - 1);
        else if (e.key === 'ArrowRight') mostrar(indice + 1);
    });
}
