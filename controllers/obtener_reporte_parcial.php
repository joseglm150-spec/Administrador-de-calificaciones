<?php
session_start();
require_once '../config/conection.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['rfc'])) {
    echo json_encode(['exito' => false, 'mensaje' => 'No hay sesión activa.']);
    exit;
}

$rfc_docente = $_SESSION['rfc'];
$clave_grupo = $_GET['grupo'] ?? '';

try {
    // 1. Obtener datos del grupo
    $stmtGrupo = $pdo->prepare(
        "SELECT g.id_grupo, g.clave_grupo, g.materia, g.periodo, g.aula, g.semestre, g.carrera,
                COALESCE(m.nombre_materia, g.materia) AS nombre_materia,
                COALESCE(m.total_unidades, 4) AS total_unidades,
                p.nombres, p.apellidos, p.grado_academico, p.departamento
         FROM grupos g
         LEFT JOIN materias m ON m.materia = g.materia
         LEFT JOIN personal p ON p.rfc = g.rfc_docente
         WHERE (g.clave_grupo = :cve OR g.id_grupo = :id_g) AND g.rfc_docente = :rfc
         LIMIT 1"
    );
    $stmtGrupo->execute([
        'cve' => $clave_grupo,
        'id_g' => is_numeric($clave_grupo) ? (int)$clave_grupo : 0,
        'rfc' => $rfc_docente
    ]);
    $grupo = $stmtGrupo->fetch(PDO::FETCH_ASSOC);

    if (!$grupo) {
        // Fallback: buscar el primer grupo del docente si no se especificó clave
        $stmtPrimerGpo = $pdo->prepare(
            "SELECT g.id_grupo, g.clave_grupo, g.materia, g.periodo, g.aula, g.semestre, g.carrera,
                    COALESCE(m.nombre_materia, g.materia) AS nombre_materia,
                    COALESCE(m.total_unidades, 4) AS total_unidades,
                    p.nombres, p.apellidos, p.grado_academico, p.departamento
             FROM grupos g
             LEFT JOIN materias m ON m.materia = g.materia
             LEFT JOIN personal p ON p.rfc = g.rfc_docente
             WHERE g.rfc_docente = :rfc AND g.estado = 'ABIERTO'
             ORDER BY g.clave_grupo ASC LIMIT 1"
        );
        $stmtPrimerGpo->execute(['rfc' => $rfc_docente]);
        $grupo = $stmtPrimerGpo->fetch(PDO::FETCH_ASSOC);
    }

    if (!$grupo) {
        echo json_encode(['exito' => false, 'mensaje' => 'No se encontraron grupos para este docente.']);
        exit;
    }

    $id_grupo = (int)$grupo['id_grupo'];
    $total_unidades = max(1, (int)$grupo['total_unidades']);

    // 2. Extraer alumnos inscritos
    $stmtAlu = $pdo->prepare(
        "SELECT a.no_de_control, a.apellidos, a.nombres
         FROM inscripciones_grupo ig
         INNER JOIN alumnos a ON a.no_de_control = ig.no_de_control
         WHERE ig.id_grupo = :id_grupo AND ig.estatus = 'INSCRITO'
         ORDER BY a.apellidos ASC, a.nombres ASC"
    );
    $stmtAlu->execute(['id_grupo' => $id_grupo]);
    $alumnos = $stmtAlu->fetchAll(PDO::FETCH_ASSOC);

    // 3. Extraer calificaciones por unidad de calificacion_evaluacion
    $stmtCalif = $pdo->prepare("SELECT no_de_control, unidad, calificacion, oportunidad FROM calificacion_evaluacion WHERE id_grupo = :id_grupo");
    $stmtCalif->execute(['id_grupo' => $id_grupo]);
    $califRaw = $stmtCalif->fetchAll(PDO::FETCH_ASSOC);

    $califMatriz = [];
    foreach ($califRaw as $c) {
        $califMatriz[$c['no_de_control']][$c['unidad']] = $c['calificacion'];
    }

    // Si para algún alumno faltan notas en calificacion_evaluacion, revisar si hay entregables
    $stmtEntNotas = $pdo->prepare(
        "SELECT ca.numero_control, e.unidad, ca.calificacion, e.valor
         FROM calificaciones_alumnos ca
         INNER JOIN entregables e ON e.id_entregable = ca.id_entregable
         WHERE e.id_grupo = :id_grupo"
    );
    $stmtEntNotas->execute(['id_grupo' => $id_grupo]);
    $entNotas = $stmtEntNotas->fetchAll(PDO::FETCH_ASSOC);

    $entPonderado = [];
    foreach ($entNotas as $en) {
        preg_match('/\d+/', $en['unidad'], $matches);
        $numU = isset($matches[0]) ? (int)$matches[0] : 1;
        $nc = $en['numero_control'];
        $val = floatval($en['calificacion'] ?? 0);
        $peso = floatval($en['valor'] ?? 0);
        if (!isset($entPonderado[$nc][$numU])) {
            $entPonderado[$nc][$numU] = 0;
        }
        $entPonderado[$nc][$numU] += ($val * ($peso / 100.0));
    }

    // Formatear filas de alumnos con unidades I a VIII, COMP y Promedio
    $filasAlumnos = [];
    $totalAlumnos = count($alumnos);
    $reprobadosPorUnidad = array_fill(1, 8, 0);
    $evaluadosPorUnidad = array_fill(1, 8, 0);

    $num = 1;
    foreach ($alumnos as $alu) {
        $nc = $alu['no_de_control'];
        $nombreCompleto = trim($alu['apellidos'] . ' ' . $alu['nombres']);

        $unidades = [];
        $sumaTotal = 0;
        $conteoCalif = 0;

        for ($u = 1; $u <= 8; $u++) {
            $nota = null;
            if (isset($califMatriz[$nc][$u])) {
                $nota = $califMatriz[$nc][$u];
            } elseif (isset($entPonderado[$nc][$u])) {
                $nota = round($entPonderado[$nc][$u]);
            }

            $unidades[$u] = $nota;

            if ($nota !== null && $nota !== '') {
                $notaNum = (int)$nota;
                $sumaTotal += $notaNum;
                $conteoCalif++;
                $evaluadosPorUnidad[$u]++;
                if ($notaNum < 70) {
                    $reprobadosPorUnidad[$u]++;
                }
            }
        }

        $promedio = ($conteoCalif > 0) ? round($sumaTotal / $conteoCalif) : '';

        $filasAlumnos[] = [
            'num' => $num++,
            'no_control' => $nc,
            'nombre' => $nombreCompleto,
            'unidades' => $unidades,
            'comp' => '',
            'promedio' => $promedio
        ];
    }

    // Índices de reprobación por unidad
    $indicesReprobacion = [];
    for ($u = 1; $u <= 8; $u++) {
        if ($evaluadosPorUnidad[$u] > 0) {
            $pct = round(($reprobadosPorUnidad[$u] / $evaluadosPorUnidad[$u]) * 100);
            $indicesReprobacion[$u] = $pct . '%';
        } else {
            $indicesReprobacion[$u] = '0%';
        }
    }

    $nombreDocente = trim(($grupo['grado_academico'] ? $grupo['grado_academico'] . ' ' : '') . $grupo['nombres'] . ' ' . $grupo['apellidos']);
    if (empty($nombreDocente)) {
        $nombreDocente = 'SIZZO NAVARRO LIZETT';
    }

    echo json_encode([
        'exito' => true,
        'info' => [
            'materia' => strtoupper($grupo['nombre_materia']),
            'clave_materia' => $grupo['materia'],
            'maestro' => strtoupper($nombreDocente),
            'grupo' => $grupo['clave_grupo'],
            'periodo' => $grupo['periodo'] ?? 'AGO-DIC/2026',
            'departamento' => $grupo['departamento'] ?? 'DIVISIÓN DE INGENIERÍA EN TECNOLOGÍAS DE LA INFORMACIÓN Y COMUNICACIONES',
            'carrera' => $grupo['carrera'] ?? 'TIC'
        ],
        'alumnos' => $filasAlumnos,
        'indices_reprobacion' => $indicesReprobacion
    ]);

} catch (Exception $e) {
    echo json_encode(['exito' => false, 'mensaje' => $e->getMessage()]);
}
?>
