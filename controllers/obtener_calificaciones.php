<?php
session_start();
require_once '../config/conection.php'; 

header('Content-Type: application/json');

if (!isset($_SESSION['rfc'])) {
    echo json_encode(['exito' => false, 'mensaje' => 'No hay sesión activa']);
    exit;
}

$clave_grupo = $_GET['grupo'] ?? '';
$rfc_docente = $_SESSION['rfc'];

try {
    // 1. Validar que el grupo exista y le pertenezca al docente
    $stmtGrupo = $pdo->prepare("SELECT id_grupo FROM grupos WHERE clave_grupo = :clave AND rfc_docente = :rfc LIMIT 1");
    $stmtGrupo->execute(['clave' => $clave_grupo, 'rfc' => $rfc_docente]);
    $grupo = $stmtGrupo->fetch(PDO::FETCH_ASSOC);

    if (!$grupo) {
        echo json_encode(['exito' => false, 'mensaje' => 'Grupo no encontrado']);
        exit;
    }
    $id_grupo = $grupo['id_grupo'];

    // 2. Extraer todas las columnas (Entregables) creadas para este grupo
    $stmtEnt = $pdo->prepare("SELECT id_entregable, unidad, nombre FROM entregables WHERE id_grupo = :id_grupo ORDER BY id_entregable ASC");
    $stmtEnt->execute(['id_grupo' => $id_grupo]);
    $entregables = $stmtEnt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Extraer las calificaciones de los alumnos
    $calificaciones = [];
    if (!empty($entregables)) {
        // Extraemos solo los IDs de los entregables para buscar sus notas
        $ids = array_column($entregables, 'id_entregable');
        $in  = str_repeat('?,', count($ids) - 1) . '?'; // Crea signos de interrogación dinámicos
        
        $stmtCal = $pdo->prepare("SELECT numero_control, id_entregable, calificacion FROM calificaciones_alumnos WHERE id_entregable IN ($in)");
        $stmtCal->execute($ids);
        $calificaciones = $stmtCal->fetchAll(PDO::FETCH_ASSOC);
    }

    // Devolvemos todo empaquetado a JavaScript
    echo json_encode([
        'exito' => true,
        'entregables' => $entregables,
        'calificaciones' => $calificaciones
    ]);

} catch (Exception $e) {
    echo json_encode(['exito' => false, 'mensaje' => $e->getMessage()]);
}
?>