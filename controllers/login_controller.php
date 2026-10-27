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

$rfc = strtoupper(trim($rfcIngresado));

if ($rfc === '' || $password === '') {
    header('Location: ../index.php?error=1');
    exit;
}

// seleccionar todas las columnas necesarias
$stmt = $pdo->prepare(
    'SELECT id_usuario, rfc, password_hash, nombre_usuario, rol 
     FROM usuarios 
     WHERE rfc = :rfc 
     LIMIT 1'
);

$stmt->execute(['rfc' => $rfc]);
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
$_SESSION['rfc'] = $rfc;
$_SESSION['nombre'] = $usuario['nombre_usuario'];
$_SESSION['rol'] = $usuario['rol'];

header('Location: ../views/layout/menu.php');
exit;