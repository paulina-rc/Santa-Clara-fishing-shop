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
                    <svg viewBox="0 0 32 32" width="22" height="22" fill="#fff" xmlns="http://www.w3.org/2000/svg"><path d="M16.001 3C9.373 3 4 8.373 4 15c0 2.117.554 4.108 1.522 5.833L4 27l6.336-1.499A11.94 11.94 0 0016.001 27C22.628 27 28 21.627 28 15S22.628 3 16.001 3zm0 21.6c-1.816 0-3.517-.494-4.976-1.351l-.357-.212-3.762.89.895-3.665-.233-.376A9.548 9.548 0 016.4 15c0-5.293 4.308-9.6 9.601-9.6 5.293 0 9.6 4.307 9.6 9.6 0 5.293-4.307 9.6-9.6 9.6zm5.276-7.194c-.29-.145-1.714-.845-1.979-.94-.265-.096-.458-.145-.65.146-.194.29-.746.94-.915 1.134-.169.194-.338.218-.628.073-.29-.145-1.226-.452-2.335-1.44-.863-.769-1.446-1.72-1.615-2.01-.169-.29-.018-.446.127-.591.13-.129.29-.338.435-.507.145-.169.194-.29.29-.483.097-.194.048-.363-.024-.508-.073-.145-.65-1.568-.891-2.148-.234-.564-.472-.488-.65-.497l-.554-.01a1.06 1.06 0 00-.77.362c-.264.29-1.007.985-1.007 2.402 0 1.417 1.031 2.787 1.175 2.98.145.193 2.032 3.098 4.925 4.345.688.297 1.225.474 1.643.607.69.219 1.319.188 1.815.114.554-.083 1.714-.7 1.957-1.376.242-.677.242-1.257.169-1.377-.073-.12-.264-.194-.554-.339z"/></svg>
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

        <!-- TODO: Reemplazar src del iframe con el embed exacto de Google Maps.
             Ir a maps.google.com → buscar "500m plaza deportes Santa Clara Quesada"
             → Compartir → Incorporar un mapa → copiar src del iframe -->
        <div class="contacto-mapa">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15723.842!2d-84.5253!3d10.3271!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2sSanta%20Clara%2C%20Quesada%2C%20Costa%20Rica!5e0!3m2!1ses!2scr!4v1700000000"
                width="100%"
                height="100%"
                style="border:0;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                title="Ubicación Tienda de Pesca Santa Clara">
            </iframe>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
