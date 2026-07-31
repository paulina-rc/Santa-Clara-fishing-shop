<?php
declare(strict_types=1);

$pagina_activa = 'nosotros';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

$meta_titulo = 'Sobre nosotros · Tienda de Pesca Santa Clara';
$meta_descripcion = 'Conoce la historia de Tienda de Pesca Santa Clara. Equipo de pesca deportiva en Santa Clara, San Carlos. Escríbenos por WhatsApp.';

$nosotrosFoto = null;
foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
    if (is_file(__DIR__ . '/assets/img/foto-con-kraken.' . $ext)) {
        $nosotrosFoto = 'assets/img/foto-con-kraken.' . $ext;
        break;
    }
}

require __DIR__ . '/includes/header.php';
?>

<section class="nosotros-historia">
    <div class="nosotros-grid">
        <div class="nosotros-texto">
            <p class="nosotros-eyebrow mono">Nuestra historia</p>
            <h1 class="nosotros-titulo">Pasión por la pesca, desde Santa Clara</h1>

            <!-- TODO: Reemplazar con historia real que envíe el cliente -->
            <div class="nosotros-parrafos">
                <p>Tienda de Pesca Santa Clara nació del amor por la pesca deportiva y las aguas de Costa Rica. Somos un pequeño equipo apasionado que sabe lo que se siente esperar el mordisco perfecto, lanzar bajo el sol y volver a casa con la mejor historia del día.</p>
                <p>No vendemos cualquier cosa: elegimos cada caña, cada carrete y cada señuelo pensando en quienes viven la pesca como una forma de conectar con la naturaleza. Trabajamos con marcas confiables, precios justos y asesoría honesta.</p>
                <p>Estamos en Santa Clara, San Carlos, y enviamos a todo Costa Rica. Si tienes dudas sobre qué equipo necesitas, escríbenos — te ayudamos con gusto.</p>
            </div>

            <div class="nosotros-pilares">
                <div class="nosotros-pilar">
                    <span class="nosotros-pilar-icono" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20s-7-4.35-9.5-9C1 7.5 3 4 6.5 4c2 0 3.5 1.2 5.5 3.5C14 5.2 15.5 4 17.5 4 21 4 23 7.5 21.5 11 19 15.65 12 20 12 20Z"/></svg>
                    </span>
                    <h3 class="nosotros-pilar-titulo">Pasión</h3>
                    <p class="nosotros-pilar-texto">Vivimos la pesca como una forma de conectar con la naturaleza.</p>
                </div>

                <div class="nosotros-pilar">
                    <span class="nosotros-pilar-icono" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/></svg>
                    </span>
                    <h3 class="nosotros-pilar-titulo">Paciencia</h3>
                    <p class="nosotros-pilar-texto">Sabemos lo que es esperar el mordisco perfecto.</p>
                </div>

                <div class="nosotros-pilar">
                    <span class="nosotros-pilar-icono" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9.5 14.5 14.5 9.5"/><path d="M8 16 5.5 18.5A3 3 0 1 1 1.7 14.7L4.2 12.2"/><path d="M16 8l2.5-2.5a3 3 0 1 1 4.2 4.2L20.2 12.2"/></svg>
                    </span>
                    <h3 class="nosotros-pilar-titulo">Conexión</h3>
                    <p class="nosotros-pilar-texto">Con el agua, con la naturaleza y con cada cliente.</p>
                </div>
            </div>
        </div>

        <div class="nosotros-foto-columna">
            <div class="nosotros-foto">
                <?php if ($nosotrosFoto !== null): ?>
                    <img src="<?= e($nosotrosFoto) ?>" alt="Tienda de Pesca Santa Clara" class="nosotros-foto-img">
                <?php else: ?>
                    <!-- TODO: Reemplazar con foto real que envíe el cliente -->
                    <p class="nosotros-foto-caption mono">[ foto del interior de la tienda o del equipo ]</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="contacto" id="contacto">
    <div class="contacto-header">
        <p class="nosotros-eyebrow mono">Contáctanos</p>
    </div>

    <div class="contacto-grid">
        <div class="contacto-stack">
            <a class="tarjeta-contacto tarjeta-whatsapp" href="<?= e(url_whatsapp('Hola, quisiera más información.')) ?>" target="_blank" rel="noopener noreferrer">
                <span class="tarjeta-contacto-icono" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="#fff">
                        <path d="M17.5 14.4c-.3-.1-1.7-.8-2-.9-.3-.1-.5-.1-.7.1-.2.3-.7.9-.9 1.1-.2.2-.3.2-.6.1-1.7-.9-2.9-1.6-4.1-3.6-.3-.5.3-.5.9-1.6.1-.2 0-.4 0-.5 0-.1-.7-1.6-.9-2.2-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.5s1.1 2.9 1.2 3.1c.1.2 2.1 3.3 5.2 4.6.7.3 1.3.5 1.7.6.7.2 1.4.2 1.9.1.6-.1 1.7-.7 2-1.4.2-.7.2-1.2.2-1.4-.1-.1-.3-.2-.6-.4z"/>
                        <path d="M20 4A10 10 0 003.4 15.6L2 22l6.6-1.4A10 10 0 1020 4zm-8 17.4a7.4 7.4 0 01-3.8-1l-.3-.2-3.9.8.8-3.8-.2-.3a7.4 7.4 0 1113.7-3.9A7.4 7.4 0 0112 21.4z"/>
                    </svg>
                </span>
                <span class="tarjeta-contacto-texto">
                    <span class="tarjeta-contacto-label mono">WhatsApp</span>
                    <span class="tarjeta-contacto-dato"><?= e(WHATSAPP_DISPLAY) ?></span>
                </span>
            </a>

            <div class="contacto-secundarias">
                <a class="tarjeta-contacto tarjeta-info tarjeta-ubicacion" href="<?= e(MAPS_URL) ?>" target="_blank" rel="noopener noreferrer">
                    <span class="tarjeta-contacto-icono" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-6.1 7-11.5A7 7 0 0 0 5 9.5C5 14.9 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.4"/></svg>
                    </span>
                    <span class="tarjeta-contacto-texto">
                        <span class="tarjeta-contacto-label mono">Ubicación</span>
                        <span class="tarjeta-contacto-linea1">500m de la plaza de deportes</span>
                        <span class="tarjeta-contacto-linea2">Santa Clara, Quesada · Retiro en tienda</span>
                    </span>
                </a>

                <!-- TODO: Confirmar horario con cliente -->
                <div class="tarjeta-contacto tarjeta-info tarjeta-horario">
                    <span class="tarjeta-contacto-icono" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3.2 1.8"/></svg>
                    </span>
                    <span class="tarjeta-contacto-texto">
                        <span class="tarjeta-contacto-label mono">Horario</span>
                        <span class="tarjeta-contacto-linea1">Lun a Sáb · 8:00 a.m. – 6:00 p.m.</span>
                        <span class="tarjeta-contacto-linea2">Domingo · 8:00 a.m. – 12:00 m.d.</span>
                    </span>
                </div>

                <a class="tarjeta-contacto tarjeta-info tarjeta-instagram" href="https://instagram.com/<?= e(INSTAGRAM_USUARIO) ?>" target="_blank" rel="noopener noreferrer">
                    <span class="tarjeta-contacto-icono" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="34" height="34">
                            <defs>
                                <linearGradient id="ig-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#F58529"/>
                                    <stop offset="50%" stop-color="#DD2A7B"/>
                                    <stop offset="100%" stop-color="#8134AF"/>
                                </linearGradient>
                            </defs>
                            <rect x="2" y="2" width="20" height="20" rx="5" fill="url(#ig-gradient)"/>
                            <circle cx="12" cy="12" r="4" fill="none" stroke="#fff" stroke-width="1.8"/>
                            <circle cx="17.5" cy="6.5" r="1.2" fill="#fff"/>
                        </svg>
                    </span>
                    <span class="tarjeta-contacto-texto">
                        <span class="tarjeta-contacto-label mono">Instagram</span>
                        <span class="tarjeta-contacto-handle">@<?= e(INSTAGRAM_USUARIO) ?></span>
                    </span>
                </a>

                <a class="tarjeta-contacto tarjeta-info tarjeta-tiktok" href="https://www.tiktok.com/@<?= e(TIKTOK_USUARIO) ?>" target="_blank" rel="noopener noreferrer">
                    <span class="tarjeta-contacto-icono" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="34" height="34">
                            <path transform="translate(-0.9,-0.9)" fill="#25F4EE" d="M16.6 5.82s.51.5 0 0A4.278 4.278 0 0115.54 3h-3.09v12.4a2.592 2.592 0 01-2.59 2.5c-1.42 0-2.6-1.16-2.6-2.6 0-1.72 1.66-3.01 3.37-2.48V9.66c-3.45-.46-6.47 2.22-6.47 5.64 0 3.33 2.76 5.7 5.69 5.7 3.14 0 5.69-2.55 5.69-5.7V9.01a7.35 7.35 0 004.29 1.38V7.3s-1.88.09-3.23-1.48z"/>
                            <path transform="translate(0.9,0.9)" fill="#FE2C55" d="M16.6 5.82s.51.5 0 0A4.278 4.278 0 0115.54 3h-3.09v12.4a2.592 2.592 0 01-2.59 2.5c-1.42 0-2.6-1.16-2.6-2.6 0-1.72 1.66-3.01 3.37-2.48V9.66c-3.45-.46-6.47 2.22-6.47 5.64 0 3.33 2.76 5.7 5.69 5.7 3.14 0 5.69-2.55 5.69-5.7V9.01a7.35 7.35 0 004.29 1.38V7.3s-1.88.09-3.23-1.48z"/>
                            <path fill="#000" d="M16.6 5.82s.51.5 0 0A4.278 4.278 0 0115.54 3h-3.09v12.4a2.592 2.592 0 01-2.59 2.5c-1.42 0-2.6-1.16-2.6-2.6 0-1.72 1.66-3.01 3.37-2.48V9.66c-3.45-.46-6.47 2.22-6.47 5.64 0 3.33 2.76 5.7 5.69 5.7 3.14 0 5.69-2.55 5.69-5.7V9.01a7.35 7.35 0 004.29 1.38V7.3s-1.88.09-3.23-1.48z"/>
                        </svg>
                    </span>
                    <span class="tarjeta-contacto-texto">
                        <span class="tarjeta-contacto-label mono">TikTok</span>
                        <span class="tarjeta-contacto-handle">@<?= e(TIKTOK_USUARIO) ?></span>
                    </span>
                </a>

                <a class="tarjeta-contacto tarjeta-info tarjeta-facebook" href="<?= e(FACEBOOK_URL) ?>" target="_blank" rel="noopener noreferrer">
                    <span class="tarjeta-contacto-icono" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="34" height="34">
                            <path d="M22 12a10 10 0 10-11.6 9.9v-7h-2.5V12h2.5v-2.2c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.7-1.6 1.5V12h2.7l-.4 2.9h-2.3v7A10 10 0 0022 12z" fill="#1877F2"/>
                        </svg>
                    </span>
                    <span class="tarjeta-contacto-texto">
                        <span class="tarjeta-contacto-label mono">Facebook</span>
                        <span class="tarjeta-contacto-handle">Tienda de pesca Santa Clara</span>
                    </span>
                </a>
            </div>
        </div>

        <!-- TODO: Reemplazar por iframe de Google Maps embebido cuando el cliente confirme dirección exacta -->
        <a class="contacto-mapa" href="<?= e(MAPS_URL) ?>" target="_blank" rel="noopener noreferrer">
            <span class="contacto-mapa-pin" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-6.1 7-11.5A7 7 0 0 0 5 9.5C5 14.9 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.4"/></svg>
            </span>
            <span class="contacto-mapa-texto mono">[ mapa de Google — Santa Clara, Quesada ]</span>
        </a>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
