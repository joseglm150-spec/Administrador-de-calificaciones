<div class="tabla-grupos-contenedor">
    <?php if ($error_db): ?>
        <p style="color: red; padding: 1rem;"><?= htmlspecialchars($error_db) ?></p>
    <?php endif; ?>
    
    <table class="tabla-grupos">
        <caption>Selecciona un grupo</caption>
        <thead>
            <tr>
                <th scope="col">Seleccionar</th>
                <th scope="col">Grupo</th>
                <th scope="col">Asignatura (ID)</th>
                <th scope="col">Periodo</th>
                <th scope="col">Capacidad</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($grupos)): ?>
                <?php foreach ($grupos as $grupo): ?>
                <tr>
                    <td><button class="boton-seleccionar-grupo" type="button" data-grupo-selector aria-pressed="false">Seleccionar</button></td>
                    <th scope="row"><?= htmlspecialchars($grupo['clave_grupo']) ?></th>
                    <td><?= htmlspecialchars($grupo['materia']) ?></td>
                    <td><?= htmlspecialchars($grupo['periodo']) ?></td>
                    <td><?= htmlspecialchars($grupo['capacidad']) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 1.5rem;">No tienes grupos asignados actualmente.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>