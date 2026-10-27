<?php
    // 1. Configuración de conexión a la base de datos (Ejemplo con PDO)
$host = 'localhost';
$dbname = 'sirca_db'; // Cambia por el nombre de tu base
$usuario_db = 'root';
$password_db = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $usuario_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>