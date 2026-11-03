// ==========================================
// ESTADO GLOBAL DE LA APLICACIÓN
// ==========================================
const memoriaGrupos = {}; 
let grupoActivo = null;

// ==========================================
// EVENTO PRINCIPAL DE CLIC EN LA PÁGINA
// ==========================================
document.addEventListener('click', function(e) {
    
    // ---------------------------------------------------------
    // 1. NAVEGACIÓN DEL MENÚ PRINCIPAL (SIDEBAR IZQUIERDO)
    // ---------------------------------------------------------
    const menuBtn = e.target.closest('.menu-opcion');
    if (menuBtn) {
        document.querySelectorAll('.menu-opcion').forEach(btn => btn.classList.remove('activo'));
        menuBtn.classList.add('activo');
        
        const titulo = menuBtn.dataset.titulo;
        document.getElementById('titulo-seccion').textContent = titulo;
        
        const contenedorPrincipal = document.getElementById('contenido-principal');
        contenedorPrincipal.innerHTML = ''; 
        
        const nombreVista = menuBtn.dataset.vista; 
        const template = document.getElementById('vista-' + nombreVista);
        
        if (template) {
            contenedorPrincipal.appendChild(template.content.cloneNode(true));
        }

        if (nombreVista === 'grupos') {
            grupoActivo = null; 
        }
        return; 
    }

    // ---------------------------------------------------------
    // 2. ABRIR LA TABLA DE UN GRUPO (AQUÍ LEEMOS LA BD)
    // ---------------------------------------------------------
    if (e.target.matches('.boton-seleccionar-grupo')) {
        const filaGrupo = e.target.closest('tr');
        grupoActivo = filaGrupo.querySelector('th').textContent.trim(); 
        
        const contenedorPrincipal = document.getElementById('contenido-principal');
        const tablaGrupos = document.querySelector('.tabla-grupos-contenedor');
        if (tablaGrupos) tablaGrupos.style.display = 'none';

        if (!memoriaGrupos[grupoActivo]) {
            // Mostrar mensaje de carga mientras PHP busca los datos
            contenedorPrincipal.innerHTML = '<h3 style="padding: 3rem; text-align: center;">Cargando calificaciones... ⏳</h3>';

            // ATENCIÓN: Ajusta la ruta si es necesario
            fetch(`/Administrador-de-calificaciones/controllers/obtener_calificaciones.php?grupo=${grupoActivo}`)
            .then(res => res.json())
            .then(datosBD => {
                contenedorPrincipal.innerHTML = ''; 
                
                const template = document.getElementById('vista-calificaciones-grupo');
                if (template) {
                    const clon = template.content.cloneNode(true);
                    const wrapper = document.createElement('div');
                    wrapper.className = 'instancia-grupo-activo';
                    wrapper.appendChild(clon);
                    
                    const tituloTabla = wrapper.querySelector('.titulo-alumnos');
                    if (tituloTabla) {
                        tituloTabla.innerHTML = `Lista de alumnos - Grupo ${grupoActivo} - <span class="titulo-vista-actual">General</span>`;
                    }
                    
                    // Reconstruimos la tabla con los datos reales
                    construirTablaConDatos(wrapper, datosBD);
                    
                    contenedorPrincipal.appendChild(wrapper);
                    memoriaGrupos[grupoActivo] = wrapper; 
                }
            })
            .catch(error => {
                console.error("Error al cargar la Base de Datos:", error);
                alert("Hubo un problema al cargar los datos de este grupo.");
            });
            return;
        } else {
            if (!contenedorPrincipal.contains(memoriaGrupos[grupoActivo])) {
                contenedorPrincipal.appendChild(memoriaGrupos[grupoActivo]);
            }
        }

        document.querySelectorAll('.instancia-grupo-activo').forEach(inst => inst.style.display = 'none');
        memoriaGrupos[grupoActivo].style.display = 'block';
        return;
    }

    // ---------------------------------------------------------
    // 3. BOTÓN DE "VOLVER A GRUPOS"
    // ---------------------------------------------------------
    if (e.target.matches('.btn-regresar') || e.target.closest('.btn-regresar')) {
        // Ocultar la tabla del grupo actual sin borrarla de memoria
        if (grupoActivo && memoriaGrupos[grupoActivo]) {
            memoriaGrupos[grupoActivo].style.display = 'none';
        }
        
        grupoActivo = null;
        
        // Volver a mostrar la vista principal de Grupos
        const contenedorPrincipal = document.getElementById('contenido-principal');
        const tablaGrupos = contenedorPrincipal.querySelector('.tabla-grupos-contenedor');
        
        if (tablaGrupos) {
            // Si la tabla sigue en el DOM, simplemente la mostramos
            tablaGrupos.style.display = 'block';
        } else {
            // Si por alguna razón no está en el DOM (ej. el contenedor se limpió), la inyectamos de nuevo desde el template
            contenedorPrincipal.innerHTML = '';
            const templateGrupos = document.getElementById('vista-grupos');
            if (templateGrupos) {
                contenedorPrincipal.appendChild(templateGrupos.content.cloneNode(true));
            }
        }
        
        // Nos aseguramos de que el título superior vuelva a decir "Mis Grupos"
        document.getElementById('titulo-seccion').textContent = 'Mis Grupos';
        
        // Remarcamos el botón "Grupos" en el menú izquierdo por si el usuario se había movido a otro lado
        document.querySelectorAll('.menu-opcion').forEach(btn => btn.classList.remove('activo'));
        const btnGruposMenu = document.querySelector('.menu-opcion[data-vista="grupos"]');
        if (btnGruposMenu) btnGruposMenu.classList.add('activo');

        return;
    }

    // =========================================================================
    // RESTRICCIÓN: TODO LO DE ABAJO SOLO FUNCIONA DENTRO DE UN GRUPO ABIERTO
    // =========================================================================
    if (!grupoActivo || !memoriaGrupos[grupoActivo]) return;
    
    const vistaActiva = memoriaGrupos[grupoActivo];
    if (!vistaActiva.contains(e.target)) return;

    // ---------------------------------------------------------
    // 4. NAVEGACIÓN POR UNIDADES 
    // ---------------------------------------------------------
    if (e.target.matches('.lista-unidades li')) {
        const todosLosLi = vistaActiva.querySelectorAll('.lista-unidades li');
        todosLosLi.forEach(li => li.classList.remove('activo'));
        e.target.classList.add('activo');
        
        const unidadSeleccionada = e.target.dataset.unidad;

        const spanTitulo = vistaActiva.querySelector('.titulo-vista-actual');
        if (spanTitulo) spanTitulo.textContent = unidadSeleccionada;

        const theadThs = vistaActiva.querySelectorAll('.tabla-excel thead th.col-dinamica');
        theadThs.forEach(th => {
            th.style.display = (th.dataset.vista === unidadSeleccionada) ? 'table-cell' : 'none';
        });

        const tbodyFilas = vistaActiva.querySelectorAll('.tabla-excel tbody tr');
        tbodyFilas.forEach(fila => {
            fila.querySelectorAll('td[data-vista]').forEach(td => {
                td.style.display = (td.dataset.vista === unidadSeleccionada) ? 'table-cell' : 'none';
            });
        });

        const btnAgregarCol = vistaActiva.querySelector('#btn-agregar-col');
        if (btnAgregarCol) {
            btnAgregarCol.style.display = (unidadSeleccionada === 'General') ? 'none' : 'inline-block';
        }
    }

    // ---------------------------------------------------------
    // 5. AÑADIR NUEVA COLUMNA (ENTREGABLE)
    // ---------------------------------------------------------
    if (e.target.matches('#btn-agregar-col') || e.target.closest('#btn-agregar-col')) {
        const tabActivo = vistaActiva.querySelector('.lista-unidades li.activo');
        if (!tabActivo) return;
        
        const unidadActual = tabActivo.dataset.unidad;
        const theadRow = vistaActiva.querySelector('.tabla-excel thead tr');
        const finalTh = theadRow.querySelector('th:nth-last-child(2)'); 
        
        const nuevoTh = document.createElement('th');
        nuevoTh.className = 'col-dinamica encabezado-editable';
        nuevoTh.contentEditable = 'true';
        nuevoTh.textContent = 'Entregables';
        nuevoTh.title = 'Clic derecho para eliminar';
        nuevoTh.dataset.vista = unidadActual; 
        
        theadRow.insertBefore(nuevoTh, finalTh);
        
        const filas = vistaActiva.querySelectorAll('.tabla-excel tbody tr');
        filas.forEach(fila => {
            const finalTd = fila.querySelector('td:nth-last-child(2)');
            const nuevoTd = document.createElement('td');
            nuevoTd.innerHTML = '<input type="number" class="celda-input" value="">';
            nuevoTd.dataset.vista = unidadActual;
            fila.insertBefore(nuevoTd, finalTd);
        });
    }

    // ---------------------------------------------------------
    // 6. GUARDAR CALIFICACIONES (ENVIAR A PHP TODAS LAS UNIDADES)
    // ---------------------------------------------------------
    if (e.target.matches('#btn-guardar-calificaciones')) {
        const btnGuardar = e.target;
        
        const payload = {
            clave_grupo: grupoActivo,
            unidades: []
        };

        const nombresUnidades = ['Unidad 1', 'Unidad 2', 'Unidad 3', 'Unidad 4', 'Unidad 5'];
        const filasAlumnos = vistaActiva.querySelectorAll('.tabla-excel tbody tr');

        nombresUnidades.forEach(nombreUnidad => {
            const thsUnidad = vistaActiva.querySelectorAll(`th.col-dinamica[data-vista="${nombreUnidad}"]`);
            const nombresColumnas = Array.from(thsUnidad).map(th => th.textContent.trim());

            const alumnosData = [];
            filasAlumnos.forEach(fila => {
                const idAlumno = fila.dataset.idAlumno; 
                const inputsUnidad = fila.querySelectorAll(`td[data-vista="${nombreUnidad}"] input`);
                const notas = Array.from(inputsUnidad).map(input => input.value);
                
                alumnosData.push({
                    id_alumno: idAlumno,
                    notas: notas
                });
            });

            payload.unidades.push({
                unidad: nombreUnidad,
                columnas: nombresColumnas,
                alumnos: alumnosData
            });
        });

        const textoOriginal = btnGuardar.textContent;
        btnGuardar.textContent = '⏳ Guardando todo...';
        btnGuardar.disabled = true;

        fetch('/Administrador-de-calificaciones/controllers/guardar_calificaciones.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(respuesta => respuesta.json())
        .then(datos => {
            if (datos.exito) alert('¡Calificaciones guardadas correctamente!');
            else alert('Error al guardar: ' + datos.mensaje);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión con el servidor.');
        })
        .finally(() => {
            btnGuardar.textContent = textoOriginal;
            btnGuardar.disabled = false;
        });
    }

}); // <--- CIERRE DEL EVENTO DE CLIC

// ==========================================
// EVENTO DE CLIC DERECHO (ELIMINAR COLUMNA)
// ==========================================
document.addEventListener('contextmenu', function(e) {
    if (!grupoActivo || !memoriaGrupos[grupoActivo]) return;
    const vistaActiva = memoriaGrupos[grupoActivo];

    if (!vistaActiva.contains(e.target)) return;

    if (e.target.matches('th.col-dinamica.encabezado-editable')) {
        e.preventDefault(); 
        
        const tabActivo = vistaActiva.querySelector('.lista-unidades li.activo');
        if (!tabActivo) return;
        
        const unidadActual = tabActivo.dataset.unidad;
        const columnasDeEstaUnidad = vistaActiva.querySelectorAll(`th.col-dinamica[data-vista="${unidadActual}"]`);
        
        if (columnasDeEstaUnidad.length <= 1) {
            alert('Debe haber al menos un entregable por unidad.');
            return;
        }

        const thSeleccionado = e.target;
        const nombreEntregable = thSeleccionado.textContent.trim() || 'Entregable';

        if (confirm(`¿Seguro que deseas eliminar la columna "${nombreEntregable}" del Grupo ${grupoActivo}?`)) {
            const trPadre = thSeleccionado.closest('tr');
            const indiceColumna = Array.from(trPadre.children).indexOf(thSeleccionado);

            thSeleccionado.remove();

            const filasAlumnos = vistaActiva.querySelectorAll('.tabla-excel tbody tr');
            filasAlumnos.forEach(fila => {
                const celdas = fila.children;
                if (celdas[indiceColumna]) celdas[indiceColumna].remove();
            });
        }
    }
});

// ==========================================
// FUNCIÓN PARA CONSTRUIR TABLA DESDE BASE DE DATOS
// ==========================================
function construirTablaConDatos(vistaActiva, datosBD) {
    const thead = vistaActiva.querySelector('.tabla-excel thead tr');
    const filas = vistaActiva.querySelectorAll('.tabla-excel tbody tr');

    // Asignar los identificadores a la vista 'General' predeterminada
    const thsOriginales = thead.querySelectorAll('th.col-dinamica');
    thsOriginales.forEach(th => th.dataset.vista = 'General');

    filas.forEach(fila => {
        const celdas = fila.children;
        if (celdas[1]) celdas[1].dataset.vista = 'General';
        if (celdas[2]) celdas[2].dataset.vista = 'General';
        if (celdas[3]) celdas[3].dataset.vista = 'General';
    });

    // Agrupar los entregables de la base de datos por su Unidad
    const entregablesPorUnidad = {
        'Unidad 1': [], 'Unidad 2': [], 'Unidad 3': [], 'Unidad 4': [], 'Unidad 5': []
    };
    
    if (datosBD.exito && datosBD.entregables) {
        datosBD.entregables.forEach(ent => {
            if (entregablesPorUnidad[ent.unidad]) {
                entregablesPorUnidad[ent.unidad].push(ent);
            }
        });
    }

    // Dibujar las columnas ocultas para cada unidad
    for (let i = 1; i <= 5; i++) {
        const nombreVista = 'Unidad ' + i;
        const finalTh = thead.querySelector('th:nth-last-child(2)'); 
        const entsUnidad = entregablesPorUnidad[nombreVista];
        
        if (entsUnidad.length === 0) {
            // Si la unidad está en blanco (no hay datos), le ponemos 1 Entregable por defecto
            crearColumnaBD(thead, filas, nombreVista, 'Entregables', null, [], finalTh);
        } else {
            // Recrear exactamente las columnas con los nombres que el docente guardó
            entsUnidad.forEach(ent => {
                crearColumnaBD(thead, filas, nombreVista, ent.nombre, ent.id_entregable, datosBD.calificaciones || [], finalTh);
            });
        }
    }
}

// Función auxiliar para generar las celdas y vaciarles el número correcto
function crearColumnaBD(thead, filas, nombreVista, nombreColumna, idEntregable, calificaciones, finalTh) {
    const nuevoTh = document.createElement('th');
    nuevoTh.className = 'col-dinamica encabezado-editable';
    nuevoTh.contentEditable = 'true';
    nuevoTh.textContent = nombreColumna;
    nuevoTh.title = 'Clic derecho para eliminar';
    nuevoTh.dataset.vista = nombreVista;
    nuevoTh.style.display = 'none';
    
    thead.insertBefore(nuevoTh, finalTh);

    filas.forEach(fila => {
        const finalTd = fila.querySelector('td:nth-last-child(2)');
        const nuevoTd = document.createElement('td');
        
        let valorNota = '';
        if (idEntregable) {
            const idAlumno = fila.dataset.idAlumno;
            // Buscar si el alumno tiene un número guardado para esta columna exacta
            const notaEncontrada = calificaciones.find(c => c.numero_control === idAlumno && c.id_entregable === idEntregable);
            if (notaEncontrada && notaEncontrada.calificacion !== null) {
                valorNota = notaEncontrada.calificacion;
            }
        }

        nuevoTd.innerHTML = `<input type="number" class="celda-input" value="${valorNota}">`;
        nuevoTd.dataset.vista = nombreVista;
        nuevoTd.style.display = 'none';
        fila.insertBefore(nuevoTd, finalTd);
    });
}

// ==========================================
// INICIALIZACIÓN AL CARGAR LA PÁGINA
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    const btnGrupos = document.querySelector('.menu-opcion[data-vista="grupos"]');
    if (btnGrupos) btnGrupos.click();
});