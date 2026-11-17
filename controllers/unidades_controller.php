<?php
session_start();
// Asegúrate de que el nombre del archivo aquí sea exactamente el correcto
require_once '../config/conection.php'; 

header('Content-Type: application/json; charset=utf-8');

$id_grupo = isset($_GET['id_grupo']) ? intval($_GET['id_grupo']) : 85;

try {
    // 1. Obtener el total de unidades de la materia
    $sql_info = "
        SELECT m.total_unidades 
        FROM grupos g 
        INNER JOIN materias m ON g.materia = m.materia 
        WHERE g.id_grupo = ?
    ";
    $stmt_info = $pdo->prepare($sql_info);
    $stmt_info->execute([$id_grupo]);
    $info = $stmt_info->fetch(PDO::FETCH_ASSOC);
    $total_unidades = $info ? (int)$info['total_unidades'] : 4; 

    // 2. Obtener los alumnos inscritos
    $sql_alumnos = "
        SELECT a.no_de_control, a.apellidos, a.nombres 
        FROM alumnos a
        INNER JOIN inscripciones_grupo ig ON a.no_de_control = ig.no_de_control
        WHERE ig.id_grupo = ? AND ig.estatus = 'INSCRITO'
        ORDER BY a.apellidos, a.nombres ASC
    ";
    $stmt_al = $pdo->prepare($sql_alumnos);
    $stmt_al->execute([$id_grupo]);
    $alumnos = $stmt_al->fetchAll(PDO::FETCH_ASSOC);

    // 3. Obtener las calificaciones
    $sql_calif = "SELECT no_de_control, unidad, calificacion FROM calificacion_evaluacion WHERE id_grupo = ?";
    $stmt_calif = $pdo->prepare($sql_calif);
    $stmt_calif->execute([$id_grupo]);
    
    $calificaciones = [];
    while ($row = $stmt_calif->fetch(PDO::FETCH_ASSOC)) {
        $calificaciones[$row['no_de_control']][$row['unidad']] = $row['calificacion'];
    }

    // 4. Devolver la respuesta JSON
    echo json_encode([
        "estado" => "exito",
        "total_unidades" => $total_unidades,
        "alumnos" => $alumnos,
        "calificaciones" => $calificaciones
    ]);

} catch (PDOException $e) {
    // Si hay un error en la base de datos, devolvemos un JSON con el error para no romper JS
    echo json_encode([
        "estado" => "error",
        "mensaje" => $e->getMessage()
    ]);
}
exit();
?>