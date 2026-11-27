<div class="tabla-grupos-contenedor">
    <?php if ($error_db): ?>
        <p style="color: #c53030; background: #fff5f5; padding: 1rem; border-radius: 6px; border: 1px solid #feb2b2; margin: 20px 24px 0;"><?= htmlspecialchars($error_db) ?></p>
    <?php endif; ?>

    <div class="grupos-encabezado">
        <div>
            <h2 class="grupos-titulo">Mis Grupos Asignados</h2>
            <p class="grupos-subtitulo">Periodo: <?= htmlspecialchars($docente['periodo_actual'] ?? 'AGOSTO-DICIEMBRE/2026') ?> | Docente: <?= htmlspecialchars($docente['nombre_completo'] ?? $nombreUsuario) ?></p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button class="btn-accion-reporte btn-secundario" type="button" onclick="document.querySelector('.menu-opcion[data-vista=\'horario\']').click();">
                Ver Horario
            </button>
            <button class="btn-accion-reporte btn-primario" type="button" onclick="document.querySelector('.menu-opcion[data-vista=\'documentos\']').click();">
                Reportes Oficiales
            </button>
        </div>
    </div>
    
    <table class="tabla-grupos">
        <caption style="text-align: left; font-weight: bold; margin-bottom: 8px; color: #4a5568;">Selecciona un grupo para capturar calificaciones o generar reportes</caption>
        <thead>
            <tr>
                <th scope="col" style="width: 130px;">Acción</th>
                <th scope="col">Grupo</th>
                <th scope="col">Asignatura</th>
                <th scope="col">Carrera / Semestre</th>
                <th scope="col">Aula</th>
                <th scope="col">Alumnos</th>
                <th scope="col">Reportes Oficiales</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($grupos)): ?>
                <?php foreach ($grupos as $grupo): ?>
                <tr>
                    <td>
                        <button class="boton-seleccionar-grupo btn-accion-reporte btn-primario" type="button" data-grupo="<?= htmlspecialchars($grupo['clave_grupo']) ?>" style="padding: 6px 12px; font-size: 12px; width: 100%;">
                            Calificar
                        </button>
                    </td>
                    <th scope="row" style="font-size: 15px; color: #1b365d;"><?= htmlspecialchars($grupo['clave_grupo']) ?></th>
                    <td>
                        <strong><?= htmlspecialchars($grupo['nombre_materia']) ?></strong>
                        <div style="font-size: 11px; color: #718096;">Clave: <?= htmlspecialchars($grupo['materia']) ?> | <?= $grupo['creditos'] ?> Créditos</div>
                    </td>
                    <td>
                        <span style="display: inline-block; background: #e2e8f0; padding: 2px 6px; border-radius: 4px; font-size: 11px; font-weight: bold; color: #2d3748;">
                            <?= htmlspecialchars($grupo['carrera']) ?>
                        </span>
                        <span style="font-size: 12px; color: #4a5568; margin-left: 4px;"><?= $grupo['semestre'] ?>° Sem.</span>
                    </td>
                    <td><?= htmlspecialchars($grupo['aula'] ?? 'Sin asignar') ?></td>
                    <td>
                        <strong><?= $grupo['total_inscritos'] ?? 0 ?></strong>
                        <span style="color: #a0aec0; font-size: 11px;">/ <?= $grupo['capacidad'] ?></span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 6px;">
                            <button type="button" class="btn-accion-reporte btn-secundario btn-abrir-reporte-parcial" data-grupo="<?= htmlspecialchars($grupo['clave_grupo']) ?>" style="padding: 4px 8px; font-size: 11px;" title="Ver Reporte Oficial de Calificaciones Parciales">
                                Parcial
                            </button>
                            <a href="../../controllers/exportar_reporte.php?tipo=calificaciones_parciales&grupo=<?= urlencode($grupo['clave_grupo']) ?>" class="btn-accion-reporte btn-excel" style="padding: 4px 8px; font-size: 11px;" title="Descargar en Excel">
                                Excel
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: #718096;">
                        <p style="font-size: 1.1rem; margin-bottom: 0.5rem;">No tienes grupos asignados actualmente.</p>
                        <small>Consulta con la subdirección académica o servicios escolares.</small>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
