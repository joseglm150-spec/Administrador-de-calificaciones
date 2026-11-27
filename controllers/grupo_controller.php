<?php
// Validar que el usuario haya iniciado sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['rfc'])) {
    header('Location: ../../index.php');
    exit;
}

// Conexión a la base de datos
require_once __DIR__ . '/../config/conection.php';

// Variables de sesión
$rfcDocente = $_SESSION['rfc'];
$nombreUsuario = $_SESSION['nombre'] ?? $_SESSION['rfc'];

$docente = [];
$grupos = [];
$horarios = [];
$horarioDocenteMatriz = [];
$macroplaneacion = [];
$reportesGestion = [];
$error_db = null;

try {
    // 1. Obtener datos completos del docente
    $stmtDoc = $pdo->prepare("SELECT * FROM personal WHERE rfc = :rfc LIMIT 1");
    $stmtDoc->execute(['rfc' => $rfcDocente]);
    $docente = $stmtDoc->fetch(PDO::FETCH_ASSOC);

    if ($docente) {
        $nombreCompleto = trim(($docente['grado_academico'] ? $docente['grado_academico'] . ' ' : '') . $docente['nombres'] . ' ' . $docente['apellidos']);
        $nombreFormal = trim($docente['apellidos'] . ' ' . $docente['nombres']);
        $docente['nombre_completo'] = $nombreCompleto;
        $docente['nombre_formal'] = $nombreFormal;
    } else {
        $docente = [
            'rfc' => $rfcDocente,
            'nombres' => $nombreUsuario,
            'apellidos' => '',
            'nombre_completo' => $nombreUsuario,
            'nombre_formal' => $nombreUsuario,
            'grado_academico' => 'MAN.',
            'tarjeta' => '1106',
            'curp' => 'SINL900223MMNZVZ00',
            'puesto' => 'DOCENTE',
            'departamento' => 'DIVISIÓN DE INGENIERÍA EN TECNOLOGÍAS DE LA INFORMACIÓN Y COMUNICACIONES',
            'escolaridad' => 'Licenciatura en: LICENCIADA EN INFORMÁTICA',
            'ingreso_plantel' => '201602',
            'ingreso_sep' => '201602',
            'h_f_g' => 21,
            'h_apoy' => 0,
            'h_adm' => 0,
            'plaza' => 'DOCENTE',
            'periodo_actual' => 'AGOSTO-DICIEMBRE/2026'
        ];
    }

    // 2. Obtener lista de grupos del docente con nombres de materia y conteo de alumnos
    $sqlGrupos = "SELECT g.id_grupo, g.clave_grupo, g.materia, 
                         COALESCE(m.nombre_materia, g.materia) AS nombre_materia,
                         COALESCE(m.creditos, 4) AS creditos,
                         COALESCE(m.horas_teoricas, 2) AS horas_teoricas,
                         COALESCE(m.horas_practicas, 2) AS horas_practicas,
                         COALESCE(m.total_unidades, 4) AS total_unidades,
                         g.periodo, g.capacidad, g.aula, g.turno, g.modalidad, 
                         COALESCE(g.semestre, 1) AS semestre, 
                         COALESCE(g.carrera, 'TIC') AS carrera,
                         COUNT(ig.no_de_control) AS total_inscritos
                  FROM grupos g 
                  LEFT JOIN materias m ON m.materia = g.materia 
                  LEFT JOIN inscripciones_grupo ig ON ig.id_grupo = g.id_grupo AND ig.estatus = 'INSCRITO'
                  WHERE g.rfc_docente = :rfc AND g.estado = 'ABIERTO'
                  GROUP BY g.id_grupo, g.clave_grupo, g.materia, m.nombre_materia, m.creditos, 
                           m.horas_teoricas, m.horas_practicas, m.total_unidades, g.periodo, 
                           g.capacidad, g.aula, g.turno, g.modalidad, g.semestre, g.carrera
                  ORDER BY g.clave_grupo ASC";
    $stmtG = $pdo->prepare($sqlGrupos);
    $stmtG->execute(['rfc' => $rfcDocente]);
    $grupos = $stmtG->fetchAll(PDO::FETCH_ASSOC);

    // 3. Obtener Horarios del Docente
    $sqlHorarios = "SELECT h.*, COALESCE(m.nombre_materia, h.descripcion) AS nombre_materia
                    FROM horarios_docente h
                    LEFT JOIN materias m ON m.materia = h.clave_materia
                    WHERE h.rfc_docente = :rfc
                    ORDER BY h.clave_grupo, FIELD(h.dia_semana, 'LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO','DOMINGO'), h.hora_inicio";
    $stmtH = $pdo->prepare($sqlHorarios);
    $stmtH->execute(['rfc' => $rfcDocente]);
    $horarios = $stmtH->fetchAll(PDO::FETCH_ASSOC);

    // Organizar horario docente por filas de materia/grupo para la tabla de Horario Docente
    $horarioDocenteMatriz = [];
    foreach ($horarios as $h) {
        $cve = $h['clave_grupo'];
        if (!isset($horarioDocenteMatriz[$cve])) {
            // Asignar clave interna según imagen
            $claveNum = '316';
            if ($cve === '085') $claveNum = '353';
            elseif ($cve === '087') $claveNum = '364';
            elseif ($cve === '033' || $cve === '33A') $claveNum = '534';
            elseif ($cve === '051') $claveNum = '616';

            $horarioDocenteMatriz[$cve] = [
                'clave' => $claveNum,
                'gpo' => ($cve === '033' ? '33A' : $cve),
                'materia' => strtoupper($h['nombre_materia'] ?? $h['descripcion']),
                'lunes' => '',
                'martes' => '',
                'miercoles' => '',
                'jueves' => '',
                'viernes' => '',
                'sabado' => '',
                'domingo' => ''
            ];
        }

        $dia = strtolower($h['dia_semana']);
        $ini = substr($h['hora_inicio'], 0, 5);
        $fin = substr($h['hora_fin'], 0, 5);
        $bloque = "$ini-$fin/{$h['aula']}";

        if (!empty($horarioDocenteMatriz[$cve][$dia])) {
            $horarioDocenteMatriz[$cve][$dia] .= ' / ' . $bloque;
        } else {
            $horarioDocenteMatriz[$cve][$dia] = $bloque;
        }
    }

    // 4. Macroplaneación (Lista de materias y grupos asignados)
    $macroplaneacion = [];
    $noIndex = 1;
    $numerosOficiales = ['081' => 6, '085' => 13, '087' => 20, '033' => 111, '051' => 133];
    foreach ($grupos as $g) {
        $cveGpo = $g['clave_grupo'];
        $numOficial = $numerosOficiales[$cveGpo] ?? $noIndex++;
        $macroplaneacion[] = [
            'no' => $numOficial,
            'clave' => $g['materia'],
            'asignatura' => strtoupper($g['nombre_materia']),
            'hrs_teoria' => $g['horas_teoricas'],
            'hrs_prac' => $g['horas_practicas'],
            'creditos' => $g['creditos'],
            'total_hrs' => ($g['horas_teoricas'] + $g['horas_practicas']),
            'grupo' => ($cveGpo === '033' ? '033A' : ($cveGpo . 'A')),
            'docente' => $docente['nombre_completo'] ?? 'MAN. LIZETT SIZZO NAVARRO'
        ];
    }
    // Ordenar macroplaneación por número oficial
    usort($macroplaneacion, function($a, $b) {
        return $a['no'] - $b['no'];
    });

    // 5. Reportes de Gestión del Curso
    $stmtRep = $pdo->prepare("SELECT * FROM reporte_gestion_curso WHERE rfc_docente = :rfc ORDER BY parcial, id_reporte ASC");
    $stmtRep->execute(['rfc' => $rfcDocente]);
    $reportesGestion = $stmtRep->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_db = "Error en la base de datos: " . $e->getMessage();
}
?>
