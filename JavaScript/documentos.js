const modal = document.getElementById('modalSubir');

// Abrir el modal con el botón "+ Subir documento"
document.querySelectorAll('[data-abrir-modal]').forEach(function (boton) {
    boton.addEventListener('click', function () {
        modal.showModal();
    });
});

// Cerrar con la X y con Cancelar (la tecla Esc también lo cierra sola)
document.querySelectorAll('[data-cerrar-modal]').forEach(function (boton) {
    boton.addEventListener('click', function () {
        modal.close();
    });
});

// Si el formulario volvió con errores, se abre solo para que se vean
if (modal.dataset.abrir === '1') {
    modal.showModal();
}

// Al elegir un archivo: se enseña su nombre, se rellena el tipo y, si el nombre está vacío, se propone uno
const inputArchivo   = document.getElementById('archivo');
const textoElegido   = document.getElementById('archivoElegido');
const campoTipo      = document.getElementById('tipo');
const campoNombre    = document.getElementById('titulo');

inputArchivo.addEventListener('change', function () {
    const archivo = inputArchivo.files[0];
    if (!archivo) {
        textoElegido.textContent = '';
        campoTipo.value = '';
        return;
    }

    textoElegido.textContent = archivo.name;
    campoTipo.value = archivo.name.split('.').pop().toUpperCase();

    if (campoNombre.value.trim() === '') {
        campoNombre.value = archivo.name.replace(/\.[^.]+$/, '');
    }
});