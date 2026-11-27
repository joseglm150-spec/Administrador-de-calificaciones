<?php
session_start();
require_once '../config/conection.php'; 

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['rfc'])) {
    echo json_encode(['exito' => false, 'mensaje' => 'No hay sesión activa']);
    exit;
}

$clave_grupo = $_GET['grupo'] ?? '';
$rfc_docente = $_SESSION['rfc'];

try {
    // 1. Validar que el grupo exista y le pertenezca al docente
    $stmtGrupo = $pdo->prepare(
        "SELECT g.id_grupo, g.clave_grupo, g.materia, g.periodo, g.aula, g.semestre, g.carrera,
                COALESCE(m.nombre_materia, g.materia) AS nombre_materia, 
                COALESCE(m.total_unidades, 4) AS total_unidades
         FROM grupos g
         LEFT JOIN materias m ON m.materia = g.materia
         WHERE (g.clave_grupo = :clave OR g.id_grupo = :id_g) AND g.rfc_docente = :rfc 
         LIMIT 1"
    );
    $stmtGrupo->execute([
        'clave' => $clave_grupo, 
        'id_g' => is_numeric($clave_grupo) ? (int)$clave_grupo : 0, 
        'rfc' => $rfc_docente
    ]);
    $grupo = $stmtGrupo->fetch(PDO::FETCH_ASSOC);

    if (!$grupo) {
        echo json_encode(['exito' => false, 'mensaje' => 'Grupo no encontrado']);
        exit;
    }
    $id_grupo = (int)$grupo['id_grupo'];
    $total_unidades = max(1, (int)$grupo['total_unidades']);

    // 1.5 Extraer los alumnos inscritos en este grupo
    $stmtAlu = $pdo->prepare(
        "SELECT a.no_de_control, a.apellidos, a.nombres, a.carrera
         FROM inscripciones_grupo ig
         INNER JOIN alumnos a ON a.no_de_control = ig.no_de_control
         WHERE ig.id_grupo = :id_grupo AND ig.estatus = 'INSCRITO'
         ORDER BY a.apellidos ASC, a.nombres ASC"
    );
    $stmtAlu->execute(['id_grupo' => $id_grupo]);
    $alumnos = $stmtAlu->fetchAll(PDO::FETCH_ASSOC);

    // 2. Extraer todas las columnas (Entregables) creadas para este grupo
    $stmtEnt = $pdo->prepare("SELECT id_entregable, unidad, nombre, valor FROM entregables WHERE id_grupo = :id_grupo ORDER BY id_entregable ASC");
    $stmtEnt->execute(['id_grupo' => $id_grupo]);
    $entregables = $stmtEnt->fetchAll(PDO::FETCH_ASSOC);

    // Si el grupo no tiene entregables, inicializar entregables por defecto para cada unidad
    if (empty($entregables)) {
        $stmtInsEnt = $pdo->prepare("INSERT INTO entregables (id_grupo, unidad, nombre, valor) VALUES (:id_g, :unidad, :nombre, :valor)");
        for ($u = 1; $u <= $total_unidades; $u++) {
            $nombreU = "Unidad $u";
            $stmtInsEnt->execute(['id_g' => $id_grupo, 'unidad' => $nombreU, 'nombre' => 'Examen', 'valor' => 70.00]);
            $stmtInsEnt->execute(['id_g' => $id_grupo, 'unidad' => $nombreU, 'nombre' => 'Tareas', 'valor' => 20.00]);
            $stmtInsEnt->execute(['id_g' => $id_grupo, 'unidad' => $nombreU, 'nombre' => 'Proyecto', 'valor' => 10.00]);
        }
        $stmtEnt->execute(['id_grupo' => $id_grupo]);
        $entregables = $stmtEnt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Extraer las calificaciones de los entregables
    $calificaciones = [];
    if (!empty($entregables)) {
        $ids = array_column($entregables, 'id_entregable');
        $in  = str_repeat('?,', count($ids) - 1) . '?';
        $stmtCal = $pdo->prepare("SELECT numero_control, id_entregable, calificacion FROM calificaciones_alumnos WHERE id_entregable IN ($in)");
        $stmtCal->execute($ids);
        $calificaciones = $stmtCal->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. Extraer calificaciones oficiales por unidad (calificacion_evaluacion)
    $stmtCalUnidades = $pdo->prepare("SELECT no_de_control, unidad, calificacion FROM calificacion_evaluacion WHERE id_grupo = :id_grupo");
    $stmtCalUnidades->execute(['id_grupo' => $id_grupo]);
    $calificaciones_unidades = $stmtCalUnidades->fetchAll(PDO::FETCH_ASSOC);

    // Devolvemos todo empaquetado a JavaScript
    echo json_encode([
        'exito' => true,
        'grupo' => $grupo,
        'total_unidades' => $total_unidades,
        'alumnos' => $alumnos,
        'entregables' => $entregables,
        'calificaciones' => $calificaciones,
        'calificaciones_unidades' => $calificaciones_unidades
    ]);

} catch (Exception $e) {
    echo json_encode(['exito' => false, 'mensaje' => $e->getMessage()]);
}
?>
