<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <base href="/RETO1/">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aergibide SL - Registrarse</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>

    <div class="container_registro">

        <!-- IZQUIERDA: imagen (incluye logo y texto) -->
        <div class="fotoRegistro">
            <img src="img/registrar/foto_registrar.png" alt="Únete al equipo de Aergibide">
        </div>

        <!-- DERECHA: formulario -->
        <div class="registrarse">

            <div class="formularioLogin">

                <h3 class="bienvenida">CREA TU CUENTA</h3>

                <h1>REGISTRARSE</h1>

                <p class="descripcionLogin">
                    Completa los datos para empezar a utilizar la
                    plataforma.
                </p>

                <form action="index.php?controller=registrarController&action=registrar" method="POST">

                    <label for="nombre">Nombre</label>
                    <div class="campoLogin">
                        <img src="img/registrar/perfil.png" alt="">
                        <input type="text" id="nombre" name="nombre"
                               placeholder="Tu nombre" required>
                    </div>

                    <label for="apellidos">Apellidos</label>
                    <div class="campoLogin">
                        <img src="img/registrar/perfil.png" alt="">
                        <input type="text" id="apellidos" name="apellidos"
                               placeholder="Tus apellidos" required>
                    </div>

                    <label for="correo">Correo electrónico</label>
                    <div class="campoLogin">
                        <img src="img/registrar/gmail.png" alt="">
                        <input type="email" id="correo" name="correo"
                               placeholder="tu@gmail.com" required>
                    </div>

                    <label for="contrasena">Contraseña</label>
                    <div class="campoLogin">
                        <img src="img/registrar/candado.png" alt="">
                        <input type="password" id="contrasena" name="contrasena"
                               placeholder="Crea tu contraseña" required>
                    </div>

                    <label for="confirmar">Confirmar contraseña</label>
                    <div class="campoLogin">
                        <img src="img/registrar/candado.png" alt="">
                        <input type="password" id="confirmar" name="confirmar"
                               placeholder="Repite la contraseña" required>
                    </div>

                    <label for="departamento">Departamento</label>
                    <div class="campoLogin">
                        <img src="img/registrar/departamento.png" alt="">
                        <select id="departamento" name="departamento" required>
                            <option value="" disabled selected>Selecciona un departamento</option>
                            <option value="ingenieria">Ingeniería</option>
                            <option value="calidad">Calidad</option>
                            <option value="produccion">Producción</option>
                            <option value="administracion">Administración</option>
                        </select>
                    </div>

                    <label class="aceptoTerminos">
                        <input type="checkbox" name="terminos" required>
                        <span>
                            Acepto la <a href="#" class="registrar">política de privacidad</a>
                            y los <a href="#" class="registrar">términos de uso</a>.
                        </span>
                    </label>

                    <button type="submit" id="btnCrearCuenta">CREAR CUENTA</button>

                </form>

                <img class="separacionLogin"
                     src="img/login/separacion.png" alt="">

                <p class="registroLogin">
                    ¿Ya tienes cuenta?
                    <a href="index.php?pagina=login" class="regigit branchstrar">Inicia sesión</a>
                </p>

            </div>
        </div>

    </div>

</body>
</html>