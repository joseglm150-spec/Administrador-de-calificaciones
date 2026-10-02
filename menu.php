<?php
$nombreUsuario = $_SESSION['nombre'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Control de calificaciones | Universidad</title>
	<style>
		:root{--azul:#173b67;--azul2:#24558c;--fondo:#f3f6fb;--texto:#26364a;--gris:#718096;--borde:#e3eaf2}
		*{box-sizing:border-box}body{margin:0;background:var(--fondo);color:var(--texto);font-family:Arial,"Segoe UI",sans-serif}
		.layout{min-height:100vh;display:flex}aside{width:245px;flex:none;padding:25px 16px;background:var(--azul);color:#fff}
		.marca{padding:4px 10px 28px;font-size:17px;font-weight:bold}.marca small{display:block;margin-top:5px;color:#c7d7ea;font-size:12px;font-weight:normal}
		.etiqueta{margin:0 10px 10px;color:#afc4dd;font-size:11px;text-transform:uppercase;letter-spacing:1px}
		nav a{display:flex;gap:12px;align-items:center;padding:12px 13px;margin:4px 0;border-radius:8px;color:#e9f1fa;text-decoration:none;font-size:14px}
		nav a:hover,nav a.activo{background:var(--azul2);color:#fff}nav .icono{width:20px;text-align:center}
		main{flex:1;min-width:0}header{height:70px;display:flex;align-items:center;justify-content:space-between;padding:0 32px;background:#fff;border-bottom:1px solid var(--borde)}
		header h1{margin:0;font-size:19px}.usuario{font-size:14px;color:var(--gris)}.usuario strong{color:var(--texto)}
		.contenido{max-width:1180px;margin:auto;padding:32px}.bienvenida{margin-bottom:28px}.bienvenida h2{margin:0 0 8px;color:var(--azul);font-size:25px}.bienvenida p{margin:0;color:var(--gris);line-height:1.5}
		.seccion{margin:27px 0 14px;font-size:17px}.tarjetas{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:17px}
		.tarjeta{padding:21px;background:#fff;border:1px solid var(--borde);border-radius:11px;box-shadow:0 3px 12px #1c375a0a}
		.icono-tarjeta{width:42px;height:42px;display:grid;place-items:center;border-radius:10px;background:#edf4fc;font-size:21px}
		.tarjeta h3{margin:15px 0 7px;font-size:16px}.tarjeta p{min-height:38px;margin:0 0 14px;color:var(--gris);font-size:13px;line-height:1.5}
		.tarjeta a{color:var(--azul2);font-size:13px;font-weight:bold;text-decoration:none}.tarjeta a:hover{text-decoration:underline}
		.aviso{margin-top:22px;padding:15px 17px;border-left:4px solid #4d83bd;border-radius:6px;background:#eaf2fb;color:#50647b;font-size:13px;line-height:1.5}
		@media(max-width:850px){aside{width:210px}.tarjetas{grid-template-columns:repeat(2,minmax(0,1fr))}}
		@media(max-width:600px){.layout{display:block}aside{width:100%;padding:15px}.marca{padding-bottom:14px}nav{display:flex;overflow-x:auto}nav a{flex:none;padding:10px}nav a span:last-child{display:none}.etiqueta{display:none}header{height:auto;min-height:62px;padding:12px 17px}.contenido{padding:22px 17px}.tarjetas{grid-template-columns:1fr}}
	</style>
</head>
<body>
<div class="layout">
	<aside>
		<div class="marca">🎓 Universidad<small>Gestión académica</small></div>
		<p class="etiqueta">Menú principal</p>
		<nav aria-label="Menú principal">
			<a class="activo" href="menu.php"><span class="icono">⌂</span><span>Inicio</span></a>
			<a href="calificaciones.php"><span class="icono">▤</span><span>Consultar calificaciones</span></a>
			<a href="agregar_calificacion.php"><span class="icono">＋</span><span>Agregar calificación</span></a>
			<a href="editar_calificacion.php"><span class="icono">✎</span><span>Corregir calificación</span></a>
			<a href="estudiantes.php"><span class="icono">♙</span><span>Estudiantes</span></a>
			<a href="materias.php"><span class="icono">▣</span><span>Asignaturas</span></a>
		</nav>
	</aside>
	<main>
		<header><h1>Control de calificaciones</h1><div class="usuario">Sesión: <strong><?= htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8') ?></strong></div></header>
		<div class="contenido">
			<section class="bienvenida"><h2>Panel académico</h2><p>Administra las calificaciones y consulta la información académica de los estudiantes.</p></section>
			<h2 class="seccion">Gestión de calificaciones</h2>
			<section class="tarjetas" aria-label="Gestión de calificaciones">
				<article class="tarjeta"><div class="icono-tarjeta">📋</div><h3>Consultar calificaciones</h3><p>Busca y revisa las notas por estudiante o asignatura.</p><a href="calificaciones.php">Ver calificaciones →</a></article>
				<article class="tarjeta"><div class="icono-tarjeta">➕</div><h3>Agregar calificación</h3><p>Registra una nueva nota para un estudiante y asignatura.</p><a href="agregar_calificacion.php">Registrar nota →</a></article>
				<article class="tarjeta"><div class="icono-tarjeta">✏️</div><h3>Corregir calificación</h3><p>Actualiza el valor de una calificación previamente registrada.</p><a href="editar_calificacion.php">Editar nota →</a></article>
			</section>
			<h2 class="seccion">Información académica</h2>
			<section class="tarjetas" aria-label="Información académica">
				<article class="tarjeta"><div class="icono-tarjeta">👥</div><h3>Estudiantes</h3><p>Consulta el listado de estudiantes registrados.</p><a href="estudiantes.php">Ver estudiantes →</a></article>
				<article class="tarjeta"><div class="icono-tarjeta">📚</div><h3>Asignaturas</h3><p>Consulta las asignaturas disponibles en el sistema.</p><a href="materias.php">Ver asignaturas →</a></article>
				<article class="tarjeta"><div class="icono-tarjeta">ℹ️</div><h3>Registro responsable</h3><p>Verifica estudiante, asignatura y período antes de guardar cambios.</p></article>
			</section>
			<p class="aviso">Confirma los datos del estudiante y la asignatura antes de guardar o corregir una calificación.</p>
		</div>
	</main>
</div>
</body>
</html>
