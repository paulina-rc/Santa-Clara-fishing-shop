<?php
declare(strict_types=1);

if (!defined('BASE_URL')) {
    http_response_code(404);
    exit;
}
?>
<footer class="pie">
    <div class="contenedor">
        <div class="pie-grid">
            <div class="pie-columna">
                <p class="pie-marca">Tienda de Pesca<br>Santa Clara</p>
                <p class="pie-descripcion">Equipo y accesorios de pesca para quienes disfrutan el río, el lago y el mar. Atención directa, sin intermediarios.</p>
            </div>

            <div class="pie-columna">
                <h4>Tienda</h4>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="productos.php">Todos los productos</a></li>
                    <li><a href="productos.php?cat=ofertas">Ofertas</a></li>
                </ul>
            </div>

            <div class="pie-columna">
                <h4>Contacto</h4>
                <ul>
                    <li><a href="<?= e(url_whatsapp('Hola, quiero más información sobre sus productos.')) ?>" target="_blank" rel="noopener">WhatsApp <?= e(WHATSAPP_DISPLAY) ?></a></li>
                    <li><a href="mailto:<?= e(EMAIL_CONTACTO) ?>"><?= e(EMAIL_CONTACTO) ?></a></li>
                    <li><a href="https://instagram.com/<?= e(INSTAGRAM_USUARIO) ?>" target="_blank" rel="noopener">@<?= e(INSTAGRAM_USUARIO) ?></a></li>
                    <li><span><?= e(DIRECCION) ?></span></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="pie-inferior">
        © <?= date('Y') ?> Tienda de Pesca Santa Clara. Todos los derechos reservados.
    </div>
</footer>

<script src="assets/js/publico.js"></script>
</body>
</html>
