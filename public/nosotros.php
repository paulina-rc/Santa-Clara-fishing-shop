<?php
declare(strict_types=1);

$pagina_activa = 'nosotros';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

$meta_titulo = 'Sobre nosotros · Tienda de Pesca Santa Clara';
$meta_descripcion = 'Conoce la historia de Tienda de Pesca Santa Clara. Equipo de pesca deportiva en Santa Clara, San Carlos. Escríbenos por WhatsApp.';

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
            <div class="nosotros-foto" aria-hidden="true">
                <!-- TODO: Reemplazar con foto real que envíe el cliente -->
                <p class="nosotros-foto-caption mono">[ foto del interior de la tienda o del equipo ]</p>
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
            <a class="tarjeta-contacto tarjeta-whatsapp" href="<?= e(url_whatsapp('Hola, quisiera más información.')) ?>" target="_blank" rel="noopener">
                <span class="tarjeta-contacto-icono" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5.5h16v11H9.5L5.5 20v-3.5H4v-11Z"/></svg>
                </span>
                <span class="tarjeta-contacto-texto">
                    <span class="tarjeta-contacto-label mono">WhatsApp</span>
                    <span class="tarjeta-contacto-dato"><?= e(WHATSAPP_DISPLAY) ?></span>
                </span>
            </a>

            <div class="tarjeta-contacto tarjeta-info tarjeta-ubicacion">
                <span class="tarjeta-contacto-icono" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-6.1 7-11.5A7 7 0 0 0 5 9.5C5 14.9 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.4"/></svg>
                </span>
                <span class="tarjeta-contacto-texto">
                    <span class="tarjeta-contacto-label mono">Ubicación</span>
                    <span class="tarjeta-contacto-linea1">Santa Clara, San Carlos</span>
                    <span class="tarjeta-contacto-linea2">Alajuela, Costa Rica · Retiro en tienda</span>
                </span>
            </div>

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

            <a class="tarjeta-contacto tarjeta-info tarjeta-instagram" href="https://instagram.com/<?= e(INSTAGRAM_USUARIO) ?>" target="_blank" rel="noopener">
                <span class="tarjeta-contacto-icono" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4.2"/><circle cx="17.2" cy="6.8" r="1" fill="currentColor" stroke="none"/></svg>
                </span>
                <span class="tarjeta-contacto-texto">
                    <span class="tarjeta-contacto-label mono">Instagram</span>
                    <span class="tarjeta-contacto-handle">@<?= e(INSTAGRAM_USUARIO) ?></span>
                </span>
            </a>
        </div>

        <!-- TODO: Reemplazar por iframe de Google Maps embebido cuando el cliente confirme dirección exacta -->
        <a class="contacto-mapa" href="https://www.google.com/maps/search/?api=1&query=Santa+Clara+San+Carlos+Alajuela+Costa+Rica" target="_blank" rel="noopener">
            <span class="contacto-mapa-pin" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-6.1 7-11.5A7 7 0 0 0 5 9.5C5 14.9 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.4"/></svg>
            </span>
            <span class="contacto-mapa-texto mono">[ mapa de Google — Santa Clara, San Carlos ]</span>
        </a>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
