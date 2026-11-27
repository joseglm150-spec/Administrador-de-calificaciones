<?php
session_start();
require_once '../config/conection.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['rfc'])) {
    echo json_encode(['exito' => false, 'mensaje' => 'No hay sesión activa.']);
    exit;
}

$json = file_get_contents('php://input');
$datos = json_decode($json, true);

if (!$datos || !isset($datos['filas'])) {
    echo json_encode(['exito' => false, 'mensaje' => 'Datos inválidos.']);
    exit;
}

$rfc_docente = $_SESSION['rfc'];
$periodo = $datos['periodo'] ?? 'Agosto - diciembre 2026';
$parcial = $datos['parcial'] ?? '1';
$filas = $datos['filas'];

try {
    $pdo->beginTransaction();

    $stmtUpsert = $pdo->prepare(
        "INSERT INTO reporte_gestion_curso 
            (rfc_docente, periodo, parcial, id_grupo, asignatura, unidad_semestre, carrera, total_alumnos, aprobados_ordinario, reprobados_ordinario, desertores)
         VALUES 
            (:rfc, :periodo, :parcial, :id_grupo, :asig, :unid, :carr, :total, :aprob, :reprob, :des)
         ON DUPLICATE KEY UPDATE 
            total_alumnos = VALUES(total_alumnos),
            aprobados_ordinario = VALUES(aprobados_ordinario),
            reprobados_ordinario = VALUES(reprobados_ordinario),
            desertores = VALUES(desertores)"
    );

    foreach ($filas as $f) {
        $id_grupo = (int)($f['id_grupo'] ?? 0);
        $asig = $f['asignatura'] ?? '';
        $unid = $f['unidad_semestre'] ?? '';
        $carr = $f['carrera'] ?? '';
        $total = (int)($f['total_alumnos'] ?? 0);
        $aprob = (int)($f['aprobados_ordinario'] ?? 0);
        $reprob = (int)($f['reprobados_ordinario'] ?? 0);
        $des = (int)($f['desertores'] ?? 0);

        $stmtUpsert->execute([
            'rfc' => $rfc_docente,
            'periodo' => $periodo,
            'parcial' => $parcial,
            'id_grupo' => $id_grupo,
            'asig' => $asig,
            'unid' => $unid,
            'carr' => $carr,
            'total' => $total,
            'aprob' => $aprob,
            'reprob' => $reprob,
            'des' => $des
        ]);
    }

    $pdo->commit();
    echo json_encode(['exito' => true, 'mensaje' => 'Reporte de gestión guardado correctamente en la base de datos.']);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['exito' => false, 'mensaje' => $e->getMessage()]);
}
?>
