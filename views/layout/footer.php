<?php
$rf = 'index.php?controller=';

// Ubicación del mapa (puede ser una dirección o unas coordenadas)
$ubicacion    = 'C. Portal de Arriaga, 64, 01013 Vitoria-Gasteiz, Álava';
$ubicacionUrl = urlencode($ubicacion);   // convierte espacios y tildes para que valgan en un enlace

// Enlaces del pie. Si cambia alguna ruta, se toca aquí y listo.
$enlacesFooter = [
    'Inicio'         => $rf . 'AergibideController&accion=inicio',
    'Preguntas'      => $rf . 'PreguntasController&accion=preguntas',
    'Documentos'     => $rf . 'DocumentosController&accion=documentos',
    'Favoritos'      => $rf . 'FavoritosController&accion=favoritos',
    'Notificaciones' => $rf . 'NotificacionesController&accion=notificaciones'
];
?>
<footer class="footerWeb">

    <div class="footerTop">

        <div class="footerMarca">
            <img src="img/header/aergibide_blanco.png" alt="Aergibide SL">
            <p>El conocimiento también vuela más alto.</p>
            <p class="footerLema">Pregunta lo que necesites, comparte lo que sabes y aprende con tus compañeros.</p>
        </div>

        <nav class="footerNav" aria-label="Navegación del pie">
            <h4>NAVEGACIÓN</h4>
            <?php foreach ($enlacesFooter as $texto => $url): ?>
                <a href="<?= $url; ?>"><?= $texto; ?></a>
            <?php endforeach; ?>
        </nav>

        <div class="footerRedes">
            <h4>Síguenos</h4>
            <div class="iconosRedes">
                <a href="#" aria-label="Instagram">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="3" width="18" height="18" rx="5"/>
                        <circle cx="12" cy="12" r="4"/>
                        <circle cx="17.3" cy="6.7" r="1" fill="currentColor" stroke="none"/>
                    </svg>
                </a>
                <a href="#" aria-label="X">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M4 4l16 16M20 4L4 20"/>
                    </svg>
                </a>
                <a href="#" aria-label="Facebook">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor">
                        <path d="M14 8h3V4h-3a4 4 0 0 0-4 4v3H7v4h3v6h4v-6h3l1-4h-4V8z"/>
                    </svg>
                </a>
            </div>
            <p class="footerLugar">Estamos en Vitoria-Gasteiz, Álava.<br>¡Pásate cuando quieras!</p>
        </div>

        <div class="footerMapa">
            <h4>Encuéntranos</h4>
            <iframe
                title="Ubicación de Aergibide SL en Vitoria-Gasteiz"
                src="https://maps.google.com/maps?q=<?= $ubicacionUrl; ?>&amp;z=16&amp;output=embed"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
            <a class="verMapa" target="_blank" rel="noopener"
               href="https://www.google.com/maps?q=<?= $ubicacionUrl; ?>">
                Abrir en Google Maps &rsaquo;
            </a>
        </div>

    </div>

    <div class="footerBottom">
        <span>© <?= date('Y'); ?> Vertex. Todos los derechos reservados.</span>
        <span>Hecho con cariño por el equipo Vertex</span>
    </div>

</footer>