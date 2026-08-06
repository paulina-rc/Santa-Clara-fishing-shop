// ============================================================
// Tienda de Pesca Santa Clara — JavaScript del sitio público
// ============================================================

/**
 * Inicializa una galería de producto (flechas, miniaturas, swipe, teclado).
 */
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
            const idx = parseInt(mini.dataset.index, 10);
            if (!isNaN(idx)) mostrar(idx);
        });
    });

    let touchStartX = 0;
    principal.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    principal.addEventListener('touchend', (e) => {
        const touchEndX = e.changedTouches[0].screenX;
        const diff = touchStartX - touchEndX;
        if (Math.abs(diff) > 50) {
            mostrar(diff > 0 ? indice + 1 : indice - 1);
        }
    }, { passive: true });

    // Teclado: solo cuando la galería tiene foco (evita interceptar flechas
    // en el resto de la página, ej. mientras se escribe en un input).
    galeria.tabIndex = 0;
    galeria.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') mostrar(indice - 1);
        else if (e.key === 'ArrowRight') mostrar(indice + 1);
    });
}

/**
 * Menú hamburguesa en móvil (header público).
 */
function inicializarMenuMovil() {
    const boton = document.getElementById('botonMenuMovil');
    const nav = document.getElementById('navPrincipal');
    if (!boton || !nav) return;

    boton.addEventListener('click', () => {
        const abierto = nav.classList.toggle('nav-abierto');
        boton.setAttribute('aria-expanded', abierto ? 'true' : 'false');
    });

    nav.querySelectorAll('a').forEach((enlace) => {
        enlace.addEventListener('click', () => {
            nav.classList.remove('nav-abierto');
            boton.setAttribute('aria-expanded', 'false');
        });
    });
}

/**
 * Scroll suave para anclas dentro de la misma página (incluye enlaces con
 * ruta + hash, ej. "nosotros.php#contacto" cuando ya se está en esa página).
 */
function inicializarAnclas() {
    document.querySelectorAll('a[href*="#"]').forEach((link) => {
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
}

/**
 * Punto de entrada: se ejecuta cuando el DOM está listo, o inmediatamente
 * si ya lo estaba al momento de cargar el script.
 */
function init() {
    document.querySelectorAll('.galeria').forEach(inicializarGaleria);
    inicializarMenuMovil();
    inicializarAnclas();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
