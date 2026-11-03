<link rel="stylesheet" href="../../assets/css/calificaciones.css">
<div class="calificaciones-layout">
    <aside class="calificaciones-sidebar">
        <h3 class="titulo-lista">Lista general</h3>
        <ul class="lista-unidades" id="nav-unidades">
            <!-- Se agregó el atributo data-vista para identificar qué unidad se clickea -->
            <li class="activo" data-unidad="General">General</li>
            <li data-unidad="Unidad 1">Unidad 1</li>
            <li data-unidad="Unidad 2">Unidad 2</li>
            <li data-unidad="Unidad 3">Unidad 3</li>
            <li data-unidad="Unidad 4">Unidad 4</li>
            <li data-unidad="Unidad 5">Unidad 5</li>
        </ul>
        <button class="btn-graficas" id="btn-nav-graficas">📊 Gráficas</button>
    </aside>

    <section class="calificaciones-contenido">
        <div class="encabezado-tabla">
            <!-- Se agregó un span con ID para cambiar el título dinámicamente -->
            <h3 class="titulo-alumnos">Lista de alumnos - <span id="titulo-vista-actual">General</span></h3>
            <button class="btn-regresar" id="btn-volver-grupos">⬅ Volver a Grupos</button>
        </div>

        <!-- CONTENEDOR 1: TABLA (Visible por defecto) -->
        <div id="contenedor-tabla-excel">
           <div class="tabla-excel-contenedor">
            <table class="tabla-excel">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th class="col-dinamica">U1</th>
                        <th class="col-dinamica">U2</th>
                        <th class="col-dinamica">U3</th>
                        <th>FINAL</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- ALUMNO FALSO 1: Usamos data-id-alumno="00000001" -->
                    <tr class="fila-aprobado" data-id-alumno="00000001">
                        <td>Alumno de Prueba 1</td>
                        <td><input type="number" class="celda-input" value=""></td>
                        <td><input type="number" class="celda-input" value=""></td>
                        <td><input type="number" class="celda-input" value=""></td>
                        <td><strong>0</strong></td>
                        <td><button class="btn-detalles">Detalles</button></td>
                    </tr>
                    
                    <!-- ALUMNO FALSO 2: Usamos data-id-alumno="00000002" -->
                    <tr class="fila-reprobado" data-id-alumno="00000002">
                        <td>Alumno de Prueba 2</td>
                        <td><input type="number" class="celda-input" value=""></td>
                        <td><input type="number" class="celda-input" value=""></td>
                        <td><input type="number" class="celda-input" value=""></td>
                        <td><strong>0</strong></td>
                        <td><button class="btn-detalles">Detalles</button></td>
                    </tr>

                    <!-- ALUMNO FALSO 2: Usamos data-id-alumno="00000002" -->
                    <tr class="fila-reprobado" data-id-alumno="00000002">
                        <td>Alumno de Prueba 2</td>
                        <td><input type="number" class="celda-input" value=""></td>
                        <td><input type="number" class="celda-input" value=""></td>
                        <td><input type="number" class="celda-input" value=""></td>
                        <td><strong>0</strong></td>
                        <td><button class="btn-detalles">Detalles</button></td>
                    </tr>

                    <!-- ALUMNO FALSO 2: Usamos data-id-alumno="00000002" -->
                    <tr class="fila-reprobado" data-id-alumno="00000002">
                        <td>Alumno de Prueba 2</td>
                        <td><input type="number" class="celda-input" value=""></td>
                        <td><input type="number" class="celda-input" value=""></td>
                        <td><input type="number" class="celda-input" value=""></td>
                        <td><strong>0</strong></td>
                        <td><button class="btn-detalles">Detalles</button></td>
                    </tr>

                    <!-- ALUMNO FALSO 2: Usamos data-id-alumno="00000002" -->
                    <tr class="fila-reprobado" data-id-alumno="00000002">
                        <td>Alumno de Prueba 2</td>
                        <td><input type="number" class="celda-input" value=""></td>
                        <td><input type="number" class="celda-input" value=""></td>
                        <td><input type="number" class="celda-input" value=""></td>
                        <td><strong>0</strong></td>
                        <td><button class="btn-detalles">Detalles</button></td>
                    </tr>

                    <!-- ALUMNO FALSO 2: Usamos data-id-alumno="00000002" -->
                    <tr class="fila-reprobado" data-id-alumno="00000002">
                        <td>Alumno de Prueba 2</td>
                        <td><input type="number" class="celda-input" value=""></td>
                        <td><input type="number" class="celda-input" value=""></td>
                        <td><input type="number" class="celda-input" value=""></td>
                        <td><strong>0</strong></td>
                        <td><button class="btn-detalles">Detalles</button></td>
                    </tr>
                </tbody>
            </table>
            
            <div class="controles-tabla">
                <button class="btn-agregar-fila" id="btn-agregar-alumno">+ Agregar Alumno</button>
                <button class="btn-secundario" id="btn-agregar-col" style="display: none;">+ Añadir Entregable</button>
            </div>
        </div>

        <!-- AQUÍ AÑADIMOS EL BOTÓN DE GUARDADO -->
        <div class="calificaciones-pie">
            <button class="btn-importar">📥 Importar lista desde excel</button>
            <button class="btn-secundario" id="btn-guardar-calificaciones" style="background-color: #007bff;"> Guardar Cambios</button> 
            <button class="btn-generar-reporte">📄 Generar reporte final</button>
        </div> <!-- Cierre de tabla-excel-contenedor -->
            </div>

            <div class="calificaciones-pie">
                <button class="btn-importar">📥 Importar lista desde excel</button>
                <button class="btn-generar-reporte">📄 Generar reporte final</button>
            </div>
            </div>

        <!-- CONTENEDOR 2: GRÁFICAS (Oculto por defecto) -->
        <div id="contenedor-graficas" style="display: none;">
            <article class="tarjeta vista-informativa" style="margin-top: 2rem; text-align: center;">
                <div class="icono-tarjeta" style="font-size: 3rem;">📊</div>
                <h3>Estadísticas de Aprobación</h3>
                <p class="aviso">Aquí se renderizarán las gráficas de esta materia.</p>
            </article>
        </div>
    </section>
    <script src="../../assets/js/menu.js" defer></script>
</div>