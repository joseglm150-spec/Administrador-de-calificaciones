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
			<button class="menu-opcion activo" type="button" data-vista="inicio" data-titulo="Información del docente" aria-current="page" aria-controls="contenido-principal"><span class="icono">⌂</span><span>Inicio</span></button>
			<button class="menu-opcion" type="button" data-vista="grupos" data-titulo="Grupos" aria-controls="contenido-principal"><span class="icono">👥</span><span>Grupos</span></button>
			<button class="menu-opcion" type="button" data-vista="documentos" data-titulo="Documentos de certificación" aria-controls="contenido-principal"><span class="icono">📄</span><span>Documentos de certificación</span></button>
			<button class="menu-opcion" type="button" data-vista="utileria" data-titulo="Utilería" aria-controls="contenido-principal"><span class="icono">🧰</span><span>Utilería</span></button>
			<button class="menu-opcion" type="button" data-vista="horario" data-titulo="Horario" aria-controls="contenido-principal"><span class="icono">📅</span><span>Horario</span></button>
			
	</aside>
	<main>
		<header><h1><img class="logo-sirca-header" src="assets/img/SIRCA.jpeg" alt="SIRCA"><span id="titulo-seccion">Información del docente</span></h1><div class="usuario">Sesión: <strong><?= htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8') ?></strong></div></header>
		<div class="contenido" id="contenido-principal" role="region" aria-live="polite" aria-labelledby="titulo-seccion">
			<section class="perfil-admin" aria-label="Información principal del docente">
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
	<div class="tabla-grupos-contenedor">
		<table class="tabla-grupos">
			<caption>Selecciona un grupo</caption>
			<thead>
				<tr>
					<th scope="col">Seleccionar</th>
					<th scope="col">Grupo</th>
					<th scope="col">Asignatura</th>
					<th scope="col">Periodo</th>
					<th scope="col">Alumnos inscritos</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><button class="boton-seleccionar-grupo" type="button" data-grupo-selector aria-pressed="false">Seleccionar</button></td>
					<th scope="row">081</th>
					<td>Fundamentos de programación</td>
					<td>Enero-Junio 2026</td>
					<td>26</td>
				</tr>
				<tr>
					<td><button class="boton-seleccionar-grupo" type="button" data-grupo-selector aria-pressed="false">Seleccionar</button></td>
					<th scope="row">083</th>
					<td>Cálculo diferencial</td>
					<td>Enero-Junio 2026</td>
					<td>18</td>
				</tr>
				<tr>
					<td><button class="boton-seleccionar-grupo" type="button" data-grupo-selector aria-pressed="false">Seleccionar</button></td>
					<th scope="row">085</th>
					<td>Bases de datos</td>
					<td>Enero-Junio 2026</td>
					<td>20</td>
				</tr>
			</tbody>
		</table>
	</div>
	
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

<script src="assets/js/menu.js" defer></script>
</body>
</html>
