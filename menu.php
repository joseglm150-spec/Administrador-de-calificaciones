<?php
$nombreUsuario = $_SESSION['nombre'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Control de calificaciones | Universidad</title>
	<link rel="stylesheet" href="assets/css/menu.css">
</head>
<body>
<div class="layout">
	<aside>
		<div class="marca">🎓 Universidad<small>Gestión académica</small></div>
		<p class="etiqueta">Menú principal</p>
		<nav aria-label="Menú principal">
			<button class="menu-opcion activo" type="button" data-vista="inicio" data-titulo="Información del administrador" aria-current="page" aria-controls="contenido-principal"><span class="icono">⌂</span><span>Inicio</span></button>
			<button class="menu-opcion" type="button" data-vista="grupos" data-titulo="Grupos" aria-controls="contenido-principal"><span class="icono">👥</span><span>Grupos</span></button>
			<button class="menu-opcion" type="button" data-vista="documentos" data-titulo="Documentos de certificación" aria-controls="contenido-principal"><span class="icono">📄</span><span>Documentos de certificación</span></button>
			<button class="menu-opcion" type="button" data-vista="utileria" data-titulo="Utilería" aria-controls="contenido-principal"><span class="icono">🧰</span><span>Utilería</span></button>
			<button class="menu-opcion" type="button" data-vista="horario" data-titulo="Horario" aria-controls="contenido-principal"><span class="icono">📅</span><span>Horario</span></button>
			<button class="menu-opcion" type="button" data-vista="asignaturas" data-titulo="Asignaturas" aria-controls="contenido-principal"><span class="icono">📚</span><span>Asignaturas</span></button>
		</nav>
	</aside>
	<main>
		<header><h1><img class="logo-sirca-header" src="assets/img/SIRCA.jpeg" alt="SIRCA"><span id="titulo-seccion">Información del administrador</span></h1><div class="usuario">Sesión: <strong><?= htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8') ?></strong></div></header>
		<div class="contenido" id="contenido-principal" role="region" aria-live="polite" aria-labelledby="titulo-seccion">
			<section class="perfil-admin" aria-label="Información principal del administrador">
				<div class="avatar-admin" aria-hidden="true">👤</div>
				<div class="datos-perfil-admin">
					<h2><?= htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8') ?></h2>
					<dl>
						<div class="dato-admin"><dt>Academia</dt><dd>No disponible</dd></div>
						<div class="dato-admin"><dt>Número de control</dt><dd>No disponible</dd></div>
					</dl>
				</div>
			</section>
		</div>
	</main>
</div>
<template id="vista-grupos">
	<section class="bienvenida"><h2>Grupos</h2><p>Consulta y organiza los grupos académicos.</p></section>
	<article class="tarjeta vista-informativa"><div class="icono-tarjeta">👥</div><h3>Gestión de grupos</h3><p>En esta sección podrás revisar la información de los grupos y sus integrantes.</p><p class="aviso">La vista está preparada. El listado de grupos estará disponible cuando se conecten sus datos.</p></article>
</template>
<template id="vista-documentos">
	<section class="bienvenida"><h2>Documentos de certificación</h2><p>Consulta la documentación relacionada con los procesos de certificación.</p></section>
	<article class="tarjeta vista-informativa"><div class="icono-tarjeta">📄</div><h3>Documentación</h3><p>En esta sección podrás consultar y organizar los documentos requeridos para certificación.</p><p class="aviso">La vista está preparada. Los documentos estarán disponibles cuando se conecten sus datos.</p></article>
</template>
<template id="vista-utileria">
	<section class="bienvenida"><h2>Utilería</h2><p>Accede a las herramientas de apoyo para la gestión académica.</p></section>
	<article class="tarjeta vista-informativa"><div class="icono-tarjeta">🧰</div><h3>Herramientas</h3><p>En esta sección se podrán reunir utilidades y acciones de mantenimiento del sistema.</p><p class="aviso">La vista está preparada. Las herramientas se agregarán cuando se definan sus funciones.</p></article>
</template>
<template id="vista-horario">
	<section class="bienvenida"><h2>Horario</h2><p>Consulta la organización de horarios académicos.</p></section>
	<article class="tarjeta vista-informativa"><div class="icono-tarjeta">📅</div><h3>Horario académico</h3><p>En esta sección podrás consultar los horarios de grupos y asignaturas.</p><p class="aviso">La vista está preparada. El horario estará disponible cuando se conecten sus datos.</p></article>
</template>
<template id="vista-asignaturas">
	<section class="bienvenida"><h2>Asignaturas</h2><p>Consulta las asignaturas disponibles en el sistema.</p></section>
	<article class="tarjeta vista-informativa"><div class="icono-tarjeta">📚</div><h3>Catálogo de asignaturas</h3><p>En esta sección podrás revisar la información de las asignaturas académicas.</p><p class="aviso">La vista está preparada. El catálogo estará disponible cuando se conecten sus datos.</p></article>
</template>
<script src="assets/js/menu.js" defer></script>
</body>
</html>
