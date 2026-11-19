<?php
/** Logo: cono + nombre del negocio. Se usa en la cabecera, el pie y el panel. */
$nombre = ajuste('negocio_nombre');
?>
<span class="logo">
    <svg class="logo-cono" viewBox="0 0 40 56" aria-hidden="true">
        <path d="M12 30 L20 55 L28 30 Z" fill="#c98a4b" stroke="#4a2a12" stroke-width="1.5"/>
        <path d="M14 33 L20 52 M22 33 L20 52 M18 33 L20 52 M26 33 L20 52" stroke="#8a5a2b" stroke-width="1"/>
        <circle cx="20" cy="20" r="11" fill="#ffd6e7" stroke="#4a2a12" stroke-width="1.5"/>
        <circle cx="14" cy="16" r="5" fill="#f63d8f" stroke="#4a2a12" stroke-width="1"/>
        <circle cx="25" cy="15" r="4" fill="#fff" stroke="#4a2a12" stroke-width="1"/>
        <circle cx="20" cy="7" r="3" fill="#e0263f"/>
    </svg>
    <span class="logo-texto">
        <small>Heladería</small>
        <b><?= e(str_replace('Heladería ', '', $nombre)) ?></b>
    </span>
</span>
