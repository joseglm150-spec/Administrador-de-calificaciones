const menuOpciones = document.querySelectorAll('.menu-opcion[data-vista]');
const contenidoPrincipal = document.getElementById('contenido-principal');
const tituloSeccion = document.getElementById('titulo-seccion');

if (!contenidoPrincipal || !tituloSeccion) {
    throw new Error('No se encontró el contenedor del menú o su título.');
}

const contenidoInicio = Array.from(contenidoPrincipal.childNodes, (nodo) => nodo.cloneNode(true));

const inicializarSeleccionGrupos = () => {
    const casillasGrupos = contenidoPrincipal.querySelectorAll('input[name="grupos[]"]');
    const conteoGrupos = contenidoPrincipal.querySelector('[data-conteo-grupos]');

    if (!conteoGrupos || casillasGrupos.length === 0) {
        return;
    }

    const actualizarConteo = () => {
        const cantidad = contenidoPrincipal.querySelectorAll('input[name="grupos[]"]:checked').length;
        conteoGrupos.textContent = `${cantidad} ${cantidad === 1 ? 'grupo seleccionado' : 'grupos seleccionados'}`;
    };

    casillasGrupos.forEach((casilla) => {
        casilla.addEventListener('change', actualizarConteo);
    });
};

const mostrarVista = (opcion, actualizarTitulo = true) => {
    if (opcion.dataset.vista === 'inicio') {
        contenidoPrincipal.replaceChildren(...contenidoInicio.map((nodo) => nodo.cloneNode(true)));
    } else {
        const vista = document.getElementById(`vista-${opcion.dataset.vista}`);

        if (!vista) {
            throw new Error(`No se encontró la vista "${opcion.dataset.vista}".`);
        }

        contenidoPrincipal.replaceChildren(vista.content.cloneNode(true));
    }

    inicializarSeleccionGrupos();
    contenidoPrincipal.scrollTop = 0;
    if (actualizarTitulo) {
        tituloSeccion.textContent = opcion.dataset.titulo;
    }

    menuOpciones.forEach((otraOpcion) => {
        otraOpcion.classList.remove('activo');
        otraOpcion.removeAttribute('aria-current');
    });

    opcion.classList.add('activo');
    opcion.setAttribute('aria-current', 'page');
};

menuOpciones.forEach((opcion) => {
    opcion.addEventListener('click', () => mostrarVista(opcion));
});

const opcionInicial = Array.from(menuOpciones).find((opcion) => opcion.dataset.vista === 'grupos');

if (!opcionInicial) {
    throw new Error('No se encontró la opción de menú "Grupos".');
}

mostrarVista(opcionInicial, false);

// =========================================================
// SOLUCIÓN: Un solo bloque de eventos delegados con Alertas
// =========================================================
document.addEventListener('click', function(e) {
    
    // 1. Clic en "Seleccionar" grupo
    const btnSeleccionar = e.target.closest('.boton-seleccionar-grupo');
    
    if (btnSeleccionar) {
        const fila = btnSeleccionar.closest('tr');
        const celdaGrupo = fila.querySelector('th[scope="row"]');
        
        // Failsafe: Si no encuentra el número
        if (!celdaGrupo) {
            alert("Error JS: No se detectó el número de grupo en la tabla.");
            return;
        }
        
        const nombreGrupo = celdaGrupo.textContent;
        const templateCalificaciones = document.getElementById('vista-calificaciones-grupo');
        
        // Failsafe crítico: Si no guardaste el PHP o falta la plantilla
        if (!templateCalificaciones) {
            alert("⚠️ ERROR: El navegador no encuentra el <template id='vista-calificaciones-grupo'>.\n\nAsegúrate de haber pegado el código HTML en tu index.php y de presionar 'Guardar'.");
            return;
        }
        
        // Cambio de vista
        tituloSeccion.textContent = `Calificaciones - Grupo ${nombreGrupo}`;
        contenidoPrincipal.innerHTML = ''; 
        const clon = templateCalificaciones.content.cloneNode(true);
        contenidoPrincipal.appendChild(clon);
    }

    // 2. Clic en "Volver a Grupos"
    const btnVolver = e.target.closest('#btn-volver-grupos');
    if (btnVolver) {
        const menuGrupos = document.querySelector('[data-vista="grupos"]');
        if (menuGrupos) {
            menuGrupos.click();
        }
    }
});