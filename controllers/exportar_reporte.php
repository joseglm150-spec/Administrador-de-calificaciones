<?php
session_start();
require_once '../config/conection.php';

if (!isset($_SESSION['rfc'])) {
    die("Acceso no autorizado.");
}

$rfc_docente = $_SESSION['rfc'];
$tipo = $_GET['tipo'] ?? '';
$formato = $_GET['formato'] ?? 'excel';

// Obtener información del docente
$stmtDoc = $pdo->prepare("SELECT * FROM personal WHERE rfc = :rfc LIMIT 1");
$stmtDoc->execute(['rfc' => $rfc_docente]);
$docente = $stmtDoc->fetch(PDO::FETCH_ASSOC);
$nombreDocente = trim(($docente['grado_academico'] ? $docente['grado_academico'] . ' ' : '') . ($docente['nombres'] ?? '') . ' ' . ($docente['apellidos'] ?? ''));
if (empty($nombreDocente)) $nombreDocente = $_SESSION['nombre'] ?? 'DOCENTE';

// -------------------------------------------------------------
// EXPORTACIÓN 1: HORARIO
// -------------------------------------------------------------
if ($tipo === 'horario') {
    $filename = "Horario_" . preg_replace('/[^A-Za-z0-9_-]/', '_', $nombreDocente) . ".xls";
    
    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo "\xEF\xBB\xBF"; // UTF-8 BOM

    // Obtener horarios
    $stmtH = $pdo->prepare(
        "SELECT h.*, COALESCE(m.nombre_materia, h.descripcion) as materia_nombre
         FROM horarios_docente h
         LEFT JOIN materias m ON m.materia = h.clave_materia
         WHERE h.rfc_docente = :rfc
         ORDER BY h.clave_grupo, FIELD(h.dia_semana, 'LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO','DOMINGO'), h.hora_inicio"
    );
    $stmtH->execute(['rfc' => $rfc_docente]);
    $horarios = $stmtH->fetchAll(PDO::FETCH_ASSOC);

    // Mapeo a matriz
    $matriz = [];
    $numerosOficiales = ['081' => '316', '085' => '353', '087' => '364', '033' => '534', '051' => '616'];
    foreach ($horarios as $h) {
        $cve = $h['clave_grupo'];
        if (!isset($matriz[$cve])) {
            $matriz[$cve] = [
                'clave' => $numerosOficiales[$cve] ?? $cve,
                'gpo' => ($cve === '033' ? '33A' : $cve),
                'materia' => strtoupper($h['materia_nombre']),
                'lunes' => '', 'martes' => '', 'miercoles' => '', 'jueves' => '', 'viernes' => '', 'sabado' => '', 'domingo' => ''
            ];
        }
        $dia = strtolower($h['dia_semana']);
        $ini = substr($h['hora_inicio'], 0, 5);
        $fin = substr($h['hora_fin'], 0, 5);
        $bloque = "$ini-$fin/{$h['aula']}";
        $matriz[$cve][$dia] = empty($matriz[$cve][$dia]) ? $bloque : $matriz[$cve][$dia] . ' / ' . $bloque;
    }

    ?>
    <table border="1">
        <tr>
            <th colspan="10" style="background:#19396A; color:#ffffff; font-size:16px;">SISTEMA INTEGRAL DE INFORMACIÓN - INSTITUTO TECNOLÓGICO SUPERIOR DE PURUÁNDIRO</th>
        </tr>
        <tr>
            <th colspan="10" style="background:#204a87; color:#ffffff; font-size:14px;">PORTAL DE DOCENTES - HORARIO DE TRABAJO</th>
        </tr>
        <tr><td colspan="10"></td></tr>
        <tr style="background:#d3d7cf; font-weight:bold;"><td colspan="10">ADSCRIPCIÓN</td></tr>
        <tr><td><strong>Nombre:</strong></td><td colspan="4"><?= htmlspecialchars($docente['apellidos'] . ' ' . $docente['nombres']) ?></td><td><strong>Tarjeta:</strong></td><td colspan="4"><?= htmlspecialchars($docente['tarjeta'] ?? '1106') ?></td></tr>
        <tr><td><strong>Escolaridad:</strong></td><td colspan="4"><?= htmlspecialchars($docente['escolaridad'] ?? 'LICENCIATURA') ?></td><td><strong>RFC:</strong></td><td colspan="4"><?= htmlspecialchars($docente['rfc'] ?? $rfc_docente) ?></td></tr>
        <tr><td><strong>CURP:</strong></td><td colspan="4"><?= htmlspecialchars($docente['curp'] ?? '') ?></td><td><strong>Puesto:</strong></td><td colspan="4"><?= htmlspecialchars($docente['puesto'] ?? 'DOCENTE') ?></td></tr>
        <tr><td><strong>Departamento:</strong></td><td colspan="4"><?= htmlspecialchars($docente['departamento'] ?? 'ING. TICS') ?></td><td><strong>Periodo:</strong></td><td colspan="4"><?= htmlspecialchars($docente['periodo_actual'] ?? 'AGOSTO-DICIEMBRE/2026') ?></td></tr>
        <tr><td colspan="10"></td></tr>
        <tr style="background:#19396A; color:#ffffff; font-weight:bold;"><td colspan="10" align="center">HORARIO DOCENTE</td></tr>
        <tr style="background:#e9f1fa; font-weight:bold;">
            <th>Clave</th><th>Gpo</th><th>Materia</th><th>Lunes</th><th>Martes</th><th>Miércoles</th><th>Jueves</th><th>Viernes</th><th>Sábado</th><th>Domingo</th>
        </tr>
        <?php foreach ($matriz as $fila): ?>
        <tr>
            <td><?= $fila['clave'] ?></td>
            <td><?= $fila['gpo'] ?></td>
            <td><?= htmlspecialchars($fila['materia']) ?></td>
            <td><?= $fila['lunes'] ?></td>
            <td><?= $fila['martes'] ?></td>
            <td><?= $fila['miercoles'] ?></td>
            <td><?= $fila['jueves'] ?></td>
            <td><?= $fila['viernes'] ?></td>
            <td><?= $fila['sabado'] ?></td>
            <td><?= $fila['domingo'] ?></td>
        </tr>
        <?php endforeach; ?>
        <tr><td colspan="10"></td></tr>
        <tr>
            <td colspan="2"><strong>Plaza:</strong> <?= htmlspecialchars($docente['plaza'] ?? 'DOCENTE') ?></td>
            <td colspan="2"><strong>H.F.G.:</strong> <?= $docente['h_f_g'] ?? 21 ?></td>
            <td colspan="2"><strong>H.Apoy.:</strong> <?= $docente['h_apoy'] ?? 0 ?></td>
            <td colspan="2"><strong>H.Adm.:</strong> <?= $docente['h_adm'] ?? 0 ?></td>
            <td colspan="2"><strong>Fecha:</strong> <?= date('d/m/Y') ?></td>
        </tr>
    </table>
    <?php
    exit;
}

// -------------------------------------------------------------
// EXPORTACIÓN 2: MACROPLANEACIÓN
// -------------------------------------------------------------
if ($tipo === 'macroplaneacion') {
    $filename = "Macroplaneacion_AGO_DIC_2026.xls";
    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo "\xEF\xBB\xBF";

    $stmtG = $pdo->prepare(
        "SELECT g.clave_grupo, g.materia, COALESCE(m.nombre_materia, g.materia) as nombre_materia,
                COALESCE(m.horas_teoricas, 2) as ht, COALESCE(m.horas_practicas, 2) as hp,
                COALESCE(m.creditos, 4) as cred
         FROM grupos g
         LEFT JOIN materias m ON m.materia = g.materia
         WHERE g.rfc_docente = :rfc AND g.estado = 'ABIERTO'
         ORDER BY g.clave_grupo ASC"
    );
    $stmtG->execute(['rfc' => $rfc_docente]);
    $grupos = $stmtG->fetchAll(PDO::FETCH_ASSOC);

    $numerosOficiales = ['081' => 6, '085' => 13, '087' => 20, '033' => 111, '051' => 133];
    ?>
    <table border="1">
        <tr>
            <th colspan="9" style="background:#19396A; color:#ffffff; font-size:16px;">INSTITUTO TECNOLÓGICO SUPERIOR DE PURUÁNDIRO - SUBDIRECCIÓN ACADÉMICA</th>
        </tr>
        <tr>
            <th colspan="9" style="background:#204a87; color:#ffffff; font-size:14px;">MACROPLANEACIÓN AGO-DIC 2026</th>
        </tr>
        <tr style="background:#e9f1fa; font-weight:bold;">
            <th>No.</th><th>CLAVE</th><th>ASIGNATURA</th><th>HRS TEORIA</th><th>HRS PRAC</th><th>CREDITOS</th><th>TOTAL HRS</th><th>GRUPO</th><th>DOCENTE ASIGNADO</th>
        </tr>
        <?php foreach ($grupos as $idx => $g): 
            $cve = $g['clave_grupo'];
            $num = $numerosOficiales[$cve] ?? ($idx + 1);
            $totalHrs = $g['ht'] + $g['hp'];
            $gpoNombre = ($cve === '033' ? '033A' : $cve . 'A');
        ?>
        <tr>
            <td><?= $num ?></td>
            <td><?= $g['materia'] ?></td>
            <td><?= htmlspecialchars(strtoupper($g['nombre_materia'])) ?></td>
            <td align="center"><?= $g['ht'] ?></td>
            <td align="center"><?= $g['hp'] ?></td>
            <td align="center"><?= $g['cred'] ?></td>
            <td align="center"><?= $totalHrs ?></td>
            <td align="center"><?= $gpoNombre ?></td>
            <td><?= htmlspecialchars($nombreDocente) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr><td colspan="9"></td></tr>
        <tr>
            <td colspan="3" align="center"><strong>Elaboró:</strong><br><br>MAE. Damaris Haydee Castillo Vega<br>Jefa de División del PE. ITIC</td>
            <td colspan="3" align="center"><strong>Vo. Bo.:</strong><br><br>Lic. Pedro Montelongo Pedroza<br>Subdirector Académico</td>
            <td colspan="3" align="center"><strong>Autorizó:</strong><br><br>Mtra. Martha Ávalos Arizmendi<br>Directora General del ITESP</td>
        </tr>
    </table>
    <?php
    exit;
}

// -------------------------------------------------------------
// EXPORTACIÓN 3: REPORTE DE CALIFICACIONES PARCIALES
// -------------------------------------------------------------
if ($tipo === 'calificaciones_parciales') {
    $clave_grupo = $_GET['grupo'] ?? '085';
    $filename = "Reporte_Calificaciones_Parciales_Gpo_" . $clave_grupo . ".xls";
    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo "\xEF\xBB\xBF";

    // Obtener grupo
    $stmtG = $pdo->prepare(
        "SELECT g.id_grupo, g.clave_grupo, g.materia, g.periodo, COALESCE(m.nombre_materia, g.materia) as nombre_materia
         FROM grupos g
         LEFT JOIN materias m ON m.materia = g.materia
         WHERE (g.clave_grupo = :cve OR g.id_grupo = :id_g) AND g.rfc_docente = :rfc LIMIT 1"
    );
    $stmtG->execute(['cve' => $clave_grupo, 'id_g' => is_numeric($clave_grupo) ? (int)$clave_grupo : 0, 'rfc' => $rfc_docente]);
    $grupo = $stmtG->fetch(PDO::FETCH_ASSOC);

    $id_grupo = (int)($grupo['id_grupo'] ?? 89);
    $nombreMateria = $grupo['nombre_materia'] ?? 'ADMINISTRACIÓN DE PROYECTOS';
    $periodo = $grupo['periodo'] ?? 'AGO-DIC/2026';

    // Obtener alumnos
    $stmtAlu = $pdo->prepare(
        "SELECT a.no_de_control, a.apellidos, a.nombres
         FROM inscripciones_grupo ig
         INNER JOIN alumnos a ON a.no_de_control = ig.no_de_control
         WHERE ig.id_grupo = :id_grupo AND ig.estatus = 'INSCRITO'
         ORDER BY a.apellidos ASC, a.nombres ASC"
    );
    $stmtAlu->execute(['id_grupo' => $id_grupo]);
    $alumnos = $stmtAlu->fetchAll(PDO::FETCH_ASSOC);

    // Obtener notas
    $stmtCalif = $pdo->prepare("SELECT no_de_control, unidad, calificacion FROM calificacion_evaluacion WHERE id_grupo = :id_grupo");
    $stmtCalif->execute(['id_grupo' => $id_grupo]);
    $califRaw = $stmtCalif->fetchAll(PDO::FETCH_ASSOC);
    $califMap = [];
    foreach ($califRaw as $c) $califMap[$c['no_de_control']][$c['unidad']] = $c['calificacion'];

    ?>
    <table border="1">
        <tr>
            <th colspan="13" style="background:#19396A; color:#ffffff; font-size:16px;">INSTITUTO TECNOLÓGICO SUPERIOR DE PURUÁNDIRO</th>
        </tr>
        <tr>
            <th colspan="13" style="background:#204a87; color:#ffffff; font-size:14px;">DIVISIÓN DE INGENIERÍA EN TECNOLOGÍAS DE LA INFORMACIÓN Y COMUNICACIONES</th>
        </tr>
        <tr>
            <th colspan="13" style="background:#f0f4f8; font-size:13px; font-weight:bold;">REPORTE DE CALIFICACIONES PARCIALES</th>
        </tr>
        <tr>
            <td colspan="4"><strong>MATERIA:</strong> <?= htmlspecialchars($nombreMateria) ?></td>
            <td colspan="5"><strong>GRUPO:</strong> <?= htmlspecialchars($clave_grupo) ?></td>
            <td colspan="4"><strong>PERIODO:</strong> <?= htmlspecialchars($periodo) ?></td>
        </tr>
        <tr>
            <td colspan="13"><strong>MAESTRO:</strong> <?= htmlspecialchars($nombreDocente) ?></td>
        </tr>
        <tr style="background:#e9f1fa; font-weight:bold;">
            <th rowspan="2">No</th>
            <th rowspan="2">No. CONTROL</th>
            <th rowspan="2">NOMBRE DEL ALUMNO</th>
            <th colspan="8" align="center">EVALUACIÓN DE UNIDADES</th>
            <th rowspan="2">COMP.</th>
            <th rowspan="2">Prom.</th>
        </tr>
        <tr style="background:#e9f1fa; font-weight:bold;">
            <th>I</th><th>II</th><th>III</th><th>IV</th><th>V</th><th>VI</th><th>VII</th><th>VIII</th>
        </tr>
        <?php 
        $reprobadosPorU = array_fill(1, 8, 0);
        $totalAlu = count($alumnos);
        foreach ($alumnos as $idx => $alu): 
            $nc = $alu['no_de_control'];
            $nom = htmlspecialchars($alu['apellidos'] . ' ' . $alu['nombres']);
            $suma = 0; $cnt = 0;
        ?>
        <tr>
            <td><?= $idx + 1 ?></td>
            <td><?= $nc ?></td>
            <td><?= $nom ?></td>
            <?php for ($u = 1; $u <= 8; $u++): 
                $nota = $califMap[$nc][$u] ?? '';
                if ($nota !== '' && $nota !== null) {
                    $suma += (int)$nota;
                    $cnt++;
                    if ((int)$nota < 70) $reprobadosPorU[$u]++;
                }
            ?>
            <td align="center"><?= $nota ?></td>
            <?php endfor; ?>
            <td></td>
            <td align="center"><strong><?= ($cnt > 0 ? round($suma / $cnt) : '') ?></strong></td>
        </tr>
        <?php endforeach; ?>
        <tr style="background:#fce4d6; font-weight:bold;">
            <td colspan="3" align="right"><strong>Índices de reprobación</strong></td>
            <?php for ($u = 1; $u <= 8; $u++): 
                $pct = ($totalAlu > 0) ? round(($reprobadosPorU[$u] / $totalAlu) * 100) : 0;
            ?>
            <td align="center"><?= $pct ?>%</td>
            <?php endfor; ?>
            <td>0%</td>
            <td>0</td>
        </tr>
    </table>
    <?php
    exit;
}

// -------------------------------------------------------------
// EXPORTACIÓN 4: REPORTE PARCIAL Y FINAL DE GESTIÓN DEL CURSO
// -------------------------------------------------------------
if ($tipo === 'gestion_curso') {
    $parcial = $_GET['parcial'] ?? '1';
    $filename = "Reporte_Gestion_del_Curso_Parcial_" . $parcial . ".xls";
    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo "\xEF\xBB\xBF";

    $stmtRep = $pdo->prepare("SELECT * FROM reporte_gestion_curso WHERE rfc_docente = :rfc AND parcial = :p ORDER BY id_reporte ASC");
    $stmtRep->execute(['rfc' => $rfc_docente, 'p' => $parcial]);
    $filas = $stmtRep->fetchAll(PDO::FETCH_ASSOC);

    ?>
    <table border="1">
        <tr>
            <th colspan="10" style="background:#19396A; color:#ffffff; font-size:16px;">REPORTE PARCIAL Y FINAL DE GESTIÓN DEL CURSO</th>
        </tr>
        <tr>
            <th colspan="10" style="background:#204a87; color:#ffffff; font-size:14px;">INSTITUTO TECNOLÓGICO SUPERIOR DE PURUÁNDIRO - SUBDIRECCIÓN ACADÉMICA</th>
        </tr>
        <tr>
            <th colspan="10" style="background:#f0f4f8;">DEPARTAMENTO DE : Tecnologías de la Información y Comunicaciones</th>
        </tr>
        <tr>
            <td colspan="4"><strong>REPORTE PARCIAL:</strong> <?= $parcial ?></td>
            <td colspan="6"><strong>DEL SEMESTRE:</strong> Agosto - diciembre 2026</td>
        </tr>
        <tr>
            <td colspan="4"><strong>Profesor (a):</strong> <?= htmlspecialchars($nombreDocente) ?></td>
            <td colspan="3"><strong>No. de Grupos Atendidos:</strong> 5</td>
            <td colspan="3"><strong>No. de Asignaturas diferentes:</strong> 4</td>
        </tr>
        <tr><td colspan="10"></td></tr>
        <tr style="background:#e9f1fa; font-weight:bold;">
            <th rowspan="2">ASIGNATURA</th>
            <th rowspan="2">UNIDAD/SEMESTRE</th>
            <th rowspan="2">CARRERA</th>
            <th rowspan="2" style="background:#d9e1f2;">A<br><small>Total Alumnos</small></th>
            <th colspan="2" style="background:#fff2cc;">B y C (Alcanzaron comp.)</th>
            <th colspan="2" style="background:#fce4d6;">D y E (No alcanzaron)</th>
            <th colspan="2" style="background:#f2dcbb;">F y G (Desertaron)</th>
        </tr>
        <tr style="background:#e9f1fa; font-weight:bold;">
            <th style="background:#d9e1f2;">B (EP/O)</th>
            <th style="background:#fff2cc;">C (%)</th>
            <th style="background:#fff2cc;">D (No ordinaria)</th>
            <th style="background:#fff2cc;">E (%)</th>
            <th style="background:#d9e1f2;">F (Desertaron)</th>
            <th style="background:#fff2cc;">G (%)</th>
        </tr>
        <?php 
        $totA = 0; $totB = 0; $totD = 0; $totF = 0;
        foreach ($filas as $f): 
            $a = (int)$f['total_alumnos'];
            $b = (int)$f['aprobados_ordinario'];
            $d = (int)$f['reprobados_ordinario'];
            $des = (int)$f['desertores'];

            $c = ($a > 0) ? round(($b / $a) * 100) : 0;
            $e = ($a > 0) ? round(($d / $a) * 100) : 0;
            $g = ($a > 0) ? round(($des / $a) * 100) : 0;

            $totA += $a; $totB += $b; $totD += $d; $totF += $des;
        ?>
        <tr>
            <td><?= htmlspecialchars($f['asignatura']) ?></td>
            <td align="center"><?= htmlspecialchars($f['unidad_semestre']) ?></td>
            <td align="center"><?= htmlspecialchars($f['carrera']) ?></td>
            <td align="center" style="background:#d9e1f2; font-weight:bold;"><?= $a ?></td>
            <td align="center" style="background:#d9e1f2;"><?= $b ?></td>
            <td align="center" style="background:#fff2cc; font-weight:bold;"><?= $c ?>%</td>
            <td align="center" style="background:#fff2cc;"><?= $d ?></td>
            <td align="center" style="background:#fff2cc; font-weight:bold;"><?= $e ?>%</td>
            <td align="center" style="background:#d9e1f2;"><?= $des ?></td>
            <td align="center" style="background:#fff2cc; font-weight:bold;"><?= $g ?>%</td>
        </tr>
        <?php endforeach; 
        $totC = ($totA > 0) ? round(($totB / $totA) * 100) : 0;
        $totE = ($totA > 0) ? round(($totD / $totA) * 100) : 0;
        $totG = ($totA > 0) ? round(($totF / $totA) * 100) : 0;
        ?>
        <tr style="background:#fce4d6; font-weight:bold;">
            <td colspan="3">TOTAL</td>
            <td align="center"><?= $totA ?></td>
            <td align="center"><?= $totB ?></td>
            <td align="center"><?= $totC ?>%</td>
            <td align="center"><?= $totD ?></td>
            <td align="center"><?= $totE ?>%</td>
            <td align="center"><?= $totF ?></td>
            <td align="center"><?= $totG ?>%</td>
        </tr>
        <tr><td colspan="10"></td></tr>
        <tr>
            <td colspan="10" style="font-size:11px;">
                <strong>NOTAS:</strong><br>
                1. La sumatoria de todas las columnas C+E+G deberá dar el 100%<br>
                2. Los alumnos que se incluirán en la columna D son todos los alumnos no acreditados incluyendo los desertores.<br>
                3. Este registro deberá de acompañarse con sus respectivas listas de calificaciones que avalen los datos presentados.<br>
                4. Lo marcado en color amarillo contiene formula, favor de no modificarlo.<br>
                5. Lo marcado en color azul es lo que debera ser capturado, dependiendo los resultados de las evaluaciones correspondientes.
            </td>
        </tr>
    </table>
    <?php
    exit;
}

echo "Tipo de exportación no válido.";
?>
