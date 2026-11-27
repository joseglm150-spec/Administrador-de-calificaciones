<?php
// 1. Cargamos la lógica y los datos desde los controladores
require_once '../../controllers/grupo_controller.php';
require_once '../../controllers/calificacion_controller.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIRCA - Sistema Integral de Calificaciones y Reportes Académicos</title>
    <link rel="stylesheet" href="../../assets/css/menu.css?v=<?= filemtime(__DIR__ . '/../../assets/css/menu.css') ?>">
    <link rel="stylesheet" href="../../assets/css/menu_calificaciones.css?v=<?= filemtime(__DIR__ . '/../../assets/css/menu_calificaciones.css') ?>">
    <link rel="stylesheet" href="../../assets/css/reportes_impresion.css?v=<?= filemtime(__DIR__ . '/../../assets/css/reportes_impresion.css') ?>">
</head>
<body>

<div class="layout">
    <aside>
        <div class="marca">
            TECNM - ITESP
            <small>Portal Docente - SIRCA</small>
        </div>
        <p class="etiqueta">Menú principal</p>
        <nav aria-label="Menú principal">
            <button class="menu-opcion" type="button" data-vista="inicio" data-titulo="Información del docente" aria-controls="contenido-principal">
                <span>Inicio</span>
            </button>
            <button class="menu-opcion activo" type="button" data-vista="grupos" data-titulo="Mis Grupos" aria-current="page" aria-controls="contenido-principal">
                <span>Grupos</span>
            </button>
            <button class="menu-opcion" type="button" data-vista="horario" data-titulo="Horario Institucional" aria-controls="contenido-principal">
                <span>Horario</span>
            </button>
            <button class="menu-opcion" type="button" data-vista="documentos" data-titulo="Documentos de Certificación y Reportes" aria-controls="contenido-principal">
                <span>Documentos Oficiales</span>
            </button>
            <button class="menu-opcion" type="button" data-vista="utileria" data-titulo="Utilería y Herramientas" aria-controls="contenido-principal">
                <span>Utilería</span>
            </button>
        </nav>
        
        <div style="margin-top: auto; padding: 15px 10px 0 10px; border-top: 1px solid rgba(255,255,255,0.15);">
            <a href="../../index.php" style="display: flex; align-items: center; gap: 8px; color: #cbd5e0; text-decoration: none; font-size: 13px;">
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </aside>

    <main>
        <header>
            <img class="logo-sirca-header" src="../../assets/img/SIRCA.jpeg" alt="SIRCA">
            <h1><span id="titulo-seccion">Mis Grupos</span></h1>
            <div class="usuario">
                Docente: <strong><?= htmlspecialchars($docente['nombre_completo'] ?? $nombreUsuario, ENT_QUOTES, 'UTF-8') ?></strong>
                <span style="font-size: 11px; color: #718096; display: block; text-align: right;">RFC: <?= htmlspecialchars($rfcDocente, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        </header>

        <div class="contenido" id="contenido-principal" role="region" aria-live="polite" aria-labelledby="titulo-seccion">
            <!-- Por defecto cargamos la vista de Grupos -->
            <?php include 'template_grupos.php'; ?>
        </div>
    </main>
</div>

<!-- ========================================== -->
<!-- MODAL FLOTANTE PARA REPORTES OFICIALES     -->
<!-- ========================================== -->
<div id="modal-reporte-global" class="modal-reporte-overlay" style="display: none;">
    <div class="modal-reporte-contenido">
        <div class="modal-reporte-cabecera">
            <h3 id="modal-reporte-titulo">Reporte Oficial</h3>
            <button class="btn-cerrar-modal" id="btn-cerrar-modal-rep" type="button" title="Cerrar ventana">&times;</button>
        </div>
        <div class="modal-reporte-cuerpo" id="modal-reporte-cuerpo">
            <!-- Contenido dinámico del reporte cargado por JS -->
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- TEMPLATES DE VISTAS INYECTADOS             -->
<!-- ========================================== -->

<!-- TEMPLATE 1: GRUPOS -->
<template id="vista-grupos">
    <?php include 'template_grupos.php'; ?>
</template>

<!-- TEMPLATE 2: CALIFICACIONES POR GRUPO -->
<template id="vista-calificaciones-grupo">
    <?php include 'template_calificaciones.php'; ?>
</template>

<!-- TEMPLATE 3: INICIO (PERFIL DOCENTE) -->
<template id="vista-inicio">
    <section class="perfil-admin" aria-label="Información principal del docente">
        <div class="perfil-admin-encabezado">
            <div>
                <span class="perfil-admin-subtitulo">Información del docente</span>
                <h2><?= htmlspecialchars($docente['nombre_completo'] ?? $nombreUsuario, ENT_QUOTES, 'UTF-8') ?></h2>
            </div>
            <span class="perfil-admin-periodo"><?= htmlspecialchars($docente['periodo_actual'] ?? 'AGOSTO-DICIEMBRE/2026', ENT_QUOTES, 'UTF-8') ?></span>
        </div>

        <div class="tabla-perfil-contenedor">
            <table class="tabla-perfil-docente">
                <colgroup>
                    <col style="width: 18%;">
                    <col style="width: 32%;">
                    <col style="width: 20%;">
                    <col style="width: 30%;">
                </colgroup>
                <tbody>
                    <tr>
                        <th scope="row">RFC</th>
                        <td><?= htmlspecialchars($rfcDocente, ENT_QUOTES, 'UTF-8') ?></td>
                        <th scope="row">CURP</th>
                        <td><?= htmlspecialchars($docente['curp'] ?? 'SINL900223MMNZVZ00', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Tarjeta</th>
                        <td><?= htmlspecialchars($docente['tarjeta'] ?? '1106', ENT_QUOTES, 'UTF-8') ?></td>
                        <th scope="row">Puesto</th>
                        <td><?= htmlspecialchars($docente['puesto'] ?? 'DOCENTE', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Horas Frente a Grupo</th>
                        <td><?= htmlspecialchars((string)($docente['h_f_g'] ?? 21), ENT_QUOTES, 'UTF-8') ?> hrs</td>
                        <th scope="row">Periodo Escolar</th>
                        <td><?= htmlspecialchars($docente['periodo_actual'] ?? 'AGOSTO-DICIEMBRE/2026', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Escolaridad</th>
                        <td colspan="3"><?= htmlspecialchars($docente['escolaridad'] ?? 'Licenciatura en Informática', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Departamento</th>
                        <td colspan="3"><?= htmlspecialchars($docente['departamento'] ?? 'Tecnologías de la Información', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <div class="accesos-inicio">
        <div class="acceso-inicio-tarjeta">
            <div class="acceso-inicio-cuerpo">
                <h3>5 Grupos</h3>
                <p>Grupos atendidos en el semestre</p>
            </div>
            <button class="btn-accion-reporte btn-primario" type="button" onclick="document.querySelector('.menu-opcion[data-vista=\'grupos\']').click();">Ir a Grupos</button>
        </div>

        <div class="acceso-inicio-tarjeta">
            <div class="acceso-inicio-cuerpo">
                <h3>Horario</h3>
                <p>21 Horas frente a grupo</p>
            </div>
            <button class="btn-accion-reporte btn-secundario" type="button" onclick="document.querySelector('.menu-opcion[data-vista=\'horario\']').click();">Ver Horario</button>
        </div>

        <div class="acceso-inicio-tarjeta">
            <div class="acceso-inicio-cuerpo">
                <h3>Macroplaneación</h3>
                <p>Planeación oficial semestral</p>
            </div>
            <button class="btn-accion-reporte btn-secundario" type="button" onclick="document.querySelector('.menu-opcion[data-vista=\'documentos\']').click();">Ver Macroplaneación</button>
        </div>

        <div class="acceso-inicio-tarjeta">
            <div class="acceso-inicio-cuerpo">
                <h3>Gestión del Curso</h3>
                <p>Reporte Parcial y Final</p>
            </div>
            <button class="btn-accion-reporte btn-secundario" type="button" onclick="document.querySelector('.menu-opcion[data-vista=\'documentos\']').click(); setTimeout(() => document.getElementById('tab-doc-gestion')?.click(), 100);">Ver Gestión</button>
        </div>
    </div>
</template>

<!-- TEMPLATE 4: HORARIO (CORRESPONDE A horario.jpeg) -->
<template id="vista-horario">
    <!-- Barra de herramientas para descargar e imprimir -->
    <div class="barra-herramientas-reporte">
        <div style="display: flex; align-items: center; gap: 8px;">
            <strong>Horario Institucional - Portal de Docentes</strong>
        </div>
        <div class="grupo-botones">
            <a href="../../controllers/exportar_reporte.php?tipo=horario&formato=excel" class="btn-accion-reporte btn-excel" title="Descargar como archivo de Excel">
                Descargar Excel
            </a>
            <button class="btn-accion-reporte btn-pdf btn-imprimir-reporte" type="button" onclick="window.print();" title="Imprimir o Guardar como PDF">
                Imprimir / Guardar PDF
            </button>
        </div>
    </div>

    <!-- Hoja de Horario estilo SII ITESP -->
    <div class="documento-hoja" id="hoja-horario-impresion">
        <!-- Encabezado Institucional -->
        <div class="encabezado-institucional">
            <img class="logo-sep" src="../../assets/img/itesp.jpg" alt="Logo ITESP">
            <div class="titulos-centro">
                <h2>SECRETARÍA DE EDUCACIÓN PÚBLICA</h2>
                <h3>SISTEMA INTEGRAL DE INFORMACIÓN</h3>
                <h4>INSTITUTO TECNOLÓGICO SUPERIOR DE PURUÁNDIRO</h4>
                <div style="font-size: 12px; font-weight: bold; color: #1b365d; margin-top: 3px;">PORTAL DE DOCENTES</div>
            </div>
            <img class="logo-tecnm" src="../../assets/img/tecnm.png" alt="TecNM">
        </div>

        <!-- Sección Adscripción -->
        <div class="barra-seccion-titulo">ADSCRIPCIÓN</div>
        <table class="tabla-adscripcion">
            <tr>
                <td class="etiqueta-adsc">Nombre:</td>
                <td><?= htmlspecialchars($docente['nombre_formal'] ?? 'SIZZO NAVARRO LIZETT') ?></td>
                <td class="etiqueta-adsc">Tarjeta:</td>
                <td><?= htmlspecialchars($docente['tarjeta'] ?? '1106') ?></td>
            </tr>
            <tr>
                <td class="etiqueta-adsc">Escolaridad:</td>
                <td><?= htmlspecialchars($docente['escolaridad'] ?? 'Licenciatura en: LICENCIADA EN INFORMATICA') ?></td>
                <td class="etiqueta-adsc">RFC:</td>
                <td><?= htmlspecialchars($docente['rfc'] ?? 'SINL900223TC2') ?></td>
            </tr>
            <tr>
                <td class="etiqueta-adsc">Curp:</td>
                <td><?= htmlspecialchars($docente['curp'] ?? 'SINL900223MMNZVZ00') ?></td>
                <td class="etiqueta-adsc">Ingreso al plantel:</td>
                <td><?= htmlspecialchars($docente['ingreso_plantel'] ?? '201602') ?></td>
            </tr>
            <tr>
                <td class="etiqueta-adsc">Puesto:</td>
                <td><?= htmlspecialchars($docente['puesto'] ?? 'DOCENTE') ?></td>
                <td class="etiqueta-adsc">Ingreso a la SEP:</td>
                <td><?= htmlspecialchars($docente['ingreso_sep'] ?? '201602') ?></td>
            </tr>
            <tr>
                <td class="etiqueta-adsc">Departamento:</td>
                <td colspan="3"><?= htmlspecialchars($docente['departamento'] ?? 'DIVISION DE INGENIERIA EN TECNOLOGIAS DE LA INFORMACION Y COMUNICACIONES') ?></td>
            </tr>
            <tr>
                <td class="etiqueta-adsc">Periodo:</td>
                <td colspan="3"><?= htmlspecialchars($docente['periodo_actual'] ?? 'AGOSTO-DICIEMBRE/2026') ?></td>
            </tr>
        </table>

        <!-- Sección Horario Administrativo -->
        <div class="barra-seccion-titulo">HORARIO ADMINISTRATIVO</div>
        <table class="tabla-horario-docente">
            <thead>
                <tr>
                    <th>Lunes</th><th>Martes</th><th>Miércoles</th><th>Jueves</th><th>Viernes</th><th>Sábado</th><th>Domingo</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
                </tr>
            </tbody>
        </table>

        <!-- Sección Horario Docente -->
        <div class="barra-seccion-titulo">HORARIO DOCENTE</div>
        <table class="tabla-horario-docente">
            <thead>
                <tr>
                    <th style="width: 45px;">Clave</th>
                    <th style="width: 40px;">Gpo</th>
                    <th style="width: 250px;">Materia</th>
                    <th>Lunes</th>
                    <th>Martes</th>
                    <th>Miércoles</th>
                    <th>Jueves</th>
                    <th>Viernes</th>
                    <th>Sábado</th>
                    <th>Domingo</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($horarioDocenteMatriz)): ?>
                    <?php foreach ($horarioDocenteMatriz as $hFila): ?>
                    <tr>
                        <td><?= $hFila['clave'] ?></td>
                        <td><strong><?= $hFila['gpo'] ?></strong></td>
                        <td class="col-materia"><?= htmlspecialchars($hFila['materia']) ?></td>
                        <td><?= $hFila['lunes'] ?></td>
                        <td><?= $hFila['martes'] ?></td>
                        <td><?= $hFila['miercoles'] ?></td>
                        <td><?= $hFila['jueves'] ?></td>
                        <td><?= $hFila['viernes'] ?></td>
                        <td><?= $hFila['sabado'] ?></td>
                        <td><?= $hFila['domingo'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="10">No se encontraron registros de horarios docentes.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Sección Horario de Apoyo -->
        <div class="barra-seccion-titulo">HORARIO DE APOYO</div>
        <table class="tabla-horario-docente">
            <thead>
                <tr>
                    <th style="width: 40px;">Act.</th>
                    <th style="width: 40px;">Cons.</th>
                    <th style="width: 250px;">Descripción</th>
                    <th>Lunes</th><th>Martes</th><th>Miércoles</th><th>Jueves</th><th>Viernes</th><th>Sábado</th><th>Domingo</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>
            </tbody>
        </table>

        <!-- Pie de totales -->
        <div class="pie-totales-horario">
            <div><strong>Plaza:</strong> <?= htmlspecialchars($docente['plaza'] ?? 'DOCENTE') ?></div>
            <div><strong>Horas:</strong> 21</div>
            <div><strong>H.F.G.:</strong> <?= $docente['h_f_g'] ?? 21 ?></div>
            <div><strong>H.Apoy.:</strong> <?= $docente['h_apoy'] ?? 0 ?></div>
            <div><strong>H.Adm.:</strong> <?= $docente['h_adm'] ?? 0 ?></div>
            <div><strong>Fecha de Elaboración:</strong> 02/10/2026</div>
            <div><strong>Interesado:</strong> <?= htmlspecialchars($docente['nombre_formal'] ?? 'SIZZO NAVARRO LIZETT') ?></div>
        </div>
    </div>
</template>

<!-- TEMPLATE 5: DOCUMENTOS OFICIALES (MACROPLANEACIÓN, REPORTE DE GESTIÓN Y REPORTE PARCIAL) -->
<template id="vista-documentos">
    <!-- Pestañas internas para alternar entre los documentos -->
    <div style="display: flex; gap: 8px; border-bottom: 2px solid #e2e8f0; margin-bottom: 20px; padding-bottom: 8px; flex-wrap: wrap;">
        <button type="button" class="btn-accion-reporte btn-primario tab-documento-activo" id="tab-doc-macro" onclick="cambiarPestanaDocumento('macro')">
            Macroplaneación (AGO-DIC 2026)
        </button>
        <button type="button" class="btn-accion-reporte btn-secundario" id="tab-doc-gestion" onclick="cambiarPestanaDocumento('gestion')">
            Reporte Parcial y Final de Gestión del Curso
        </button>
        <button type="button" class="btn-accion-reporte btn-secundario" id="tab-doc-parcial" onclick="cambiarPestanaDocumento('parcial')">
            Reporte de Calificaciones Parciales
        </button>
    </div>

    <!-- SUBVISTA A: MACROPLANEACIÓN (CORRESPONDE A Macroplaneación.jpeg) -->
    <div id="subvista-macro" class="subvista-documento">
        <div class="barra-herramientas-reporte">
            <div>
                <strong>Macroplaneación Oficial - Semestre Agosto - Diciembre 2026</strong>
                <p style="margin: 2px 0 0 0; color: #718096; font-size: 12px;">Subdirección Académica | ITESP</p>
            </div>
            <div class="grupo-botones">
                <a href="../../controllers/exportar_reporte.php?tipo=macroplaneacion&formato=excel" class="btn-accion-reporte btn-excel" title="Descargar en Excel">
                    Descargar Excel
                </a>
                <button class="btn-accion-reporte btn-pdf btn-imprimir-reporte" type="button" onclick="window.print();" title="Imprimir documento">
                    Imprimir / Guardar PDF
                </button>
            </div>
        </div>

        <div class="documento-hoja">
            <div class="encabezado-institucional">
                <img class="logo-sep" src="../../assets/img/itesp.jpg" alt="ITESP">
                <div class="titulos-centro">
                    <h2>SECRETARÍA DE EDUCACIÓN PÚBLICA</h2>
                    <h3>GOBIERNO DE MICHOACÁN</h3>
                    <h4>INSTITUTO TECNOLÓGICO SUPERIOR DE PURUÁNDIRO</h4>
                    <div style="font-size: 13px; font-weight: bold; color: #1b365d; margin-top: 4px;">SUBDIRECCIÓN ACADÉMICA</div>
                </div>
                <img class="logo-tecnm" src="../../assets/img/tecnm.png" alt="TecNM">
            </div>

            <h3 style="text-align: center; color: #1b365d; letter-spacing: 1px; margin: 15px 0 20px 0;">
                MACROPLANEACIÓN AGO-DIC 2026
            </h3>

            <table class="tabla-macroplaneacion">
                <thead>
                    <tr>
                        <th style="width: 35px;">No.</th>
                        <th style="width: 85px;">CLAVE</th>
                        <th>ASIGNATURA</th>
                        <th style="width: 65px;">HRS TEORIA</th>
                        <th style="width: 65px;">HRS PRAC</th>
                        <th style="width: 65px;">CREDITOS</th>
                        <th style="width: 65px;">TOTAL HRS</th>
                        <th style="width: 65px;">GRUPO</th>
                        <th style="width: 220px;">DOCENTE ASIGNADO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($macroplaneacion)): ?>
                        <?php foreach ($macroplaneacion as $m): ?>
                        <tr>
                            <td align="center"><strong><?= $m['no'] ?></strong></td>
                            <td align="center"><?= htmlspecialchars($m['clave']) ?></td>
                            <td><strong><?= htmlspecialchars($m['asignatura']) ?></strong></td>
                            <td align="center"><?= $m['hrs_teoria'] ?></td>
                            <td align="center"><?= $m['hrs_prac'] ?></td>
                            <td align="center"><?= $m['creditos'] ?></td>
                            <td align="center"><?= $m['total_hrs'] ?></td>
                            <td align="center"><strong><?= htmlspecialchars($m['grupo']) ?></strong></td>
                            <td><?= htmlspecialchars($m['docente']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" align="center">No hay registros de macroplaneación.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Sección de Firmas Oficiales -->
            <div class="seccion-firmas">
                <div class="caja-firma">
                    <div class="linea-firma"></div>
                    <div class="nombre-firma">MAE. Damaris Haydee Castillo Vega</div>
                    <div class="cargo-firma">Elaboró<br>Jefa de División del PE. ITIC</div>
                </div>
                <div class="caja-firma">
                    <div class="linea-firma"></div>
                    <div class="nombre-firma">Lic. Pedro Montelongo Pedroza</div>
                    <div class="cargo-firma">Vo. Bo.<br>Subdirector Académico</div>
                </div>
                <div class="caja-firma">
                    <div class="linea-firma"></div>
                    <div class="nombre-firma">Mtra. Martha Ávalos Arizmendi</div>
                    <div class="cargo-firma">Autorizó<br>Directora General del ITESP</div>
                </div>
            </div>
        </div>
    </div>

    <!-- SUBVISTA B: REPORTE DE GESTIÓN DEL CURSO (CORRESPONDE A reporte parcial y final de gestion del curso.jpeg) -->
    <div id="subvista-gestion" class="subvista-documento" style="display: none;">
        <div class="barra-herramientas-reporte">
            <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                <div>
                    <strong>Reporte Parcial y Final de Gestión del Curso</strong>
                    <div style="font-size: 12px; color: #718096;">Semestre: Agosto - diciembre 2026</div>
                </div>
                <!-- Pestañas de Parciales estilo Excel -->
                <div class="pestanas-periodo-gestion">
                    <button type="button" class="btn-pestana-periodo activo" data-parcial="1">1</button>
                    <button type="button" class="btn-pestana-periodo" data-parcial="2">2</button>
                    <button type="button" class="btn-pestana-periodo" data-parcial="3">3</button>
                    <button type="button" class="btn-pestana-periodo" data-parcial="4">4</button>
                    <button type="button" class="btn-pestana-periodo" data-parcial="F">F</button>
                </div>
            </div>
            <div class="grupo-botones">
                <button class="btn-accion-reporte btn-primario" id="btn-guardar-gestion-db" type="button" title="Guardar cambios directamente en la base de datos">
                    Guardar en BD
                </button>
                <a href="../../controllers/exportar_reporte.php?tipo=gestion_curso&parcial=1" id="enlace-descarga-gestion-excel" class="btn-accion-reporte btn-excel" title="Descargar como hoja de cálculo Excel">
                    Descargar Excel
                </a>
                <button class="btn-accion-reporte btn-pdf btn-imprimir-reporte" type="button" onclick="window.print();" title="Imprimir formato">
                    Imprimir / Guardar PDF
                </button>
            </div>
        </div>

        <div class="documento-hoja" id="hoja-gestion-impresion">
            <!-- Encabezado del reporte de gestión -->
            <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #1b365d; padding-bottom: 10px; margin-bottom: 12px;">
                <img src="../../assets/img/itesp.jpg" alt="ITESP" style="height: 50px;">
                <div style="text-align: center; flex: 1;">
                    <h3 style="margin: 0; font-size: 15px; color: #1b365d;">REPORTE PARCIAL Y FINAL DE GESTIÓN DEL CURSO</h3>
                    <h4 style="margin: 2px 0 0 0; font-size: 13px; color: #2d3748;">INSTITUTO TECNOLÓGICO SUPERIOR DE PURUÁNDIRO</h4>
                    <div style="font-size: 12px; color: #4a5568;">SUBDIRECCIÓN ACADÉMICA</div>
                </div>
                <img src="../../assets/img/tecnm.png" alt="TecNM" style="height: 50px;">
            </div>

            <!-- Fila informativa del reporte -->
            <div style="font-size: 12px; margin-bottom: 12px; line-height: 1.6;">
                <div><strong>DEPARTAMENTO DE :</strong> Tecnologías de la Información y Comunicaciones</div>
                <div style="display: flex; justify-content: space-between; flex-wrap: wrap; margin-top: 4px;">
                    <div><strong>REPORTE PARCIAL:</strong> <span id="label-parcial-activo" style="display: inline-block; background: #1b365d; color: #fff; padding: 1px 8px; border-radius: 3px; font-weight: bold;">1</span> DEL SEMESTRE: Agosto - diciembre 2026</div>
                    <div><strong>Profesor (a):</strong> <?= htmlspecialchars($docente['nombre_completo'] ?? 'MAN. Lizett Sizzo Navarro') ?></div>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 4px; color: #4a5568;">
                    <div><strong>No. de Grupos Atendidos:</strong> <span id="label-num-grupos">5</span></div>
                    <div><strong>No. de Asignaturas diferentes:</strong> <span id="label-num-asig">4</span></div>
                </div>
            </div>

            <!-- Tabla de Datos con Fórmulas e Interacción en Tiempo Real -->
            <table class="tabla-gestion-excel" id="tabla-gestion-curso">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 220px;">ASIGNATURA</th>
                        <th rowspan="2" style="width: 70px;">UNIDAD/ SEMESTRE</th>
                        <th rowspan="2" style="width: 70px;">CARRERA</th>
                        <th rowspan="2" class="celda-azul" style="width: 50px;">A<br><small style="font-size: 9px;">TOTAL</small></th>
                        <th colspan="2" class="celda-amarillo">B</th>
                        <th rowspan="2" class="celda-amarillo" style="width: 45px;">D</th>
                        <th rowspan="2" class="celda-naranja" style="width: 50px;">E<br><small style="font-size: 9px;">% REPR.</small></th>
                        <th rowspan="2" class="celda-azul" style="width: 45px;">F<br><small style="font-size: 9px;">DESERT.</small></th>
                        <th rowspan="2" class="celda-naranja" style="width: 50px;">G<br><small style="font-size: 9px;">% DES.</small></th>
                    </tr>
                    <tr>
                        <th class="celda-azul" style="width: 45px;">EP/O</th>
                        <th class="celda-amarillo" style="width: 50px;">C (%)</th>
                    </tr>
                </thead>
                <tbody id="cuerpo-tabla-gestion">
                    <!-- Filas inyectadas y calculadas en tiempo real por menu.js -->
                </tbody>
                <tfoot>
                    <tr style="background: #fce4d6; font-weight: bold; text-align: center;" id="fila-total-gestion">
                        <td colspan="3" style="text-align: left; padding-left: 10px;">TOTAL</td>
                        <td id="tot-a" class="celda-azul">85</td>
                        <td id="tot-b" class="celda-azul">84</td>
                        <td id="tot-c" class="celda-amarillo">99%</td>
                        <td id="tot-d" class="celda-amarillo">1</td>
                        <td id="tot-e" class="celda-naranja">1%</td>
                        <td id="tot-f" class="celda-azul">0</td>
                        <td id="tot-g" class="celda-naranja">0%</td>
                    </tr>
                </tfoot>
            </table>

            <!-- Notas Oficiales del Documento -->
            <div class="seccion-notas-gestion">
                <strong>A =</strong> TOTAL DE ALUMNOS POR MATERIA<br>
                <strong>B =</strong> NO. DE ALUMNOS QUE ALCANZARON LAS COMPETENCIAS (EP= EVALUACIÓN DE PRIMERA OPORTUNIDAD) (O=ORDINARIO)<br>
                <strong>C =</strong> % DE ALUMNOS QUE ALCANZARON LAS COMPETENCIAS DE MANERA ORDINARIA (B / A * 100%)<br>
                <strong>D =</strong> NO. DE ALUMNOS QUE NO ALCANZARON LAS COMPETENCIAS DE MANERA ORDINARIA (A - B - F)<br>
                <strong>E =</strong> % DE ALUMNOS QUE NO ALCANZARON LAS COMPETENCIAS DE MANERA ORDINARIA (D / A * 100%)<br>
                <strong>F =</strong> NO. DE ALUMNOS QUE DESERTARON DURANTE EL SEMESTRE EN LA MATERIA<br>
                <strong>G =</strong> % DE ALUMNOS QUE DESERTARON EN LA MATERIA (F / A * 100%)<br><br>
                <strong>NOTAS:</strong><br>
                1. La sumatoria de todas las columnas C+E+G deberá dar el 100%<br>
                2. Los alumnos que se incluirán en la columna D son todos los alumnos no acreditados incluyendo los desertores.<br>
                3. Este registro deberá de acompañarse con sus respectivas listas de calificaciones que avalen los datos presentados.<br>
                4. Lo marcado en color amarillo contiene formula, favor de no modificarlo.<br>
                5. Lo marcado en color azul es lo que debera ser capturado, dependiendo los resultados de las evaluaciones correspondientes.
            </div>
        </div>
    </div>

    <!-- SUBVISTA C: REPORTE DE CALIFICACIONES PARCIALES (CORRESPONDE A reporte de calificaciones parciales.jpeg) -->
    <div id="subvista-parcial" class="subvista-documento" style="display: none;">
        <div class="barra-herramientas-reporte">
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <strong>Reporte de Calificaciones Parciales</strong>
                <label for="select-grupo-reporte-parcial" style="font-size: 13px; font-weight: bold; color: #4a5568;">Seleccionar Grupo:</label>
                <select id="select-grupo-reporte-parcial" style="padding: 6px 10px; border-radius: 4px; border: 1px solid #cbd5e0; font-size: 13px; font-weight: bold; color: #1b365d;">
                    <?php foreach ($grupos as $g): ?>
                        <option value="<?= htmlspecialchars($g['clave_grupo']) ?>" <?= ($g['clave_grupo'] === '085' ? 'selected' : '') ?>>
                            Grupo <?= htmlspecialchars($g['clave_grupo']) ?> - <?= htmlspecialchars($g['nombre_materia']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grupo-botones">
                <a href="../../controllers/exportar_reporte.php?tipo=calificaciones_parciales&grupo=085" id="enlace-descarga-parcial-excel" class="btn-accion-reporte btn-excel" title="Descargar lista en Excel">
                    Descargar Excel
                </a>
                <button class="btn-accion-reporte btn-pdf btn-imprimir-reporte" type="button" onclick="window.print();" title="Imprimir lista">
                    Imprimir / Guardar PDF
                </button>
            </div>
        </div>

        <div class="documento-hoja" id="hoja-calificaciones-parciales-impresion">
            <!-- Encabezado Institucional -->
            <div class="encabezado-institucional">
                <img class="logo-sep" src="../../assets/img/itesp.jpg" alt="ITESP">
                <div class="titulos-centro">
                    <h2>INSTITUTO TECNOLÓGICO SUPERIOR DE PURUÁNDIRO</h2>
                    <h4 id="rep-parcial-depto">DIVISIÓN DE INGENIERÍA EN TECNOLOGÍAS DE LA INFORMACIÓN Y COMUNICACIONES</h4>
                    <div style="font-size: 14px; font-weight: bold; color: #1b365d; margin-top: 4px;">REPORTE DE CALIFICACIONES PARCIALES</div>
                </div>
                <img class="logo-tecnm" src="../../assets/img/tecnm.png" alt="TecNM">
            </div>

            <!-- Metadatos de la Materia -->
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 8px; font-size: 12px; margin-bottom: 12px; border-bottom: 1px solid #cbd5e0; padding-bottom: 8px;">
                <div>
                    <div><strong>MATERIA:</strong> <span id="rep-parcial-materia">ADMINISTRACIÓN DE PROYECTOS</span></div>
                    <div style="margin-top: 4px;"><strong>MAESTRO:</strong> <span id="rep-parcial-maestro"><?= htmlspecialchars($docente['nombre_formal'] ?? 'SIZZO NAVARRO LIZETT') ?></span></div>
                </div>
                <div>
                    <div><strong>GRUPO:</strong> <span id="rep-parcial-grupo">085</span></div>
                    <div style="margin-top: 4px;"><strong>PERIODO:</strong> <span id="rep-parcial-periodo"><?= htmlspecialchars($docente['periodo_actual'] ?? 'AGO-DIC/2026') ?></span></div>
                </div>
            </div>

            <!-- Tabla de Calificaciones Parciales con Alumnos y Unidades -->
            <table class="tabla-calificaciones-parciales" id="tabla-calificaciones-parciales-dom">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 30px;">No</th>
                        <th rowspan="2" style="width: 90px;">No. CONTROL</th>
                        <th rowspan="2">NOMBRE DEL ALUMNO</th>
                        <th colspan="8">EVALUACIÓN DE UNIDADES</th>
                        <th rowspan="2" style="width: 50px;">COMP.</th>
                        <th rowspan="2" style="width: 50px;">Prom.</th>
                    </tr>
                    <tr>
                        <th style="width: 38px;">I</th>
                        <th style="width: 38px;">II</th>
                        <th style="width: 38px;">III</th>
                        <th style="width: 38px;">IV</th>
                        <th style="width: 38px;">V</th>
                        <th style="width: 38px;">VI</th>
                        <th style="width: 38px;">VII</th>
                        <th style="width: 38px;">VIII</th>
                    </tr>
                </thead>
                <tbody id="cuerpo-tabla-rep-parcial">
                    <!-- Filas inyectadas por JS -->
                </tbody>
                <tfoot>
                    <tr class="fila-indices-reprobacion" id="fila-indices-reprobacion-dom">
                        <td colspan="3" style="text-align: right; padding-right: 10px;">Índices de reprobación</td>
                        <td id="rep-u1">0%</td>
                        <td id="rep-u2">0%</td>
                        <td id="rep-u3">0%</td>
                        <td id="rep-u4">0%</td>
                        <td id="rep-u5">0%</td>
                        <td id="rep-u6">0%</td>
                        <td id="rep-u7">0%</td>
                        <td id="rep-u8">0%</td>
                        <td>0%</td>
                        <td>0</td>
                    </tr>
                </tfoot>
            </table>

            <!-- Firmas -->
            <div class="seccion-firmas" style="margin-top: 30px;">
                <div class="caja-firma">
                    <div class="linea-firma"></div>
                    <div class="nombre-firma"><?= htmlspecialchars($docente['nombre_completo'] ?? 'MAN. Lizett Sizzo Navarro') ?></div>
                    <div class="cargo-firma">Firma del Docente</div>
                </div>
                <div class="caja-firma">
                    <div class="linea-firma"></div>
                    <div class="nombre-firma">MAE. Damaris Haydee Castillo Vega</div>
                    <div class="cargo-firma">Vo. Bo. División de Carrera</div>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- TEMPLATE 6: UTILERÍA -->
<template id="vista-utileria">
    <div style="background: #ffffff; padding: 25px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 20px;">
        <h2 style="color: #1b365d; margin-top: 0;">Centro de Utilerías y Descargas</h2>
        <p style="color: #4a5568;">Descarga y gestiona directamente los formatos y reportes oficiales del semestre sin salir del sistema:</p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 15px; margin-top: 20px;">
            <div style="border: 1px solid #e2e8f0; padding: 15px; border-radius: 6px; background: #fbfcfd;">
                <h4 style="margin: 0 0 8px 0; color: #1b365d;">Horario Institucional</h4>
                <p style="font-size: 12px; color: #718096; margin-bottom: 12px;">Horario completo de adscripción, actividades docentes y horas frente a grupo.</p>
                <div style="display: flex; gap: 8px;">
                    <a href="../../controllers/exportar_reporte.php?tipo=horario&formato=excel" class="btn-accion-reporte btn-excel" style="font-size: 12px;">Excel</a>
                    <button class="btn-accion-reporte btn-secundario" onclick="document.querySelector('.menu-opcion[data-vista=\'horario\']').click();" style="font-size: 12px;">Ver en Pantalla</button>
                </div>
            </div>

            <div style="border: 1px solid #e2e8f0; padding: 15px; border-radius: 6px; background: #fbfcfd;">
                <h4 style="margin: 0 0 8px 0; color: #1b365d;">Macroplaneación AGO-DIC 2026</h4>
                <p style="font-size: 12px; color: #718096; margin-bottom: 12px;">Documento oficial con créditos, horas de teoría, práctica y grupos asignados.</p>
                <div style="display: flex; gap: 8px;">
                    <a href="../../controllers/exportar_reporte.php?tipo=macroplaneacion&formato=excel" class="btn-accion-reporte btn-excel" style="font-size: 12px;">Excel</a>
                    <button class="btn-accion-reporte btn-secundario" onclick="document.querySelector('.menu-opcion[data-vista=\'documentos\']').click();" style="font-size: 12px;">Ver en Pantalla</button>
                </div>
            </div>

            <div style="border: 1px solid #e2e8f0; padding: 15px; border-radius: 6px; background: #fbfcfd;">
                <h4 style="margin: 0 0 8px 0; color: #1b365d;">Gestión del Curso (Excel Oficial)</h4>
                <p style="font-size: 12px; color: #718096; margin-bottom: 12px;">Reporte Parcial y Final con porcentajes de acreditación, reprobación y deserción.</p>
                <div style="display: flex; gap: 8px;">
                    <a href="../../controllers/exportar_reporte.php?tipo=gestion_curso&parcial=1" class="btn-accion-reporte btn-excel" style="font-size: 12px;">Excel</a>
                    <button class="btn-accion-reporte btn-secundario" onclick="document.querySelector('.menu-opcion[data-vista=\'documentos\']').click(); setTimeout(() => document.getElementById('tab-doc-gestion')?.click(), 100);" style="font-size: 12px;">Ver y Editar</button>
                </div>
            </div>

            <div style="border: 1px solid #e2e8f0; padding: 15px; border-radius: 6px; background: #fbfcfd;">
                <h4 style="margin: 0 0 8px 0; color: #1b365d;">Reportes de Calificaciones Parciales</h4>
                <p style="font-size: 12px; color: #718096; margin-bottom: 12px;">Listas oficiales de alumnos por grupo con evaluaciones de unidades I a VIII.</p>
                <div style="display: flex; gap: 8px;">
                    <a href="../../controllers/exportar_reporte.php?tipo=calificaciones_parciales&grupo=085" class="btn-accion-reporte btn-excel" style="font-size: 12px;">Grupo 085 Excel</a>
                    <button class="btn-accion-reporte btn-secundario" onclick="document.querySelector('.menu-opcion[data-vista=\'documentos\']').click(); setTimeout(() => document.getElementById('tab-doc-parcial')?.click(), 100);" style="font-size: 12px;">Ver Reportes</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script src="../../assets/js/menu.js?v=<?= filemtime(__DIR__ . '/../../assets/js/menu.js') ?>" defer></script>
</body>
</html>
