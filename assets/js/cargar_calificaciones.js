document.addEventListener('DOMContentLoaded', () => {
    // Asegúrate de que este idGrupo sea dinámico o coincida con el grupo actual (85)
    const idGrupo = 85; 

    // OJO CON ESTA RUTA: Si tu archivo principal se ejecuta desde la raíz o controladores, 
    // ajusta la ruta del fetch para que llegue bien al controlador.
    const urlControlador = `/Administrador-de-calificaciones/controllers/unidades_controller.php?id_grupo=${idGrupo}`;

    fetch(urlControlador)
        .then(response => {
            if (!response.ok) {
                throw new Error("Respuesta de red fallida");
            }
            return response.json();
        })
        .then(data => {
            console.log("Datos recibidos del servidor:", data); // Esto saldrá en F12 -> Consola

            if(data.estado === "exito") {
                construirInterfaz(data.total_unidades, data.alumnos, data.calificaciones);
            } else {
                console.error("El servidor indicó un error:", data.mensaje);
            }
        })
        .catch(error => console.error("Error al conectar con el controlador:", error));
});

function construirInterfaz(totalUnidades, alumnos, calificaciones) {
    const listaUnidadesHTML = document.getElementById('lista-unidades-dinamica');
    const cabeceraTabla = document.getElementById('cabecera-tabla');
    const cuerpoTabla = document.getElementById('cuerpo-tabla');

    // Validación por si los elementos no existen en el HTML
    if (!listaUnidadesHTML || !cabeceraTabla || !cuerpoTabla) {
        console.error("No se encontraron algunos elementos clave en el DOM (IDs: lista-unidades-dinamica, cabecera-tabla o cuerpo-tabla).");
        return;
    }

    // Limpiar contenedores por si acaso se ejecuta dos veces
    listaUnidadesHTML.innerHTML = '';
    
    // Remover columnas dinámicas previas si las hubiera para evitar duplicados
    const thDinamicos = cabeceraTabla.querySelectorAll('.col-dinamica');
    thDinamicos.forEach(th => th.remove());

    cuerpoTabla.innerHTML = '';

    // 1. Dibujar el menú lateral con las unidades
    for (let i = 1; i <= totalUnidades; i++) {
        const li = document.createElement('li');
        li.setAttribute('data-unidad', `Unidad ${i}`);
        li.textContent = `Unidad ${i}`;
        listaUnidadesHTML.appendChild(li);
    }

    // 2. Dibujar las columnas de la tabla (U1, U2...) antes de la columna "FINAL"
    const thFinal = cabeceraTabla.querySelector('th:nth-last-child(2)'); 
    for (let i = 1; i <= totalUnidades; i++) {
        const th = document.createElement('th');
        th.className = 'col-dinamica';
        th.textContent = `U${i}`;
        cabeceraTabla.insertBefore(th, thFinal);
    }

    // 3. Dibujar a los alumnos
    if (!alumnos || alumnos.length === 0) {
        cuerpoTabla.innerHTML = `<tr><td colspan="${3 + totalUnidades}" style="text-align:center;">No hay alumnos inscritos.</td></tr>`;
        return;
    }

    alumnos.forEach(alumno => {
        let sumaCalif = 0;
        let unidadesEvaluadas = 0;
        const noControl = alumno.no_de_control;
        const tr = document.createElement('tr');
        tr.className = 'fila-alumno';
        tr.setAttribute('data-id-alumno', noControl);

        let htmlFila = `<td>${alumno.apellidos} ${alumno.nombres}</td>`;

        for (let u = 1; u <= totalUnidades; u++) {
            // Evaluamos de forma segura si calificaciones contiene datos para este alumno y unidad
            const calif = (calificaciones && calificaciones[noControl] && calificaciones[noControl][u] !== undefined) 
                          ? calificaciones[noControl][u] 
                          : '';

            if (calif !== '' && calif !== null) {
                sumaCalif += Number(calif);
                unidadesEvaluadas++;
            }

            htmlFila += `
                <td>
                    <input type="number" class="celda-input" value="${calif}" data-id-unidad="${u}">
                </td>`;
        }

        const promedio = (totalUnidades > 0 && unidadesEvaluadas > 0) 
                         ? Math.round(sumaCalif / totalUnidades) 
                         : 0;

        htmlFila += `
            <td><strong>${promedio}</strong></td>
            <td><button class="btn-detalles">Detalles</button></td>
        `;

        tr.innerHTML = htmlFila;
        cuerpoTabla.appendChild(tr);
    });
}