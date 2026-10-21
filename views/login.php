
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <base href="/RETO1/">
    <title>Aergibide SL - Iniciar sesión</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>

    <div class="container_login">

        <div class="fotoLogin">
            <img src="img/login/foto_login.png" alt="Avión de Aergibide">
        </div>

        <div class="iniciarSesion">

            <div class="formularioLogin">

                <h3 class="bienvenida">BIENVENIDO DE NUEVO</h3>

                <h1>INICIAR SESIÓN</h1>

                <p class="descripcionLogin">
                    Accede a tu cuenta para continuar con la
                    plataforma de AERGIBIDE SL.
                </p>

                <form action="index.php?controller=loginController&accion=login" method="POST">

                    <label for="usuario">Usuario</label>

                    <div class="campoLogin">
                        <img src="img/login/logo_usuario.png" alt="logoUsuario">
                        <input type="text" id="usuario" name="usuario"
                               placeholder="Introduce tu nombre de Usuario" required>
                    </div>

                    <label for="contrasena">Contraseña</label>

                    <div class="campoLogin">
                        <img src="img/login/candado.png" alt="logoContrasena">
                        <input type="password" id="contrasena" name="contrasena"
                               placeholder="Introduce tu contraseña" required>
                    </div>
                    <?php if (isset($error) && !empty($error)): ?>
                    <div style="color: red; margin-bottom: 15px; font-weight: bold; text-align: center;">
                     <?php echo $error; ?>
                    </div>
                    <?php endif; ?>
                    <button type="submit">INICIAR SESIÓN</button>

                </form>

                <img class="separacionLogin"
                     src="img/login/separacion.png" alt="">

                <p class="registroLogin">
                    ¿No tienes cuenta?

                    <a href="index.php?controller=registrarController&accion=index" class="registrar">Regístrate</a>


            </div>
        </div>

    </div>

</body>
</html>
