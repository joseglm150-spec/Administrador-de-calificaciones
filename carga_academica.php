<?php
declare(strict_types=1);
session_start();

// Página básica para cargar calificaciones. Configure estos datos con los de
// su entorno antes de usarla.
$host = 'localhost';
$db   = 'administrador_calificaciones';
$user = 'root';
$pass = '';

if (empty($_SESSION['csrf_token'])) {
	$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$mensaje = '';
$error = '';

try {
	$pdo = new PDO(
		"mysql:host={$host};dbname={$db};charset=utf8mb4",
		$user,
		$pass,
		[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
	);

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$token = (string)($_POST['csrf_token'] ?? '');
		if (!hash_equals($_SESSION['csrf_token'], $token)) {
			throw new RuntimeException('La sesión expiró. Recargue la página e inténtelo de nuevo.');
		}

		$estudianteId = filter_input(INPUT_POST, 'estudiante_id', FILTER_VALIDATE_INT);
		$asignaturaId = filter_input(INPUT_POST, 'asignatura_id', FILTER_VALIDATE_INT);
		$calificacion = filter_input(INPUT_POST, 'calificacion', FILTER_VALIDATE_FLOAT);
		$periodo = trim((string)($_POST['periodo'] ?? ''));

		if (!$estudianteId || !$asignaturaId || $calificacion === false ||
			$calificacion < 0 || $calificacion > 100 || $periodo === '' || mb_strlen($periodo) > 30) {
			throw new InvalidArgumentException('Verifique los datos. La calificación debe estar entre 0 y 100.');
		}

		// Ajuste nombres de tabla/campos si su esquema utiliza otros.
		$stmt = $pdo->prepare(
			'INSERT INTO calificaciones (estudiante_id, asignatura_id, periodo, calificacion)
			 VALUES (:estudiante, :asignatura, :periodo, :calificacion)'
		);
		$stmt->execute([
			':estudiante' => $estudianteId,
			':asignatura' => $asignaturaId,
			':periodo' => $periodo,
			':calificacion' => $calificacion,
		]);
		$mensaje = 'Calificación registrada correctamente.';
	}

	$estudiantes = $pdo->query('SELECT id, nombre FROM estudiantes ORDER BY nombre')->fetchAll(PDO::FETCH_ASSOC);
	$asignaturas = $pdo->query('SELECT id, nombre FROM asignaturas ORDER BY nombre')->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$error = $e instanceof PDOException
			? 'No se pudo guardar la calificación. Revise la conexión y la estructura de la base de datos.'
			: $e->getMessage();
	} else {
		$error = 'No se pudieron cargar los datos. Revise la conexión y las tablas estudiantes y asignaturas.';
	}
	$estudiantes = $estudiantes ?? [];
	$asignaturas = $asignaturas ?? [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Cargar calificaciones</title>
</head>
<body>
	<main>
		<h1>Cargar calificación</h1>
		<?php if ($mensaje !== ''): ?>
			<p role="status"><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?></p>
		<?php endif; ?>
		<?php if ($error !== ''): ?>
			<p role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
		<?php endif; ?>

		<form method="post" action="">
			<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
			<p>
				<label for="estudiante_id">Estudiante</label>
				<select id="estudiante_id" name="estudiante_id" required>
					<option value="">Seleccione un estudiante</option>
					<?php foreach ($estudiantes as $estudiante): ?>
						<option value="<?= (int)$estudiante['id'] ?>"><?= htmlspecialchars($estudiante['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<p>
				<label for="asignatura_id">Asignatura</label>
				<select id="asignatura_id" name="asignatura_id" required>
					<option value="">Seleccione una asignatura</option>
					<?php foreach ($asignaturas as $asignatura): ?>
						<option value="<?= (int)$asignatura['id'] ?>"><?= htmlspecialchars($asignatura['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<p>
				<label for="periodo">Periodo</label>
				<input id="periodo" name="periodo" type="text" maxlength="30" required>
			</p>
			<p>
				<label for="calificacion">Calificación (0–100)</label>
				<input id="calificacion" name="calificacion" type="number" min="0" max="100" step="0.01" required>
			</p>
			<button type="submit">Guardar calificación</button>
		</form>
	</main>
</body>
</html>
                                            