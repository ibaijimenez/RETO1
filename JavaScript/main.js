// Iconos de cada tipo de botón: "on" = con relleno, "off" = sin relleno
const ICONOS = {
    like: {
        on:  'img/main/coraConRelleno.png',
        off: 'img/main/coraSinRelleno.png'
    },
    guardar: {
        on:  'img/main/guardadoConRelleno.png',
        off: 'img/main/guardadoSinRelleno.png'
    }
};

// Tipo del botón -> tipo que entiende el servidor (AergibideModel::alternar)
const TIPOS = {
    like:    'pregunta_like',
    guardar: 'pregunta_guardar'
};

document.addEventListener('click', async function (e) {
    const boton = e.target.closest('.btnToggle');
    if (!boton || boton.disabled) return;

    // Sin data-id (por ejemplo, datos de ejemplo) no hay nada que guardar
    if (!boton.dataset.id) return;

    const tipo = boton.dataset.tipo;   // "like" o "guardar"
    boton.disabled = true;             // evita dobles clics mientras responde el servidor

    try {
        const datos = new FormData();
        datos.append('tipo', TIPOS[tipo]);
        datos.append('id', boton.dataset.id);

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

        // Estado e icono según lo que dice la base de datos
        boton.setAttribute('aria-pressed', resultado.activo ? 'true' : 'false');
        boton.querySelector('img').src = ICONOS[tipo][resultado.activo ? 'on' : 'off'];

        // Contador de likes real
        if (tipo === 'like') {
            const contador = boton.parentElement.querySelector('.numLikes');
            if (contador) contador.textContent = resultado.total;
        }
    } catch (err) {
        console.error(err);
        alert('No se ha podido guardar. Inténtalo de nuevo.');
    } finally {
        boton.disabled = false;
    }
});

// Cierra el panel de "Temas" al hacer clic fuera
document.addEventListener('click', function (e) {
    document.querySelectorAll('details.filtroTemas[open]').forEach(function (d) {
        if (!d.contains(e.target)) d.removeAttribute('open');
    });
});