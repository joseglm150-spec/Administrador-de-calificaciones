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
        // Quitar clase activo de todos los botones y ponérsela al clickeado
        document.querySelectorAll('.menu-opcion').forEach(btn => btn.classList.remove('activo'));
        menuBtn.classList.add('activo');
        
        // Cambiar el título de la cabecera superior
        const titulo = menuBtn.dataset.titulo;
        document.getElementById('titulo-seccion').textContent = titulo;
        
        // Contenedor principal donde se inyectan las vistas
        const contenedorPrincipal = document.getElementById('contenido-principal');
        
        // Limpiamos el contenedor
        contenedorPrincipal.innerHTML = ''; 
        
        // Inyectamos la vista correspondiente usando los <template>
        const nombreVista = menuBtn.dataset.vista; // "grupos", "documentos", etc.
        const template = document.getElementById('vista-' + nombreVista);
        
        if (template) {
            contenedorPrincipal.appendChild(template.content.cloneNode(true));
        }

        // Si el usuario da clic en "Grupos" en el menú izquierdo, salimos del grupo activo
        if (nombreVista === 'grupos') {
            grupoActivo = null; 
        }
        return; 
    }

    // ---------------------------------------------------------
    // 2. ABRIR LA TABLA DE UN GRUPO ESPECÍFICO
    // ---------------------------------------------------------
    if (e.target.matches('.boton-seleccionar-grupo')) {
        const filaGrupo = e.target.closest('tr');
        grupoActivo = filaGrupo.querySelector('th').textContent.trim(); // Ej: "081"
        
        const contenedorPrincipal = document.getElementById('contenido-principal');
        const tablaGrupos = document.querySelector('.tabla-grupos-contenedor');
        
        // Ocultar la lista de grupos principal
        if (tablaGrupos) tablaGrupos.style.display = 'none';

        // Si nunca hemos abierto este grupo, lo creamos desde el template
        if (!memoriaGrupos[grupoActivo]) {
            const template = document.getElementById('vista-calificaciones-grupo');
            if (template) {
                const clon = template.content.cloneNode(true);
                
                const wrapper = document.createElement('div');
                wrapper.className = 'instancia-grupo-activo';
                wrapper.appendChild(clon);
                
                // Personalizamos el título con el nombre del grupo
                const tituloTabla = wrapper.querySelector('.titulo-alumnos');
                if (tituloTabla) {
                    tituloTabla.innerHTML = `Lista de alumnos - Grupo ${grupoActivo} - <span class="titulo-vista-actual">General</span>`;
                }
                
                contenedorPrincipal.appendChild(wrapper);
                memoriaGrupos[grupoActivo] = wrapper; // Guardamos en memoria
            }
        } else {
            // Si ya estaba en memoria, nos aseguramos de que vuelva a existir en el HTML
            if (!contenedorPrincipal.contains(memoriaGrupos[grupoActivo])) {
                contenedorPrincipal.appendChild(memoriaGrupos[grupoActivo]);
            }
        }

        // Ocultar cualquier otro grupo abierto y mostrar solo el seleccionado
        document.querySelectorAll('.instancia-grupo-activo').forEach(inst => inst.style.display = 'none');
        memoriaGrupos[grupoActivo].style.display = 'block';
        return;
    }

    // ---------------------------------------------------------
    // 3. BOTÓN DE "VOLVER A GRUPOS"
    // ---------------------------------------------------------
    if (e.target.matches('.btn-regresar') || e.target.closest('.btn-regresar')) {
        // Ocultamos el grupo actual (no se borra, sigue en memoria)
        if (memoriaGrupos[grupoActivo]) memoriaGrupos[grupoActivo].style.display = 'none';
        
        grupoActivo = null;
        
        // Volvemos a mostrar la lista de selección de grupos
        const tablaGrupos = document.querySelector('.tabla-grupos-contenedor');
        if (tablaGrupos) tablaGrupos.style.display = 'block';
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
        inicializarColumnasOcultas(vistaActiva);

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
});

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
// FUNCIÓN PARA PREPARAR LA TABLA OCULTA
// ==========================================
function inicializarColumnasOcultas(vistaActiva) {
    const thead = vistaActiva.querySelector('.tabla-excel thead tr');
    const filas = vistaActiva.querySelectorAll('.tabla-excel tbody tr');

    if (thead.querySelector('th[data-vista="Unidad 1"]')) return;

    const thsOriginales = thead.querySelectorAll('th.col-dinamica');
    thsOriginales.forEach(th => th.dataset.vista = 'General');

    filas.forEach(fila => {
        const celdas = fila.children;
        if (celdas[1]) celdas[1].dataset.vista = 'General';
        if (celdas[2]) celdas[2].dataset.vista = 'General';
        if (celdas[3]) celdas[3].dataset.vista = 'General';
    });

    for (let i = 1; i <= 5; i++) {
        const nombreVista = 'Unidad ' + i;
        const finalTh = thead.querySelector('th:nth-last-child(2)'); 
        
        const nuevoTh = document.createElement('th');
        nuevoTh.className = 'col-dinamica encabezado-editable';
        nuevoTh.contentEditable = 'true';
        nuevoTh.textContent = 'Entregables';
        nuevoTh.title = 'Clic derecho para eliminar';
        nuevoTh.dataset.vista = nombreVista;
        nuevoTh.style.display = 'none';
        
        thead.insertBefore(nuevoTh, finalTh);

        filas.forEach(fila => {
            const finalTd = fila.querySelector('td:nth-last-child(2)');
            const nuevoTd = document.createElement('td');
            nuevoTd.innerHTML = '<input type="number" class="celda-input" value="">';
            nuevoTd.dataset.vista = nombreVista;
            nuevoTd.style.display = 'none';
            fila.insertBefore(nuevoTd, finalTd);
        });
    }
}
// ==========================================
// INICIALIZACIÓN AL CARGAR LA PÁGINA
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    // Buscar el botón de "Grupos" en el menú lateral
    const btnGrupos = document.querySelector('.menu-opcion[data-vista="grupos"]');
    
    // Si existe, simulamos un clic para que cargue esa vista automáticamente
    if (btnGrupos) {
        btnGrupos.click();
    }
});