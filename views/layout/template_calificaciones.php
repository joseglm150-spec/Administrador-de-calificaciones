<div class="calificaciones-layout">
    <aside class="calificaciones-sidebar">
        <h3 class="titulo-lista">Lista general</h3>
        <ul class="lista-unidades" id="nav-unidades">
            <li class="activo" data-unidad="General">General</li>
            <li data-unidad="Unidad 1">Unidad 1</li>
            <li data-unidad="Unidad 2">Unidad 2</li>
            <li data-unidad="Unidad 3">Unidad 3</li>
            <li data-unidad="Unidad 4">Unidad 4</li>
        </ul>
        <div style="padding: 12px; display: flex; flex-direction: column; gap: 8px;">
            <button class="btn-accion-reporte btn-secundario btn-abrir-reporte-modal" id="btn-ver-reporte-parcial-actual" type="button" style="width: 100%; justify-content: center;">
                Reporte Parcial
            </button>
            <button class="btn-accion-reporte btn-excel" id="btn-descargar-excel-grupo" type="button" style="width: 100%; justify-content: center;">
                Descargar Excel
            </button>
            <button class="btn-accion-reporte btn-pdf" id="btn-imprimir-grupo" type="button" style="width: 100%; justify-content: center;">
                Imprimir / PDF
            </button>
        </div>
    </aside>

    <section class="calificaciones-contenido">
        <div class="encabezado-tabla">
            <div>
                <h3 class="titulo-alumnos" style="margin: 0; font-size: 16px; color: #1b365d;">
                    Lista de alumnos - <span class="titulo-vista-actual">General</span>
                </h3>
                <small id="subtitulo-grupo-info" style="color: #718096; font-size: 12px;"></small>
            </div>
            <div style="display: flex; gap: 8px; align-items: center;">
                <button class="btn-accion-reporte btn-primario" id="btn-guardar-calificaciones" style="background-color: #007bff; border-color: #007bff;">
                    Guardar Cambios
                </button>
                <button class="btn-regresar" id="btn-volver-grupos" type="button">Volver a Grupos</button>
            </div>
        </div>

        <!-- CONTENEDOR 1: TABLA -->
        <div id="contenedor-tabla-excel">
           <div class="tabla-excel-contenedor">
                <table class="tabla-excel">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th class="col-dinamica">U1</th>
                            <th class="col-dinamica">U2</th>
                            <th class="col-dinamica">U3</th>
                            <th class="col-dinamica">U4</th>
                            <th>FINAL</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Alumnos inyectados dinámicamente por construirTablaConDatos -->
                    </tbody>
                </table>
                
                <div class="controles-tabla" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 15px; background: #f8fafc; border-top: 1px solid #e2e8f0;">
                    <button class="btn-agregar-fila" id="btn-agregar-alumno" style="width: auto; padding: 8px 12px;">+ Agregar Alumno</button>
                    <button class="btn-accion-reporte btn-secundario" id="btn-agregar-col" style="display: none; font-size: 12px;">+ Añadir Entregable</button>
                </div>
            </div>

            <!-- PIE DE ACCIONES -->
            <div class="calificaciones-pie" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 20px; background: #ffffff; border-top: 1px solid #e2e8f0; flex-wrap: wrap; gap: 10px;">
                <div style="display: flex; gap: 8px;">
                    <button class="btn-accion-reporte btn-excel" id="btn-exportar-grupo-pie" type="button">Exportar Lista (Excel)</button>
                    <button class="btn-accion-reporte btn-secundario btn-abrir-reporte-modal" id="btn-reporte-parcial-pie" type="button">Reporte Parcial Oficial</button>
                </div>
                <div>
                    <button class="btn-accion-reporte btn-primario" id="btn-guardar-calificaciones-pie" type="button" style="background-color: #007bff; border-color: #007bff;">
                        Guardar Calificaciones
                    </button>
                </div>
            </div>
        </div>

        <!-- CONTENEDOR 2: GRÁFICAS -->
        <div id="contenedor-graficas" style="display: none;">
            <article class="tarjeta vista-informativa" style="margin: 2rem auto; text-align: center; max-width: 600px;">
                <h3>Estadísticas de Aprobación</h3>
                <p class="aviso">Distribución de calificaciones del grupo activo.</p>
                <div id="graficas-estadisticas-contenido" style="margin-top: 15px;"></div>
            </article>
        </div>
    </section>
</div>
