<div class="calificaciones-layout">
    <aside class="calificaciones-sidebar">
        <h3 class="titulo-lista">Lista general</h3>
        <ul class="lista-unidades">
            <li class="activo">General</li>
            <li>Unidad 1</li>
            <li>Unidad 2</li>
            <li>Unidad 3</li>
            <li>Unidad 4</li>
            <li>Unidad 5</li>
        </ul>
        <button class="btn-graficas">📊 Gráficas</button>
    </aside>

    <section class="calificaciones-contenido">
        <div class="encabezado-tabla">
            <h3 class="titulo-alumnos">Lista de alumnos</h3>
            <button class="btn-regresar" id="btn-volver-grupos">⬅ Volver a Grupos</button>
        </div>

        <div class="tabla-excel-contenedor">
            <table class="tabla-excel">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>U1</th>
                        <th>U2</th>
                        <th>U3</th>
                        <th>FINAL</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="fila-aprobado">
                        <td>Aguilar Garcia Daniel</td>
                        <td><input type="number" class="celda-input" value="90"></td>
                        <td><input type="number" class="celda-input" value="85"></td>
                        <td><input type="number" class="celda-input" value="95"></td>
                        <td><strong>90</strong></td>
                        <td><button class="btn-detalles">Detalles</button></td>
                    </tr>
                </tbody>
            </table>
            <button class="btn-agregar-fila">+ Agregar</button>
        </div>

        <div class="calificaciones-pie">
            <button class="btn-importar">📥 Importar lista desde excel</button>
            <button class="btn-generar-reporte">📄 Generar reporte final</button>
        </div>
    </section>
</div>