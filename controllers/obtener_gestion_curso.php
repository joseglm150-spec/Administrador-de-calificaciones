<?php
session_start();
require_once '../config/conection.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['rfc'])) {
    echo json_encode(['exito' => false, 'mensaje' => 'No hay sesión activa.']);
    exit;
}

$rfc_docente = $_SESSION['rfc'];
$parcial = $_GET['parcial'] ?? '1';
$periodo = $_GET['periodo'] ?? 'Agosto - diciembre 2026';

try {
    $stmt = $pdo->prepare("SELECT * FROM reporte_gestion_curso WHERE rfc_docente = :rfc AND parcial = :parcial ORDER BY id_reporte ASC");
    $stmt->execute(['rfc' => $rfcDocente ?? $rfc_docente, 'parcial' => $parcial]);
    $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Si no existen filas para este parcial, generarlas con base en los grupos y alumnos reales
    if (empty($filas)) {
        $stmtGrupos = $pdo->prepare(
            "SELECT g.id_grupo, g.clave_grupo, g.semestre, g.carrera,
                    COALESCE(m.nombre_materia, g.materia) AS asignatura,
                    COUNT(DISTINCT ig.no_de_control) AS total_alumnos
             FROM grupos g
             LEFT JOIN materias m ON m.materia = g.materia
             LEFT JOIN inscripciones_grupo ig ON ig.id_grupo = g.id_grupo AND ig.estatus = 'INSCRITO'
             WHERE g.rfc_docente = :rfc AND g.estado = 'ABIERTO'
             GROUP BY g.id_grupo
             ORDER BY g.clave_grupo ASC"
        );
        $stmtGrupos->execute(['rfc' => $rfc_docente]);
        $grupos = $stmtGrupos->fetchAll(PDO::FETCH_ASSOC);

        $unidadNum = (is_numeric($parcial)) ? (int)$parcial : 1;

        $filas = [];
        foreach ($grupos as $g) {
            $idG = (int)$g['id_grupo'];
            $total = (int)$g['total_alumnos'];

            // Conteo de aprobados (>=70) en la unidad correspondiente
            $stmtAprob = $pdo->prepare(
                "SELECT COUNT(*) FROM calificacion_evaluacion 
                 WHERE id_grupo = :id_g AND unidad = :u AND calificacion >= 70"
            );
            $stmtAprob->execute(['id_g' => $idG, 'u' => $unidadNum]);
            $aprob = (int)$stmtAprob->fetchColumn();

            // Si no hay calificaciones cargadas aún, por defecto ponemos aprobado = total
            if ($aprob == 0 && $total > 0) {
                // Verificar si hay reprobados
                $stmtReprob = $pdo->prepare("SELECT COUNT(*) FROM calificacion_evaluacion WHERE id_grupo = :id_g AND unidad = :u AND calificacion < 70");
                $stmtReprob->execute(['id_g' => $idG, 'u' => $unidadNum]);
                $reprob = (int)$stmtReprob->fetchColumn();
                if ($reprob == 0) {
                    $aprob = $total;
                }
            } else {
                $reprob = max(0, $total - $aprob);
            }

            $filas[] = [
                'id_grupo' => $idG,
                'asignatura' => $g['asignatura'],
                'unidad_semestre' => $g['semestre'] . '°',
                'carrera' => ($g['carrera'] === 'TIC' ? 'TICS' : $g['carrera']),
                'total_alumnos' => $total,
                'aprobados_ordinario' => $aprob,
                'reprobados_ordinario' => $reprob,
                'desertores' => 0
            ];
        }
    }

    echo json_encode(['exito' => true, 'parcial' => $parcial, 'filas' => $filas]);

} catch (Exception $e) {
    echo json_encode(['exito' => false, 'mensaje' => $e->getMessage()]);
}
?>
