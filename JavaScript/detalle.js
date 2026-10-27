// Likes, guardados y "respuesta favorita" de la página de detalle.
// Cada botón lleva data-tipo y data-id; se manda al servidor y se actualiza sin recargar.
document.addEventListener('click', async function (e) {
    const btn = e.target.closest('.btnAccion');
    if (!btn || btn.disabled) return;

    btn.disabled = true;   // evita dobles clics mientras responde el servidor

    try {
        const datos = new FormData();
        datos.append('tipo', btn.dataset.tipo);
        datos.append('id', btn.dataset.id);

        const respuesta = await fetch('index.php?controller=PreguntasController&accion=alternar', {
            method: 'POST',
            body: datos
        });

        if (respuesta.status === 401) {
            window.location.href = 'index.php';   // sesión caducada: al login
            return;
        }
        if (!respuesta.ok) throw new Error('Error del servidor');

        const resultado = await respuesta.json();

        // Estado (lo pinta el CSS con aria-pressed)
        btn.setAttribute('aria-pressed', resultado.activo ? 'true' : 'false');

        // Contador de likes: SOLO en los botones de like
        if (btn.dataset.tipo === 'pregunta_like' || btn.dataset.tipo === 'respuesta_like') {
            const num = btn.parentElement.querySelector('.numLikes');
            if (num) num.textContent = resultado.total;
        }

        // Texto que cambia (Guardar / Guardada)
        const texto = btn.querySelector('.textoAccion[data-on]');
        if (texto) texto.textContent = resultado.activo ? texto.dataset.on : texto.dataset.off;

    } catch (err) {
        console.error(err);
        alert('No se ha podido guardar. Inténtalo de nuevo.');
    } finally {
        btn.disabled = false;
    }
});