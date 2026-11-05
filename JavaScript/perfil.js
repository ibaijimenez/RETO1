// Perfil: borrar preguntas y quitar guardados sin recargar la página

// Quita una tarjeta, baja el contador de arriba y, si la lista se queda vacía, enseña el aviso
function quitarTarjeta(tarjeta) {
    const seccion  = tarjeta.closest('.seccionPestana');   // #preguntas o #guardados
    const contador = document.getElementById(
        seccion.id === 'preguntas' ? 'contadorPreguntas' : 'contadorGuardados'
    );

    tarjeta.remove();
    contador.textContent = Math.max(0, parseInt(contador.textContent, 10) - 1);

    if (!seccion.querySelector('.tarjetaPregunta')) {
        seccion.querySelector('.sinResultados').style.display = 'block';
    }
}

// 1. Papelera: borrar una pregunta propia
document.addEventListener('click', async function (e) {
    const boton = e.target.closest('.btnEliminar');
    if (!boton || boton.disabled) return;

    if (!confirm('¿Seguro que quieres eliminar esta pregunta? También se borrarán sus respuestas.')) return;

    const id = boton.dataset.pregunta;
    boton.disabled = true;

    try {
        const datos = new FormData();
        datos.append('id', id);

        const respuesta = await fetch('index.php?controller=usuarioController&accion=eliminarPregunta', {
            method: 'POST',
            body: datos
        });

        if (respuesta.status === 401) {
            window.location.href = 'index.php';
            return;
        }
        if (!respuesta.ok) throw new Error('Error del servidor');

        // Fuera la tarjeta (y la copia que pueda haber en Guardados)
        document.querySelectorAll('.tarjetaPregunta[data-id="' + id + '"]').forEach(quitarTarjeta);

    } catch (err) {
        console.error(err);
        alert('No se ha podido eliminar. Inténtalo de nuevo.');
        boton.disabled = false;
    }
});

// 2. Guardados: al quitar el guardado, la tarjeta desaparece de la lista
document.addEventListener('alternado', function (e) {
    const tarjeta = e.target.closest('.tarjetaPregunta');

    if (e.detail.tipo === 'guardar' && !e.detail.activo && tarjeta && tarjeta.closest('#guardados')) {
        quitarTarjeta(tarjeta);
    }
});