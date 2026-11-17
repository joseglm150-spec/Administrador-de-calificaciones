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
        actualizarIndicador(vistaActiva);

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

        const filaValores = vistaActiva.querySelector('.tabla-excel thead .fila-valores');
        const finalValTh = filaValores.querySelector('th:nth-last-child(2)');
        filaValores.insertBefore(crearCeldaValor(unidadActual, 0), finalValTh);
        actualizarIndicador(vistaActiva);
        
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

        const nombresUnidades = Array.from(vistaActiva.querySelectorAll('.lista-unidades li[data-unidad]'))
            .map(li => li.dataset.unidad)
            .filter(u => u !== 'General');
        const filasAlumnos = vistaActiva.querySelectorAll('.tabla-excel tbody tr[data-id-alumno]');

        const errores = [];
        nombresUnidades.forEach(nombreUnidad => {
            const thsUnidad = vistaActiva.querySelectorAll(`th.col-dinamica.encabezado-editable[data-vista="${nombreUnidad}"]`);
            const nombresColumnas = Array.from(thsUnidad).map(th => th.textContent.trim());

            const valores = Array.from(vistaActiva.querySelectorAll(`th.col-valor[data-vista="${nombreUnidad}"] input`))
                .map(inp => parseFloat(inp.value) || 0);
            const sumaValores = valores.reduce((a, b) => a + b, 0);
            if (Math.abs(sumaValores - 100) > 0.01) errores.push(`${nombreUnidad}: suma ${sumaValores}% (debe ser 100%)`);

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
                valores: valores,
                alumnos: alumnosData
            });
        });

        if (errores.length) {
            alert('Los valores de los entregables deben sumar 100% en cada unidad:\n\n' + errores.join('\n'));
            return;
        }

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
        const columnasDeEstaUnidad = vistaActiva.querySelectorAll(`th.col-dinamica.encabezado-editable[data-vista="${unidadActual}"]`);
        
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
            const filaValoresDel = vistaActiva.querySelector('.tabla-excel thead .fila-valores');
            if (filaValoresDel && filaValoresDel.children[indiceColumna]) filaValoresDel.children[indiceColumna].remove();

            const filasAlumnos = vistaActiva.querySelectorAll('.tabla-excel tbody tr');
            filasAlumnos.forEach(fila => {
                const celdas = fila.children;
                if (celdas[indiceColumna]) celdas[indiceColumna].remove();
            });
            recalcularTodo(vistaActiva);
            actualizarIndicador(vistaActiva);
        }
    }
});

// ==========================================
// FUNCIÓN PARA CONSTRUIR TABLA DESDE BASE DE DATOS
// ==========================================
function construirTablaConDatos(vistaActiva, datosBD) {
    const thead = vistaActiva.querySelector('.tabla-excel thead tr');
    const tbody = vistaActiva.querySelector('.tabla-excel tbody');

    // Número de unidades de la materia (BD: materias.total_unidades, entre 4 y 7)
    let totalUnidades = parseInt(datosBD.total_unidades, 10);
    if (isNaN(totalUnidades)) totalUnidades = 4;
    totalUnidades = Math.min(7, Math.max(4, totalUnidades));
    vistaActiva.dataset.totalUnidades = totalUnidades;

    // ---- Barra lateral: General + Unidad 1..N ----
    const listaUnidades = vistaActiva.querySelector('.lista-unidades');
    listaUnidades.querySelectorAll('li:not([data-unidad="General"])').forEach(li => li.remove());
    for (let i = 1; i <= totalUnidades; i++) {
        const li = document.createElement('li');
        li.dataset.unidad = 'Unidad ' + i;
        li.textContent = 'Unidad ' + i;
        listaUnidades.appendChild(li);
    }

    // ---- Encabezado de la vista General: U1..UN (reemplaza los 3 fijos) ----
    thead.querySelectorAll('th.col-dinamica').forEach(th => th.remove());
    const finalThBase = thead.querySelector('th:nth-last-child(2)'); // FINAL

    // Segunda fila del encabezado: valor (%) de cada entregable
    const filaValores = document.createElement('tr');
    filaValores.className = 'fila-valores';
    filaValores.innerHTML = '<th>Valor (%)</th>';
    for (let i = 1; i <= totalUnidades; i++) {
        const thv = document.createElement('th');
        thv.className = 'col-dinamica col-valor';
        thv.dataset.vista = 'General';
        filaValores.appendChild(thv);
    }
    filaValores.insertAdjacentHTML('beforeend', '<th></th><th></th>');
    thead.parentNode.appendChild(filaValores);

    // Indicador de suma de valores (solo en vistas de unidad)
    if (!vistaActiva.querySelector('.indicador-suma-valores')) {
        const ind = document.createElement('div');
        ind.className = 'indicador-suma-valores';
        ind.style.cssText = 'display:none; margin:.75rem 0; font-weight:600;';
        const controles = vistaActiva.querySelector('.controles-tabla');
        controles.parentNode.insertBefore(ind, controles);
    }
    for (let i = 1; i <= totalUnidades; i++) {
        const th = document.createElement('th');
        th.className = 'col-dinamica';
        th.textContent = 'U' + i;
        th.dataset.vista = 'General';
        thead.insertBefore(th, finalThBase);
    }

    // ---- Alumnos inscritos ----
    tbody.innerHTML = '';
    const alumnosBD = (datosBD.exito && datosBD.alumnos) ? datosBD.alumnos : [];

    if (alumnosBD.length === 0) {
        const filaVacia = document.createElement('tr');
        filaVacia.innerHTML = `<td colspan="${totalUnidades + 3}" style="text-align:center; padding:1.5rem;">No hay alumnos inscritos en este grupo.</td>`;
        tbody.appendChild(filaVacia);
    }

    alumnosBD.forEach(alu => {
        const tr = document.createElement('tr');
        tr.className = 'fila-reprobado';
        tr.dataset.idAlumno = alu.no_de_control;

        const tdNombre = document.createElement('td');
        tdNombre.textContent = `${alu.apellidos} ${alu.nombres}`;
        tr.appendChild(tdNombre);

        // Promedio por unidad (solo lectura, se calcula con los entregables)
        for (let i = 1; i <= totalUnidades; i++) {
            const td = document.createElement('td');
            td.dataset.vista = 'General';
            td.dataset.promedioUnidad = 'Unidad ' + i;
            td.textContent = '-';
            tr.appendChild(td);
        }

        const tdFinal = document.createElement('td');
        tdFinal.innerHTML = '<strong>0</strong>';
        tr.appendChild(tdFinal);

        const tdAcc = document.createElement('td');
        tdAcc.innerHTML = '<button class="btn-detalles">Detalles</button>';
        tr.appendChild(tdAcc);

        tbody.appendChild(tr);
    });

    const filas = tbody.querySelectorAll('tr[data-id-alumno]');

    // ---- Agrupar entregables de la BD por unidad ----
    const entregablesPorUnidad = {};
    for (let i = 1; i <= totalUnidades; i++) entregablesPorUnidad['Unidad ' + i] = [];

    if (datosBD.exito && datosBD.entregables) {
        datosBD.entregables.forEach(ent => {
            if (entregablesPorUnidad[ent.unidad]) entregablesPorUnidad[ent.unidad].push(ent);
        });
    }

    // ---- Columnas (ocultas) de cada unidad ----
    for (let i = 1; i <= totalUnidades; i++) {
        const nombreVista = 'Unidad ' + i;
        const finalTh = thead.querySelector('th:nth-last-child(2)');
        const entsUnidad = entregablesPorUnidad[nombreVista];

        if (entsUnidad.length === 0) {
            crearColumnaBD(thead, filas, nombreVista, 'Entregables', null, [], finalTh, 0);
        } else {
            entsUnidad.forEach(ent => {
                crearColumnaBD(thead, filas, nombreVista, ent.nombre, ent.id_entregable, datosBD.calificaciones || [], finalTh, parseFloat(ent.valor) || 0);
            });
        }
    }

    // Unidades sin valores guardados (suma 0): repartir 100% en partes iguales
    for (let i = 1; i <= totalUnidades; i++) {
        const inputs = vistaActiva.querySelectorAll(`th.col-valor[data-vista="Unidad ${i}"] input`);
        const suma = Array.from(inputs).reduce((a, inp) => a + (parseFloat(inp.value) || 0), 0);
        if (inputs.length && suma === 0) {
            const base = Math.floor(10000 / inputs.length) / 100;
            inputs.forEach(inp => inp.value = base);
            inputs[inputs.length - 1].value = Math.round((100 - base * (inputs.length - 1)) * 100) / 100;
        }
    }

    // Recalcular al editar una nota o un valor
    vistaActiva.addEventListener('input', e => {
        if (e.target.matches('.celda-input')) recalcularFila(e.target.closest('tr'));
        if (e.target.matches('.input-valor')) {
            recalcularTodo(vistaActiva);
            actualizarIndicador(vistaActiva);
        }
    });
    recalcularTodo(vistaActiva);
    actualizarIndicador(vistaActiva);
}

// Promedio por unidad y FINAL (promedio de las unidades con calificación)
function recalcularFila(fila) {
    if (!fila || !fila.dataset.idAlumno) return;
    const promedios = [];

    fila.querySelectorAll('td[data-promedio-unidad]').forEach(td => {
        const unidad = td.dataset.promedioUnidad;
        const vistaActiva = fila.closest('.instancia-grupo-activo') || document;
        const pesos = Array.from(vistaActiva.querySelectorAll(`th.col-valor[data-vista="${unidad}"] input`))
            .map(inp => parseFloat(inp.value) || 0);
        const notas = Array.from(fila.querySelectorAll(`td[data-vista="${unidad}"] input`));

        // Calificación de la unidad = suma de (nota × valor / 100); nota vacía cuenta 0
        const hayNotas = notas.some(inp => inp.value !== '');
        if (hayNotas) {
            const prom = notas.reduce((acc, inp, k) => acc + (parseFloat(inp.value) || 0) * (pesos[k] || 0) / 100, 0);
            td.textContent = prom.toFixed(1).replace(/\.0$/, '');
            promedios.push(prom);
        } else {
            td.textContent = '-';
        }
    });

    const final = promedios.length ? promedios.reduce((a, b) => a + b, 0) / promedios.length : 0;
    const celdaFinal = fila.querySelector('td:nth-last-child(2) strong');
    if (celdaFinal) celdaFinal.textContent = Math.round(final);
    fila.classList.toggle('fila-aprobado', final >= 70);
    fila.classList.toggle('fila-reprobado', final < 70);
}

function recalcularTodo(vistaActiva) {
    vistaActiva.querySelectorAll('.tabla-excel tbody tr[data-id-alumno]').forEach(recalcularFila);
}

// Celda editable con el valor (%) de un entregable
function crearCeldaValor(nombreVista, valor) {
    const th = document.createElement('th');
    th.className = 'col-dinamica col-valor';
    th.dataset.vista = nombreVista;
    th.innerHTML = '<input type="number" class="input-valor" min="0" max="100" step="any" style="width:70px;" title="Valor del entregable (%)">';
    th.querySelector('input').value = valor;
    return th;
}

// Muestra la suma de valores de la unidad activa (debe ser 100%)
function actualizarIndicador(vistaActiva) {
    const ind = vistaActiva.querySelector('.indicador-suma-valores');
    const tab = vistaActiva.querySelector('.lista-unidades li.activo');
    if (!ind || !tab) return;
    const unidad = tab.dataset.unidad;
    if (unidad === 'General') { ind.style.display = 'none'; return; }

    const suma = Array.from(vistaActiva.querySelectorAll(`th.col-valor[data-vista="${unidad}"] input`))
        .reduce((a, inp) => a + (parseFloat(inp.value) || 0), 0);
    const redondeada = Math.round(suma * 100) / 100;
    const ok = Math.abs(redondeada - 100) <= 0.01;
    ind.style.display = 'block';
    ind.style.color = ok ? '#198754' : '#dc3545';
    ind.textContent = ok
        ? `✔ Suma de valores de ${unidad}: 100%`
        : `⚠ Suma de valores de ${unidad}: ${redondeada}% (${redondeada < 100 ? 'faltan ' + (Math.round((100 - redondeada) * 100) / 100) : 'sobran ' + (Math.round((redondeada - 100) * 100) / 100)}%, debe sumar 100%)`;
}

// Función auxiliar para generar las celdas y vaciarles el número correcto
function crearColumnaBD(thead, filas, nombreVista, nombreColumna, idEntregable, calificaciones, finalTh, valor) {
    const nuevoTh = document.createElement('th');
    nuevoTh.className = 'col-dinamica encabezado-editable';
    nuevoTh.contentEditable = 'true';
    nuevoTh.textContent = nombreColumna;
    nuevoTh.title = 'Clic derecho para eliminar';
    nuevoTh.dataset.vista = nombreVista;
    nuevoTh.style.display = 'none';
    
    thead.insertBefore(nuevoTh, finalTh);

    // Celda de valor (%) en la segunda fila del encabezado
    const filaValores = thead.parentNode.querySelector('.fila-valores');
    const celdaValor = crearCeldaValor(nombreVista, valor);
    celdaValor.style.display = 'none';
    filaValores.insertBefore(celdaValor, filaValores.querySelector('th:nth-last-child(2)'));

    filas.forEach(fila => {
        const finalTd = fila.querySelector('td:nth-last-child(2)');
        const nuevoTd = document.createElement('td');
        
        let valorNota = '';
        if (idEntregable) {
            const idAlumno = fila.dataset.idAlumno;
            // Buscar si el alumno tiene un número guardado para esta columna exacta
            const notaEncontrada = calificaciones.find(c => String(c.numero_control) === String(idAlumno) && String(c.id_entregable) === String(idEntregable));
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