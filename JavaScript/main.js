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

document.querySelectorAll('.btnToggle').forEach(function (boton) {

    boton.addEventListener('click', function () {

        const tipo     = boton.dataset.tipo;   // "like" o "guardar"
        const estabaOn = boton.getAttribute('aria-pressed') === 'true';
        const ahoraOn  = !estabaOn;

        // Cambiar el estado y la imagen
        boton.setAttribute('aria-pressed', ahoraOn);
        boton.querySelector('img').src = ICONOS[tipo][ahoraOn ? 'on' : 'off'];

        // Si es un "me gusta", sumar o restar uno al contador
        if (tipo === 'like') {
            const contador = boton.parentElement.querySelector('.numLikes');
            contador.textContent = parseInt(contador.textContent, 10) + (ahoraOn ? 1 : -1);
        }
    });
});


document.addEventListener('click', function (e) {
    var boton = e.target.closest('.btnLike');
    if (!boton) return;

    var datos = new FormData();
    datos.append('pregunta_id', boton.dataset.id);

    fetch('index.php?controller=AergibideController&accion=like', {
        method: 'POST',
        body: datos
    });
});

