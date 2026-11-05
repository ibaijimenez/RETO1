# AERGIBIDE SL · Plataforma de preguntas y documentación técnica

> *El conocimiento también vuela más alto.*

Aplicación web interna para el equipo de ingeniería de **Aergibide SL**. Permite hacer preguntas, responderlas, compartir documentación técnica y recibir avisos de lo que pasa con tus publicaciones.

Proyecto **RETO1**, desarrollado por el equipo **Vertex**.

---

## Funcionalidades

**Usuarios y sesión**
- Registro e inicio de sesión.
- Perfil con tus preguntas, tus preguntas guardadas y tu información personal.
- Cambio de contraseña desde el perfil.
- Roles: `user` y `admin`.

**Preguntas y respuestas**
- Listado de preguntas con buscador (por título o autor), orden (más recientes, más likes, más respuestas) y filtro por temas.
- Pestañas *Todas* / *Mis preguntas*.
- Crear, editar y eliminar tus propias preguntas (hasta 3 etiquetas por pregunta).
- Detalle de pregunta con sus respuestas.
- Likes en preguntas y respuestas, y guardado de preguntas en favoritos (sin recargar la página, con `fetch`).
- Archivos adjuntos en preguntas y respuestas (PDF, DOC/DOCX, XLS/XLSX, PNG, JPG, TXT; máx. 10 MB por archivo y 5 por publicación). Las imágenes se muestran en miniatura.

**Favoritos**
- Vista con las preguntas y respuestas que te han gustado, con filtro y orden.

**Documentos**
- Subida de documentos técnicos (PDF, DOC, DOCX, XLS, XLSX, hasta 50 MB) desde una ventana modal.
- Cuatro categorías: *Informes técnicos*, *Manuales*, *Ensayos y resultados* y *Planos y diseños*.
- Tabla de documentos recientes, filtro por categoría y descarga.

**Notificaciones**
- Aviso cuando alguien responde a tu pregunta o da like a tu pregunta o respuesta.
- Burbuja con el número de notificaciones no leídas en el menú lateral.

**Administración** (solo rol `admin`)
- Listado de usuarios y creación de usuarios.
- Consulta de las preguntas y respuestas de cada usuario y moderación (eliminar preguntas y respuestas).

---

## Tecnologías

| Capa | Tecnología |
|---|---|
| Backend | PHP (arquitectura MVC propia, sin framework) |
| Base de datos | MySQL, acceso con PDO y consultas preparadas |
| Frontend | HTML, CSS (Grid y Flexbox) y JavaScript sin librerías |
| Fuentes | Montserrat y Source Sans 3 (Google Fonts) |
| Entorno de desarrollo | Laragon (Apache + MySQL + PHP) |

---

## Estructura del proyecto

```
RETO1/
├── config/
│   └── config.php              # Datos de conexión a la base de datos
├── controllers/                # Controladores (reciben la petición y eligen la vista)
│   ├── BaseController.php
│   ├── loginController.php
│   ├── registroController.php
│   ├── AergibideController.php # Página de inicio
│   ├── PreguntasController.php
│   ├── FavoritosController.php
│   ├── DocumentosController.php
│   ├── notificacionesController.php
│   ├── usuarioController.php
│   └── AdminController.php
├── models/
│   ├── Database.php            # Conexión PDO (singleton)
│   └── AergibideModel.php      # Consultas a la base de datos
├── views/                      # Plantillas PHP
│   ├── layout/                 # head, sidebar y footer comunes
│   ├── admin/
│   └── ...                     # login, registrar, main, preguntas, detallePregunta, etc.
├── JavaScript/                 # Likes, guardados, pestañas, modal, adjuntos...
├── styles/
│   └── styles.css
├── img/                        # Iconos, logos e imágenes
├── uploads/                    # Archivos subidos (no se sube a Git)
├── Documentacion/
├── index.php                   # Punto de entrada
└── Router.php                  # Enrutador
```

### Cómo funciona el enrutado

Todas las peticiones pasan por `index.php`, que arranca la sesión y llama a `Router::dispatch()`. El router lee dos parámetros de la URL:

```
index.php?controller=PreguntasController&accion=detalle&id=5
```

- `controller`: nombre de la clase del controlador (por defecto, `loginController`).
- `accion`: método que se ejecuta (por defecto, `index`).

El controlador pide los datos a `AergibideModel` y carga la vista correspondiente.

---

## Instalación en local

### Requisitos

- PHP 8.0 o superior (con la extensión `fileinfo` activada)
- MySQL o MariaDB
- Apache (recomendado: [Laragon](https://laragon.org/) en Windows)

### Pasos

1. **Clona el repositorio** dentro de la carpeta pública del servidor. Con Laragon:

   ```bash
   cd C:\laragon\www
   git clone <URL-del-repositorio> RETO1
   ```

2. **Crea la base de datos** llamada `reto1` e importa el script SQL del proyecto (tablas y datos de ejemplo).

   > Indica aquí la ruta del script, por ejemplo `Documentacion/reto1.sql`.

3. **Revisa la configuración** en `config/config.php`:

   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'reto1');
   define('DB_USER', 'root');
   define('DB_PASS', 'toor');
   ```

4. **Comprueba la ruta base.** En `views/layout/head.php` (y en `login.php` y `registrar.php`) hay una etiqueta `<base href="/RETO1/">`. Si la carpeta tiene otro nombre, cámbiala.

5. **Permisos de subida.** El servidor debe poder escribir en `uploads/`. Las subcarpetas (`docs`, `preguntas`, `adjuntos`) se crean solas la primera vez que se sube un archivo.

6. **Abre la aplicación** en el navegador:

   ```
   www.aergibide.com

   192.168.56.102
   ```

### Tablas principales

`usuarios`, `preguntas`, `respuestas`, `temas`, `pregunta_temas`, `pregunta_likes`, `respuesta_likes`, `preguntas_guardadas`, `adjuntos`, `documentos` y `notificaciones`.

---

## Seguridad

- Consultas preparadas con PDO en todo el acceso a datos.
- Salida escapada con `htmlspecialchars()` en las vistas.
- Archivos subidos: validación de extensión y tamaño, nombre aleatorio en disco y `basename()` para impedir rutas tipo `../`.
- Comprobación de sesión en cada controlador y de rol en la zona de administración.
- Solo el autor puede editar o borrar sus preguntas.

### Limitaciones conocidas

- Las contraseñas se guardan y comparan **en texto plano**. En un entorno real habría que usar `password_hash()` y `password_verify()`.
- No hay protección contra CSRF en los formularios.

---

## Posibles mejoras

- Cifrado de contraseñas y token CSRF.
- Marcar una respuesta como "la correcta".
- Paginación en el listado de preguntas.
- Buscador también en documentos.

---

## Equipo

Equipo **Vertex**:

- Michael
- Ibai
- Urko

---

## Enlaces
- Figma:
     https://www.figma.com/design/5TbFd3i7GAwVUMkv8TEQeg/VERTEX---RETO-1?node-id=0-1&p=f&t=X8O4xE9uw5sCOQJh-0
- Trello:
     https://trello.com/b/F9ZxnopR/reto-1-aergibide-sl
