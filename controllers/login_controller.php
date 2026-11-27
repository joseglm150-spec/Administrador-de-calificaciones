<?php
require '../config/conection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

// Nota: Asegúrate de que el 'name' en tu formulario HTML sea realmente 'password_hash'
// De lo contrario, cámbialo por $_POST['password']
$rfcIngresado = $_POST['rfc'] ?? '';
$password = $_POST['password'] ?? '';

if (!is_string($rfcIngresado) || !is_string($password)) {
    header('Location: ../index.php?error=1');
    exit;
}

$rfcRaw = trim($rfcIngresado);
$rfc = strtoupper($rfcRaw);

if ($rfc === '' || $password === '') {
    header('Location: ../index.php?error=1');
    exit;
}

// Seleccionar usuario por RFC o por nombre_usuario
$stmt = $pdo->prepare(
    'SELECT u.id_usuario, u.rfc, u.password_hash, u.nombre_usuario, u.rol, 
            p.nombres, p.apellidos, p.grado_academico, p.departamento
     FROM usuarios u 
     LEFT JOIN personal p ON p.rfc = u.rfc
     WHERE u.rfc = :rfc OR u.nombre_usuario = :nombre_usuario
     LIMIT 1'
);

$stmt->execute([
    'rfc' => $rfc,
    'nombre_usuario' => $rfcRaw
]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// password_verify se encarga de validar la contraseña
if (!$usuario || !password_verify($password, $usuario['password_hash'])) {
    header('Location: ../index.php?error=1');
    exit;
}

$stmt = $pdo->prepare('UPDATE usuarios SET ultimo_acceso = CURRENT_TIMESTAMP WHERE id_usuario = :id_usuario');
$stmt->execute(['id_usuario' => $usuario['id_usuario']]);

session_start();
session_regenerate_id(true);
$_SESSION['id_usuario'] = $usuario['id_usuario'];
$_SESSION['rfc'] = $usuario['rfc'] ?? $rfc;
$_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];

$nombreCompleto = trim(($usuario['grado_academico'] ? $usuario['grado_academico'] . ' ' : '') . ($usuario['nombres'] ?? '') . ' ' . ($usuario['apellidos'] ?? ''));
if (empty(trim($nombreCompleto))) {
    $nombreCompleto = $usuario['nombre_usuario'];
}
$_SESSION['nombre'] = $nombreCompleto;
$_SESSION['nombre_completo'] = $nombreCompleto;
$_SESSION['rol'] = $usuario['rol'];
$_SESSION['departamento'] = $usuario['departamento'] ?? 'División de Tecnologías de la Información';

header('Location: ../views/layout/menu.php');
exit;