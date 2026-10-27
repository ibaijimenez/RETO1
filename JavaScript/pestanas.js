function cambiarPestana(nombrePestana, botonPulsado) {
    // ocultas 
    document.getElementById('preguntas').style.display = 'none';
    document.getElementById('guardados').style.display = 'none';
    document.getElementById('informacion').style.display = 'none';

    // solo muestra desde el princiopo la de preguntas
    document.getElementById(nombrePestana).style.display = 'block';

    let botones = document.querySelectorAll('.pestana-btn');
    botones.forEach(btn => btn.classList.remove('activa'));
    
    botonPulsado.classList.add('activa');
}
