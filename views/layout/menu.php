<?php
// 1. Cargamos la lógica y los datos desde los controladores
require_once '../../controllers/grupo_controller.php';
require_once '../../controllers/calificacion_controller.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Control de calificaciones | Universidad</title>
    <link rel="stylesheet" href="../../assets/css/menu.css">
    <link rel="stylesheet" href="../../assets/css/menu_calificaciones.css">
</head>
<body>

<div class="layout">
    <aside>
        <div class="marca">🎓 Universidad<small>Gestión académica</small></div>
        <p class="etiqueta">Menú principal</p>
        <nav aria-label="Menú principal">
            <button class="menu-opcion" type="button" data-vista="inicio" data-titulo="Información del docente" aria-controls="contenido-principal"><span class="icono">⌂</span><span>Inicio</span></button>
            <button class="menu-opcion activo" type="button" data-vista="grupos" data-titulo="Grupos" aria-current="page" aria-controls="contenido-principal"><span class="icono">👥</span><span>Grupos</span></button>
            <button class="menu-opcion" type="button" data-vista="documentos" data-titulo="Documentos de certificación" aria-controls="contenido-principal"><span class="icono">📄</span><span>Documentos de certificación</span></button>
            <button class="menu-opcion" type="button" data-vista="utileria" data-titulo="Utilería" aria-controls="contenido-principal"><span class="icono">🧰</span><span>Utilería</span></button>
            <button class="menu-opcion" type="button" data-vista="horario" data-titulo="Horario" aria-controls="contenido-principal"><span class="icono">📅</span><span>Horario</span></button>
        </nav>
    </aside>
    <main>
        <header>
            <h1><img class="logo-sirca-header" src="../../assets/img/SIRCA.jpeg" alt="SIRCA"><span id="titulo-seccion">Grupos</span></h1>
            <div class="usuario">Sesión: <strong><?= htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8') ?></strong></div>
        </header>
        <div class="contenido" id="contenido-principal" role="region" aria-live="polite" aria-labelledby="titulo-seccion">
            <section class="perfil-admin" aria-label="Información principal del docente">
                <div class="avatar-admin" aria-hidden="true">👤</div>
                <div class="datos-perfil-admin">
                    <h2><?= htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8') ?></h2>
                    <dl>
                        <div class="dato-admin"><dt>RFC</dt><dd><?= htmlspecialchars($rfcDocente, ENT_QUOTES, 'UTF-8') ?></dd></div>
                        <div class="dato-admin"><dt>Academia</dt><dd>No disponible</dd></div>
                    </dl>
                </div>
            </section>
        </div>
    </main>
</div>

<!-- Fragmentos de Vistas Inyectados -->
<template id="vista-grupos">
    <?php include 'template_grupos.php'; ?>
</template>

<template id="vista-calificaciones-grupo">
    <?php include 'template_calificaciones.php'; ?>
</template>

<template id="vista-documentos">
    <section class="bienvenida"><h2>Documentos de certificación</h2><p>Consulta la documentación.</p></section>
</template>

<template id="vista-utileria">
    <section class="bienvenida"><h2>Utilería</h2><p>Accede a las herramientas.</p></section>
</template>

<template id="vista-horario">
    <section class="bienvenida"><h2>Horario</h2><p>Consulta la organización de horarios.</p></section>
</template>

<script src="../../assets/js/menu.js" defer></script>
</body>
</html>