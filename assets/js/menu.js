// ==========================================
// ESTADO GLOBAL DE LA APLICACIÓN (SIRCA)
// ==========================================
const memoriaGrupos = {}; 
let grupoActivo = null;
let parcialActivoGestion = '1';

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
        } else if (nombreVista === 'documentos') {
            // Inicializar vista de gestión y parcial si se abren
            setTimeout(() => {
                if (document.getElementById('subvista-gestion') && document.getElementById('subvista-gestion').style.display !== 'none') {
                    cargarDatosGestion(parcialActivoGestion);
                }
                if (document.getElementById('subvista-parcial') && document.getElementById('subvista-parcial').style.display !== 'none') {
                    const selGpo = document.getElementById('select-grupo-reporte-parcial');
                    cargarReporteParcial(selGpo ? selGpo.value : '085');
                }
            }, 50);
        }
        return; 
    }

    // ---------------------------------------------------------
    // 2. ABRIR LA TABLA DE UN GRUPO (LEER DESDE BD)
    // ---------------------------------------------------------
    if (e.target.matches('.boton-seleccionar-grupo') || e.target.closest('.boton-seleccionar-grupo')) {
        const btn = e.target.closest('.boton-seleccionar-grupo');
        const filaGrupo = btn.closest('tr');
        grupoActivo = btn.dataset.grupo || filaGrupo.querySelector('th').textContent.trim(); 
        
        const contenedorPrincipal = document.getElementById('contenido-principal');
        const tablaGrupos = document.querySelector('.tabla-grupos-contenedor');
        if (tablaGrupos) tablaGrupos.style.display = 'none';

        if (!memoriaGrupos[grupoActivo]) {
            // Mostrar mensaje de carga mientras PHP busca los datos
            contenedorPrincipal.innerHTML = '<h3 style="padding: 3rem; text-align: center; color: #1b365d;">Cargando calificaciones del Grupo ' + grupoActivo + '...</h3>';

            fetch(`../../controllers/obtener_calificaciones.php?grupo=${encodeURIComponent(grupoActivo)}`)
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
                        const nomMateria = (datosBD.grupo && datosBD.grupo.nombre_materia) ? datosBD.grupo.nombre_materia : '';
                        tituloTabla.innerHTML = `Grupo ${grupoActivo} - ${nomMateria} - <span class="titulo-vista-actual">General</span>`;
                    }

                    const subtitulo = wrapper.querySelector('#subtitulo-grupo-info');
                    if (subtitulo && datosBD.grupo) {
                        subtitulo.textContent = `Periodo: ${datosBD.grupo.periodo || 'AGO-DIC/2026'} | Aula: ${datosBD.grupo.aula || 'N/A'} | Alumnos: ${datosBD.alumnos ? datosBD.alumnos.length : 0}`;
                    }

                    // Enlaces de descarga específicos para este grupo
                    const btnExcel = wrapper.querySelector('#btn-descargar-excel-grupo');
                    if (btnExcel) {
                        btnExcel.onclick = () => window.location.href = `../../controllers/exportar_reporte.php?tipo=calificaciones_parciales&grupo=${encodeURIComponent(grupoActivo)}`;
                    }

                    const btnExcelPie = wrapper.querySelector('#btn-exportar-grupo-pie');
                    if (btnExcelPie) {
                        btnExcelPie.onclick = () => window.location.href = `../../controllers/exportar_reporte.php?tipo=calificaciones_parciales&grupo=${encodeURIComponent(grupoActivo)}`;
                    }

                    const btnPrint = wrapper.querySelector('#btn-imprimir-grupo');
                    if (btnPrint) {
                        btnPrint.onclick = () => abrirModalReporteParcial(grupoActivo);
                    }
                    
                    // Reconstruimos la tabla con los datos reales
                    construirTablaConDatos(wrapper, datosBD);
                    
                    contenedorPrincipal.appendChild(wrapper);
                    memoriaGrupos[grupoActivo] = wrapper; 
                }
            })
            .catch(error => {
                console.error("Error al cargar la Base de Datos:", error);
                alert("Hubo un problema al cargar los datos del grupo.");
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
        if (grupoActivo && memoriaGrupos[grupoActivo]) {
            memoriaGrupos[grupoActivo].style.display = 'none';
        }
        
        grupoActivo = null;
        
        const contenedorPrincipal = document.getElementById('contenido-principal');
        const tablaGrupos = contenedorPrincipal.querySelector('.tabla-grupos-contenedor');
        
        if (tablaGrupos) {
            tablaGrupos.style.display = 'block';
        } else {
            contenedorPrincipal.innerHTML = '';
            const templateGrupos = document.getElementById('vista-grupos');
            if (templateGrupos) {
                contenedorPrincipal.appendChild(templateGrupos.content.cloneNode(true));
            }
        }
        
        document.getElementById('titulo-seccion').textContent = 'Mis Grupos';
        document.querySelectorAll('.menu-opcion').forEach(btn => btn.classList.remove('activo'));
        const btnGruposMenu = document.querySelector('.menu-opcion[data-vista="grupos"]');
        if (btnGruposMenu) btnGruposMenu.classList.add('activo');

        return;
    }

    // ---------------------------------------------------------
    // 4. ABRIR REPORTE PARCIAL OFICIAL EN MODAL
    // ---------------------------------------------------------
    if (e.target.matches('.btn-abrir-reporte-parcial') || e.target.closest('.btn-abrir-reporte-parcial')) {
        const btn = e.target.closest('.btn-abrir-reporte-parcial');
        const cveGrupo = btn.dataset.grupo || grupoActivo || '085';
        abrirModalReporteParcial(cveGrupo);
        return;
    }

    if (e.target.matches('.btn-abrir-reporte-modal') || e.target.closest('.btn-abrir-reporte-modal')) {
        abrirModalReporteParcial(grupoActivo || '085');
        return;
    }

    // Cerrar modal de reporte
    if (e.target.matches('#btn-cerrar-modal-rep') || e.target.matches('.modal-reporte-overlay')) {
        cerrarModalReporte();
        return;
    }

    // ---------------------------------------------------------
    // 5. BOTONES DE PARCIALES EN GESTIÓN DEL CURSO (1, 2, 3, 4, F)
    // ---------------------------------------------------------
    if (e.target.matches('.btn-pestana-periodo')) {
        document.querySelectorAll('.btn-pestana-periodo').forEach(b => b.classList.remove('activo'));
        e.target.classList.add('activo');
        parcialActivoGestion = e.target.dataset.parcial;
        const lbl = document.getElementById('label-parcial-activo');
        if (lbl) lbl.textContent = parcialActivoGestion;

        const enlaceExcel = document.getElementById('enlace-descarga-gestion-excel');
        if (enlaceExcel) enlaceExcel.href = `../../controllers/exportar_reporte.php?tipo=gestion_curso&parcial=${parcialActivoGestion}`;

        cargarDatosGestion(parcialActivoGestion);
        return;
    }

    // Guardar cambios del reporte de gestión en BD
    if (e.target.matches('#btn-guardar-gestion-db')) {
        guardarGestionEnBD();
        return;
    }

    // =========================================================================
    // RESTRICCIÓN: CALIFICADOR ACTIVO
    // =========================================================================
    if (!grupoActivo || !memoriaGrupos[grupoActivo]) return;
    
    const vistaActiva = memoriaGrupos[grupoActivo];
    if (!vistaActiva.contains(e.target)) return;

    // ---------------------------------------------------------
    // 6. NAVEGACIÓN POR UNIDADES EN EL CALIFICADOR
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
    // 7. AÑADIR NUEVA COLUMNA (ENTREGABLE)
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
        nuevoTh.textContent = 'Nuevo Entregable';
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
    // 8. GUARDAR CALIFICACIONES (ENVIAR A PHP)
    // ---------------------------------------------------------
    if (e.target.matches('#btn-guardar-calificaciones') || e.target.matches('#btn-guardar-calificaciones-pie')) {
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
            if (valores.length > 0 && Math.abs(sumaValores - 100) > 0.05) {
                errores.push(`${nombreUnidad}: suma ${sumaValores}% (debe sumar 100%)`);
            }

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
        btnGuardar.textContent = 'Guardando...';
        btnGuardar.disabled = true;

        fetch('../../controllers/guardar_calificaciones.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(respuesta => respuesta.json())
        .then(datos => {
            if (datos.exito) {
                alert('¡Calificaciones guardadas exitosamente y sincronizadas con los reportes oficiales!');
            } else {
                alert('Error al guardar: ' + datos.mensaje);
            }
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

}); // CIERRE DEL EVENTO DE CLIC

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
// PESTAÑAS EN DOCUMENTOS OFICIALES
// ==========================================
function cambiarPestanaDocumento(tipo) {
    document.querySelectorAll('.subvista-documento').forEach(el => el.style.display = 'none');
    document.querySelectorAll('[id^="tab-doc-"]').forEach(btn => {
        btn.classList.remove('btn-primario');
        btn.classList.add('btn-secundario');
    });

    const sub = document.getElementById('subvista-' + tipo);
    const tab = document.getElementById('tab-doc-' + tipo);
    if (sub) sub.style.display = 'block';
    if (tab) {
        tab.classList.remove('btn-secundario');
        tab.classList.add('btn-primario');
    }

    if (tipo === 'gestion') {
        cargarDatosGestion(parcialActivoGestion);
    } else if (tipo === 'parcial') {
        const sel = document.getElementById('select-grupo-reporte-parcial');
        cargarReporteParcial(sel ? sel.value : '085');
    }
}

// Selector de grupo en reporte de calificaciones parciales
document.addEventListener('change', function(e) {
    if (e.target.matches('#select-grupo-reporte-parcial')) {
        cargarReporteParcial(e.target.value);
        const linkExcel = document.getElementById('enlace-descarga-parcial-excel');
        if (linkExcel) linkExcel.href = `../../controllers/exportar_reporte.php?tipo=calificaciones_parciales&grupo=${encodeURIComponent(e.target.value)}`;
    }
});

// ==========================================
// CARGAR DATOS DE GESTIÓN DEL CURSO (IMAGEN 4)
// ==========================================
function cargarDatosGestion(parcial) {
    const cuerpo = document.getElementById('cuerpo-tabla-gestion');
    if (!cuerpo) return;

    cuerpo.innerHTML = '<tr><td colspan="10" style="text-align:center; padding:1.5rem;">Cargando reporte de gestión del curso...</td></tr>';

    fetch(`../../controllers/obtener_gestion_curso.php?parcial=${encodeURIComponent(parcial)}`)
    .then(r => r.json())
    .then(data => {
        if (!data.exito || !data.filas) {
            cuerpo.innerHTML = '<tr><td colspan="10" style="text-align:center; color:red;">No se pudieron cargar los datos de gestión.</td></tr>';
            return;
        }

        cuerpo.innerHTML = '';
        const filas = data.filas;

        filas.forEach((f, idx) => {
            const tr = document.createElement('tr');
            tr.dataset.idGrupo = f.id_grupo;

            tr.innerHTML = `
                <td><strong>${f.asignatura}</strong></td>
                <td align="center">${f.unidad_semestre}</td>
                <td align="center"><span style="background:#e2e8f0; padding:2px 5px; border-radius:3px; font-weight:bold;">${f.carrera}</span></td>
                <td align="center" class="celda-azul">
                    <input type="number" class="input-tabla-gestion input-a" min="0" value="${f.total_alumnos}">
                </td>
                <td align="center" class="celda-azul">
                    <input type="number" class="input-tabla-gestion input-b" min="0" value="${f.aprobados_ordinario}">
                </td>
                <td align="center" class="celda-amarillo col-c font-bold" style="font-weight:bold;">0%</td>
                <td align="center" class="celda-amarillo col-d">0</td>
                <td align="center" class="celda-naranja col-e font-bold" style="font-weight:bold;">0%</td>
                <td align="center" class="celda-azul">
                    <input type="number" class="input-tabla-gestion input-f" min="0" value="${f.desertores}">
                </td>
                <td align="center" class="celda-naranja col-g font-bold" style="font-weight:bold;">0%</td>
            `;

            cuerpo.appendChild(tr);
        });

        // Recalcular fórmulas
        recalcularGestionTotales();

        // Escuchar cambios en los inputs para recalcular en tiempo real
        cuerpo.querySelectorAll('.input-tabla-gestion').forEach(inp => {
            inp.addEventListener('input', recalcularGestionTotales);
        });
    })
    .catch(err => {
        console.error("Error al cargar gestión:", err);
        cuerpo.innerHTML = '<tr><td colspan="10" style="text-align:center; color:red;">Error de conexión al cargar datos de gestión.</td></tr>';
    });
}

// Recalcular fórmulas en tiempo real para Reporte de Gestión
function recalcularGestionTotales() {
    const cuerpo = document.getElementById('cuerpo-tabla-gestion');
    if (!cuerpo) return;

    const filas = cuerpo.querySelectorAll('tr');
    let sumA = 0, sumB = 0, sumD = 0, sumF = 0;

    filas.forEach(tr => {
        const inpA = tr.querySelector('.input-a');
        const inpB = tr.querySelector('.input-b');
        const inpF = tr.querySelector('.input-f');

        if (!inpA || !inpB || !inpF) return;

        const a = parseInt(inpA.value) || 0;
        const b = parseInt(inpB.value) || 0;
        const f = parseInt(inpF.value) || 0;

        // D = NO. DE ALUMNOS QUE NO ALCANZARON LAS COMPETENCIAS (A - B - F o según fórmula)
        const d = Math.max(0, a - b - f);

        // C = % B / A
        const c = (a > 0) ? Math.round((b / a) * 100) : 0;
        // E = % D / A
        const e = (a > 0) ? Math.round((d / a) * 100) : 0;
        // G = % F / A
        const g = (a > 0) ? Math.round((f / a) * 100) : 0;

        tr.querySelector('.col-c').textContent = c + '%';
        tr.querySelector('.col-d').textContent = d;
        tr.querySelector('.col-e').textContent = e + '%';
        tr.querySelector('.col-g').textContent = g + '%';

        sumA += a;
        sumB += b;
        sumD += d;
        sumF += f;
    });

    const totC = (sumA > 0) ? Math.round((sumB / sumA) * 100) : 0;
    const totE = (sumA > 0) ? Math.round((sumD / sumA) * 100) : 0;
    const totG = (sumA > 0) ? Math.round((sumF / sumA) * 100) : 0;

    const elA = document.getElementById('tot-a');
    const elB = document.getElementById('tot-b');
    const elC = document.getElementById('tot-c');
    const elD = document.getElementById('tot-d');
    const elE = document.getElementById('tot-e');
    const elF = document.getElementById('tot-f');
    const elG = document.getElementById('tot-g');

    if (elA) elA.textContent = sumA;
    if (elB) elB.textContent = sumB;
    if (elC) elC.textContent = totC + '%';
    if (elD) elD.textContent = sumD;
    if (elE) elE.textContent = totE + '%';
    if (elF) elF.textContent = sumF;
    if (elG) elG.textContent = totG + '%';
}

// Guardar los datos de Gestión en la base de datos
function guardarGestionEnBD() {
    const cuerpo = document.getElementById('cuerpo-tabla-gestion');
    if (!cuerpo) return;

    const filas = [];
    cuerpo.querySelectorAll('tr').forEach(tr => {
        const idG = tr.dataset.idGrupo;
        const tds = tr.querySelectorAll('td');
        const asig = tds[0].textContent.trim();
        const unid = tds[1].textContent.trim();
        const carr = tds[2].textContent.trim();

        const a = parseInt(tr.querySelector('.input-a').value) || 0;
        const b = parseInt(tr.querySelector('.input-b').value) || 0;
        const d = parseInt(tr.querySelector('.col-d').textContent) || 0;
        const f = parseInt(tr.querySelector('.input-f').value) || 0;

        filas.push({
            id_grupo: idG,
            asignatura: asig,
            unidad_semestre: unid,
            carrera: carr,
            total_alumnos: a,
            aprobados_ordinario: b,
            reprobados_ordinario: d,
            desertores: f
        });
    });

    const payload = {
        parcial: parcialActivoGestion,
        periodo: 'Agosto - diciembre 2026',
        filas: filas
    };

    const btn = document.getElementById('btn-guardar-gestion-db');
    const origText = btn ? btn.textContent : '';
    if (btn) {
        btn.textContent = 'Guardando en BD...';
        btn.disabled = true;
    }

    fetch('../../controllers/guardar_gestion_curso.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(res => {
        if (res.exito) {
            alert('¡Reporte de Gestión del Curso guardado exitosamente en la base de datos!');
        } else {
            alert('Error al guardar: ' + res.mensaje);
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error de conexión al guardar reporte.');
    })
    .finally(() => {
        if (btn) {
            btn.textContent = origText;
            btn.disabled = false;
        }
    });
}

// ==========================================
// CARGAR REPORTE DE CALIFICACIONES PARCIALES (IMAGEN 3)
// ==========================================
function cargarReporteParcial(grupo) {
    const cuerpo = document.getElementById('cuerpo-tabla-rep-parcial');
    if (!cuerpo) return;

    cuerpo.innerHTML = '<tr><td colspan="13" style="text-align:center; padding:1.5rem;">Cargando lista de calificaciones...</td></tr>';

    fetch(`../../controllers/obtener_reporte_parcial.php?grupo=${encodeURIComponent(grupo)}`)
    .then(r => r.json())
    .then(data => {
        if (!data.exito || !data.info) {
            cuerpo.innerHTML = '<tr><td colspan="13" style="text-align:center; color:red;">No se pudo cargar el reporte del grupo.</td></tr>';
            return;
        }

        // Actualizar metadatos
        const info = data.info;
        const elMat = document.getElementById('rep-parcial-materia');
        const elMaestro = document.getElementById('rep-parcial-maestro');
        const elGrupo = document.getElementById('rep-parcial-grupo');
        const elPer = document.getElementById('rep-parcial-periodo');
        const elDepto = document.getElementById('rep-parcial-depto');

        if (elMat) elMat.textContent = info.materia;
        if (elMaestro) elMaestro.textContent = info.maestro;
        if (elGrupo) elGrupo.textContent = info.grupo;
        if (elPer) elPer.textContent = info.periodo;
        if (elDepto) elDepto.textContent = info.departamento;

        // Cargar filas de alumnos
        cuerpo.innerHTML = '';
        const alumnos = data.alumnos || [];

        if (alumnos.length === 0) {
            cuerpo.innerHTML = '<tr><td colspan="13" style="text-align:center;">No hay alumnos inscritos en este grupo.</td></tr>';
            return;
        }

        alumnos.forEach(alu => {
            const tr = document.createElement('tr');
            let uTds = '';
            for (let u = 1; u <= 8; u++) {
                const n = (alu.unidades && alu.unidades[u] !== null && alu.unidades[u] !== undefined) ? alu.unidades[u] : '';
                uTds += `<td class="celda-calif">${n}</td>`;
            }

            tr.innerHTML = `
                <td class="celda-num">${alu.num}</td>
                <td class="celda-ctrl"><strong>${alu.no_control}</strong></td>
                <td>${alu.nombre}</td>
                ${uTds}
                <td align="center">${alu.comp || ''}</td>
                <td align="center"><strong>${alu.promedio || ''}</strong></td>
            `;

            cuerpo.appendChild(tr);
        });

        // Índices de reprobación en el pie
        const ind = data.indices_reprobacion || {};
        for (let u = 1; u <= 8; u++) {
            const elU = document.getElementById(`rep-u${u}`);
            if (elU) elU.textContent = ind[u] || '0%';
        }
    })
    .catch(err => {
        console.error("Error al cargar reporte parcial:", err);
        cuerpo.innerHTML = '<tr><td colspan="13" style="text-align:center; color:red;">Error de conexión con el servidor.</td></tr>';
    });
}

// Modal para ver Reporte Parcial de cualquier grupo
function abrirModalReporteParcial(grupo) {
    const modal = document.getElementById('modal-reporte-global');
    const cuerpoModal = document.getElementById('modal-reporte-cuerpo');
    const tituloModal = document.getElementById('modal-reporte-titulo');

    if (!modal || !cuerpoModal) return;

    tituloModal.textContent = `Reporte Oficial de Calificaciones Parciales - Grupo ${grupo}`;
    cuerpoModal.innerHTML = '<div style="padding: 2rem; text-align: center; color: #1b365d;">Cargando documento oficial...</div>';
    modal.style.display = 'flex';

    fetch(`../../controllers/obtener_reporte_parcial.php?grupo=${encodeURIComponent(grupo)}`)
    .then(r => r.json())
    .then(data => {
        if (!data.exito) {
            cuerpoModal.innerHTML = `<div style="color:red; padding:2rem; text-align:center;">Error: ${data.mensaje}</div>`;
            return;
        }

        const info = data.info;
        const alumnos = data.alumnos || [];
        const ind = data.indices_reprobacion || {};

        let filasHtml = '';
        alumnos.forEach(alu => {
            let uTds = '';
            for (let u = 1; u <= 8; u++) {
                const n = (alu.unidades && alu.unidades[u] !== null && alu.unidades[u] !== undefined) ? alu.unidades[u] : '';
                uTds += `<td align="center">${n}</td>`;
            }
            filasHtml += `
                <tr>
                    <td align="center">${alu.num}</td>
                    <td align="center"><strong>${alu.no_control}</strong></td>
                    <td>${alu.nombre}</td>
                    ${uTds}
                    <td></td>
                    <td align="center"><strong>${alu.promedio || ''}</strong></td>
                </tr>
            `;
        });

        cuerpoModal.innerHTML = `
            <div style="display: flex; justify-content: flex-end; gap: 8px; margin-bottom: 15px;">
                <a href="../../controllers/exportar_reporte.php?tipo=calificaciones_parciales&grupo=${encodeURIComponent(grupo)}" class="btn-accion-reporte btn-excel">
                    Descargar Excel
                </a>
                <button class="btn-accion-reporte btn-pdf btn-imprimir-reporte" type="button" onclick="window.print();">
                    Imprimir / Guardar PDF
                </button>
            </div>

            <div class="documento-hoja" style="padding: 15px;">
                <div class="encabezado-institucional">
                    <img src="../../assets/img/itesp.jpg" alt="ITESP" style="height: 50px;">
                    <div class="titulos-centro">
                        <h2 style="font-size: 14px;">INSTITUTO TECNOLÓGICO SUPERIOR DE PURUÁNDIRO</h2>
                        <h4 style="font-size: 12px;">${info.departamento}</h4>
                        <div style="font-size: 13px; font-weight: bold; color: #1b365d; margin-top: 3px;">REPORTE DE CALIFICACIONES PARCIALES</div>
                    </div>
                    <img src="../../assets/img/tecnm.png" alt="TecNM" style="height: 50px;">
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 6px; font-size: 11.5px; margin-bottom: 10px; border-bottom: 1px solid #cbd5e0; padding-bottom: 6px;">
                    <div>
                        <div><strong>MATERIA:</strong> ${info.materia}</div>
                        <div><strong>MAESTRO:</strong> ${info.maestro}</div>
                    </div>
                    <div>
                        <div><strong>GRUPO:</strong> ${info.grupo}</div>
                        <div><strong>PERIODO:</strong> ${info.periodo}</div>
                    </div>
                </div>

                <table class="tabla-calificaciones-parciales">
                    <thead>
                        <tr>
                            <th rowspan="2">No</th>
                            <th rowspan="2">No. CONTROL</th>
                            <th rowspan="2">NOMBRE DEL ALUMNO</th>
                            <th colspan="8">EVALUACIÓN DE UNIDADES</th>
                            <th rowspan="2">COMP.</th>
                            <th rowspan="2">Prom.</th>
                        </tr>
                        <tr>
                            <th>I</th><th>II</th><th>III</th><th>IV</th><th>V</th><th>VI</th><th>VII</th><th>VIII</th>
                        </tr>
                    </thead>
                    <tbody>${filasHtml}</tbody>
                    <tfoot>
                        <tr class="fila-indices-reprobacion">
                            <td colspan="3" align="right"><strong>Índices de reprobación</strong></td>
                            <td align="center">${ind[1] || '0%'}</td>
                            <td align="center">${ind[2] || '0%'}</td>
                            <td align="center">${ind[3] || '0%'}</td>
                            <td align="center">${ind[4] || '0%'}</td>
                            <td align="center">${ind[5] || '0%'}</td>
                            <td align="center">${ind[6] || '0%'}</td>
                            <td align="center">${ind[7] || '0%'}</td>
                            <td align="center">${ind[8] || '0%'}</td>
                            <td>0%</td>
                            <td>0</td>
                        </tr>
                    </tfoot>
                </table>

                <div class="seccion-firmas" style="margin-top: 25px;">
                    <div class="caja-firma">
                        <div class="linea-firma"></div>
                        <div class="nombre-firma">${info.maestro}</div>
                        <div class="cargo-firma">Firma del Docente</div>
                    </div>
                    <div class="caja-firma">
                        <div class="linea-firma"></div>
                        <div class="nombre-firma">MAE. Damaris Haydee Castillo Vega</div>
                        <div class="cargo-firma">Vo. Bo. División de Carrera</div>
                    </div>
                </div>
            </div>
        `;
    })
    .catch(err => {
        cuerpoModal.innerHTML = `<div style="color:red; padding:2rem; text-align:center;">Error de conexión al cargar reporte.</div>`;
    });
}

function cerrarModalReporte() {
    const modal = document.getElementById('modal-reporte-global');
    if (modal) modal.style.display = 'none';
}

// ==========================================
// FUNCIÓN PARA CONSTRUIR TABLA DESDE BASE DE DATOS
// ==========================================
function construirTablaConDatos(vistaActiva, datosBD) {
    const thead = vistaActiva.querySelector('.tabla-excel thead tr');
    const tbody = vistaActiva.querySelector('.tabla-excel tbody');

    let totalUnidades = parseInt(datosBD.total_unidades, 10);
    if (isNaN(totalUnidades)) totalUnidades = 4;
    totalUnidades = Math.min(7, Math.max(1, totalUnidades));
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

    // ---- Encabezado de la vista General ----
    thead.querySelectorAll('th.col-dinamica').forEach(th => th.remove());
    const finalThBase = thead.querySelector('th:nth-last-child(2)'); // FINAL

    // Fila de valores (%)
    const filaValoresExistente = vistaActiva.querySelector('.tabla-excel thead .fila-valores');
    if (filaValoresExistente) filaValoresExistente.remove();

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

    // Indicador de suma de valores
    if (!vistaActiva.querySelector('.indicador-suma-valores')) {
        const ind = document.createElement('div');
        ind.className = 'indicador-suma-valores';
        ind.style.cssText = 'display:none; margin:.75rem 0; font-weight:600; padding:6px 12px; border-radius:4px;';
        const controles = vistaActiva.querySelector('.controles-tabla');
        if (controles) controles.parentNode.insertBefore(ind, controles);
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
        tdNombre.innerHTML = `<strong>${alu.apellidos}</strong> ${alu.nombres} <small style="display:block; color:#718096; font-size:11px;">NC: ${alu.no_de_control}</small>`;
        tr.appendChild(tdNombre);

        // Promedio por unidad
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
        tdAcc.innerHTML = '<button class="btn-detalles" type="button">Detalles</button>';
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

    // ---- Columnas de cada unidad ----
    for (let i = 1; i <= totalUnidades; i++) {
        const nombreVista = 'Unidad ' + i;
        const finalTh = thead.querySelector('th:nth-last-child(2)');
        const entsUnidad = entregablesPorUnidad[nombreVista];

        if (entsUnidad.length === 0) {
            crearColumnaBD(thead, filas, nombreVista, 'Entregables', null, [], finalTh, 100);
        } else {
            entsUnidad.forEach(ent => {
                crearColumnaBD(thead, filas, nombreVista, ent.nombre, ent.id_entregable, datosBD.calificaciones || [], finalTh, parseFloat(ent.valor) || 0);
            });
        }
    }

    // Unidades sin valores guardados (suma 0): repartir 100%
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

// Promedio por unidad y FINAL
function recalcularFila(fila) {
    if (!fila || !fila.dataset.idAlumno) return;
    const promedios = [];

    fila.querySelectorAll('td[data-promedio-unidad]').forEach(td => {
        const unidad = td.dataset.promedioUnidad;
        const vistaActiva = fila.closest('.instancia-grupo-activo') || document;
        const pesos = Array.from(vistaActiva.querySelectorAll(`th.col-valor[data-vista="${unidad}"] input`))
            .map(inp => parseFloat(inp.value) || 0);
        const notas = Array.from(fila.querySelectorAll(`td[data-vista="${unidad}"] input`));

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

function crearCeldaValor(nombreVista, valor) {
    const th = document.createElement('th');
    th.className = 'col-dinamica col-valor';
    th.dataset.vista = nombreVista;
    th.innerHTML = '<input type="number" class="input-valor" min="0" max="100" step="any" style="width:70px;" title="Valor del entregable (%)">';
    th.querySelector('input').value = valor;
    return th;
}

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
    ind.style.background = ok ? '#d4edda' : '#f8d7da';
    ind.style.color = ok ? '#155724' : '#721c24';
    ind.style.border = ok ? '1px solid #c3e6cb' : '1px solid #f5c6cb';
    ind.textContent = ok
        ? `Suma de valores de ${unidad}: 100%`
        : `Suma de valores de ${unidad}: ${redondeada}% (${redondeada < 100 ? 'faltan ' + (Math.round((100 - redondeada) * 100) / 100) : 'sobran ' + (Math.round((redondeada - 100) * 100) / 100)}%, debe sumar 100%)`;
}

function crearColumnaBD(thead, filas, nombreVista, nombreColumna, idEntregable, calificaciones, finalTh, valor) {
    const nuevoTh = document.createElement('th');
    nuevoTh.className = 'col-dinamica encabezado-editable';
    nuevoTh.contentEditable = 'true';
    nuevoTh.textContent = nombreColumna;
    nuevoTh.title = 'Clic derecho para eliminar';
    nuevoTh.dataset.vista = nombreVista;
    nuevoTh.style.display = 'none';
    
    thead.insertBefore(nuevoTh, finalTh);

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
