<?php
// Validar que el usuario haya iniciado sesión
session_start();

if (!isset($_SESSION['rfc'])) {
    header('Location: ../../index.php');
    exit;
}

// Conexión a la base de datos
require_once '../../config/conection.php';

// Variables de sesión
$nombreUsuario = $_SESSION['nombre'];
$rfcDocente = $_SESSION['rfc'];

$grupos = [];
$error_db = null;

// Lógica para obtener grupos
try {
    $sql = "SELECT clave_grupo, materia, periodo, capacidad 
            FROM grupos 
            WHERE rfc_docente = :rfc AND estado = 'ABIERTO'";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['rfc' => $rfcDocente]);
    $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_db = "Error en la base de datos: " . $e->getMessage();
}
?>