// Muestra los nombres de los archivos elegidos y resalta la zona al arrastrar
document.querySelectorAll('.zonaAdjuntos').forEach(function (zona) {
    const input = zona.querySelector('input[type="file"]');
    const lista = zona.querySelector('.listaElegidos');

    input.addEventListener('change', function () {
        lista.innerHTML = '';
        Array.from(input.files).forEach(function (archivo) {
            const li = document.createElement('li');
            li.textContent = archivo.name;
            lista.appendChild(li);
        });
    });

    ['dragenter', 'dragover'].forEach(function (ev) {
        zona.addEventListener(ev, function () { zona.classList.add('arrastrando'); });
    });
    ['dragleave', 'drop'].forEach(function (ev) {
        zona.addEventListener(ev, function () { zona.classList.remove('arrastrando'); });
    });
});