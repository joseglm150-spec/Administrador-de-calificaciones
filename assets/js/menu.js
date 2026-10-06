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

const opcionInicio = Array.from(menuOpciones).find((opcion) => opcion.dataset.vista === 'inicio');

if (!opcionInicio) {
	throw new Error('No se encontró la opción de menú "Inicio".');
}

mostrarVista(opcionInicio, false);
