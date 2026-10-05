const menuOpciones = document.querySelectorAll('.menu-opcion[data-vista]');
const contenidoPrincipal = document.getElementById('contenido-principal');
const tituloSeccion = document.getElementById('titulo-seccion');

if (!contenidoPrincipal || !tituloSeccion) {
	throw new Error('No se encontró el contenedor del menú o su título.');
}

const contenidoInicio = Array.from(contenidoPrincipal.childNodes, (nodo) => nodo.cloneNode(true));

menuOpciones.forEach((opcion) => {
	opcion.addEventListener('click', () => {
		if (opcion.dataset.vista === 'inicio') {
			contenidoPrincipal.replaceChildren(...contenidoInicio.map((nodo) => nodo.cloneNode(true)));
		} else {
			const vista = document.getElementById(`vista-${opcion.dataset.vista}`);

			if (!vista) {
				throw new Error(`No se encontró la vista "${opcion.dataset.vista}".`);
			}

			contenidoPrincipal.replaceChildren(vista.content.cloneNode(true));
		}

		contenidoPrincipal.scrollTop = 0;
		tituloSeccion.textContent = opcion.dataset.titulo;

		menuOpciones.forEach((otraOpcion) => {
			otraOpcion.classList.remove('activo');
			otraOpcion.removeAttribute('aria-current');
		});

		opcion.classList.add('activo');
		opcion.setAttribute('aria-current', 'page');
	});
});
