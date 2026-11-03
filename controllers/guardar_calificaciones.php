<?php
session_start();
// Ajusta la ruta de tu conexión
require_once '../config/conection.php'; 

header('Content-Type: application/json');

if (!isset($_SESSION['rfc'])) {
    echo json_encode(['exito' => false, 'mensaje' => 'No hay sesión activa.']);
    exit;
}

$json = file_get_contents('php://input');
$datos = json_decode($json, true);

if (!$datos) {
    echo json_encode(['exito' => false, 'mensaje' => 'No se recibieron datos válidos.']);
    exit;
}

$clave_grupo = $datos['clave_grupo'];
$paquete_unidades = $datos['unidades']; // Ahora recibimos el arreglo de 5 unidades
$rfc_docente = $_SESSION['rfc'];

try {
    $pdo->beginTransaction();

    // 1. Verificar el grupo
    $stmtGrupo = $pdo->prepare("SELECT id_grupo FROM grupos WHERE clave_grupo = :clave AND rfc_docente = :rfc LIMIT 1");
    $stmtGrupo->execute(['clave' => $clave_grupo, 'rfc' => $rfc_docente]);
    $grupo = $stmtGrupo->fetch(PDO::FETCH_ASSOC);

    if (!$grupo) {
        throw new Exception("El grupo no existe o no te pertenece.");
    }
    $id_grupo = $grupo['id_grupo'];

    // Preparamos las consultas (se preparan una vez por eficiencia)
    $stmtDelete = $pdo->prepare("DELETE FROM entregables WHERE id_grupo = :id_grupo AND unidad = :unidad");
    $stmtInsertCol = $pdo->prepare("INSERT INTO entregables (id_grupo, unidad, nombre) VALUES (:id_grupo, :unidad, :nombre)");
    $stmtInsertNota = $pdo->prepare("INSERT INTO calificaciones_alumnos (numero_control, id_entregable, calificacion) VALUES (:num_control, :id_entregable, :calif)");

    // 2. Recorremos cada unidad recibida (Unidad 1 a Unidad 5)
    foreach ($paquete_unidades as $data_unidad) {
        
        $nombre_unidad = $data_unidad['unidad'];
        $columnas = $data_unidad['columnas'];
        $alumnos = $data_unidad['alumnos'];

        // Sincronización: Limpiamos los entregables de ESTA unidad en ESTE grupo
        $stmtDelete->execute(['id_grupo' => $id_grupo, 'unidad' => $nombre_unidad]);

        // Insertar columnas de esta unidad
        $ids_entregables = [];
        foreach ($columnas as $nombre_columna) {
            $stmtInsertCol->execute([
                'id_grupo' => $id_grupo,
                'unidad' => $nombre_unidad,
                'nombre' => $nombre_columna
            ]);
            $ids_entregables[] = $pdo->lastInsertId();
        }

        // Insertar las calificaciones de esta unidad
        foreach ($alumnos as $alumno) {
            $num_control = $alumno['id_alumno'];
            
            foreach ($alumno['notas'] as $indice => $nota) {
                $valor_nota = ($nota === '') ? null : floatval($nota);
                
                if (isset($ids_entregables[$indice])) {
                    $stmtInsertNota->execute([
                        'num_control' => $num_control,
                        'id_entregable' => $ids_entregables[$indice],
                        'calif' => $valor_nota
                    ]);
                }
            }
        }
    }

    $pdo->commit();
    echo json_encode(['exito' => true, 'mensaje' => 'Guardado exitosamente.']);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['exito' => false, 'mensaje' => $e->getMessage()]);
}
?>