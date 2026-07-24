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
            <!-- TODO: Reemplazar con foto real que envíe el cliente -->
            <div class="nosotros-foto" aria-hidden="true"></div>
            <p class="nosotros-foto-caption mono">[ foto del interior de la tienda o del equipo ]</p>
        </div>
    </div>
</section>

<section class="nosotros-contacto" id="contacto">
    <div class="nosotros-contacto-contenido">
        <div class="nosotros-contacto-header">
            <p class="nosotros-contacto-eyebrow mono">Contáctanos</p>
            <h2 class="nosotros-contacto-titulo">Escríbenos, te respondemos rápido</h2>
            <p class="nosotros-contacto-subtitulo">Estamos para ayudarte a encontrar el equipo de pesca ideal, resolver dudas o coordinar tu compra.</p>
        </div>

        <div class="grid-contacto">
            <div class="tarjeta-contacto">
                <span class="tarjeta-contacto-icono" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5.1-1.3A10 10 0 1 0 12 2Zm0 18.2a8.1 8.1 0 0 1-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1-.2.2-.7.8-.8.9-.2.2-.3.2-.5.1-.2-.1-1-.4-1.9-1.2-.7-.6-1.2-1.4-1.3-1.6-.1-.2 0-.4.1-.5.1-.1.2-.3.4-.4.1-.1.2-.3.2-.4.1-.2 0-.3 0-.5 0-.1-.6-1.5-.8-2-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.2-.9.9-.9 2.2s1 2.6 1.1 2.7c.1.2 2 3 4.8 4.2.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.3-.2-.5-.3Z"/></svg>
                </span>
                <p class="tarjeta-contacto-etiqueta mono">WhatsApp</p>
                <p class="tarjeta-contacto-dato"><?= e(WHATSAPP_DISPLAY) ?></p>
                <a class="boton boton-dorado boton-pequeno" href="<?= e(url_whatsapp('Hola, quisiera más información.')) ?>" target="_blank" rel="noopener">Escribir</a>
            </div>

            <div class="tarjeta-contacto">
                <span class="tarjeta-contacto-icono" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4.2"/><circle cx="17.2" cy="6.8" r="1" fill="currentColor" stroke="none"/></svg>
                </span>
                <p class="tarjeta-contacto-etiqueta mono">Instagram</p>
                <p class="tarjeta-contacto-dato">@<?= e(INSTAGRAM_USUARIO) ?></p>
                <a class="boton boton-ghost-claro boton-pequeno" href="https://instagram.com/<?= e(INSTAGRAM_USUARIO) ?>" target="_blank" rel="noopener">Ver perfil</a>
            </div>

            <div class="tarjeta-contacto">
                <span class="tarjeta-contacto-icono" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-6.1 7-11.5A7 7 0 0 0 5 9.5C5 14.9 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.4"/></svg>
                </span>
                <p class="tarjeta-contacto-etiqueta mono">Estamos en</p>
                <p class="tarjeta-contacto-dato"><?= e(DIRECCION) ?></p>
                <a class="boton boton-ghost-claro boton-pequeno" href="https://www.google.com/maps/search/?api=1&query=<?= urlencode(DIRECCION) ?>" target="_blank" rel="noopener">Ver en Google Maps</a>
            </div>
        </div>

        <!-- TODO: Confirmar horario con cliente -->
        <p class="nosotros-horario">Atención de lunes a sábado, 8:00 a.m. a 5:00 p.m.</p>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
