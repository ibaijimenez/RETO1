<?php
require_once __DIR__ . '/Database.php';

class AergibideModel {

    public static function getAll() {

    }

    public static function getById($id) {

    }

    public static function create($datos) {

    }

    public static function deleteById($id) {

    }

    public static function deleteAll() {

    }

    public static function comprobarCredenciales($usuario, $contrasena) {
        $datos = [
            "usuario" => $usuario,
            "contrasena" => $contrasena
        ];
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE nombre = :usuario AND passwd = :contrasena");
        $stmt->execute($datos);
        return $stmt->fetch();
    }

    public static function getPreguntas(int $usuarioId, string $filtro = 'todas', string $busqueda = '', string $orden = 'recientes'): array
    {
        // Lista blanca de ordenaciones (evita inyección SQL)
        $ordenes = [
            'recientes'  => 'p.fecha_creacion DESC, p.id DESC',
            'likes'      => 'likes DESC',
            'respuestas' => 'respuestas DESC',
        ];
        $orderBy = $ordenes[$orden] ?? $ordenes['recientes'];

        $sql = "
            SELECT p.id, p.titulo, p.fecha_creacion,
                u.nombre AS autor,
                (SELECT GROUP_CONCAT(t.nombre ORDER BY t.nombre SEPARATOR '||')
                    FROM pregunta_temas pt
                    JOIN temas t ON t.id = pt.tema_id
                    WHERE pt.pregunta_id = p.id) AS temas,
                (SELECT COUNT(*) FROM respuestas r WHERE r.pregunta_id = p.id) AS respuestas,
                (SELECT COUNT(*) FROM pregunta_likes l WHERE l.pregunta_id = p.id) AS likes,
                EXISTS(SELECT 1 FROM pregunta_likes l2
                        WHERE l2.pregunta_id = p.id AND l2.usuario_id = :uid_like) AS me_gusta,
                EXISTS(SELECT 1 FROM preguntas_guardadas g
                        WHERE g.pregunta_id = p.id AND g.usuario_id = :uid_guard) AS guardada
            FROM preguntas p
            JOIN usuarios u ON u.id = p.usuario_id
            WHERE 1 = 1
        ";
        $params = [':uid_like' => $usuarioId, ':uid_guard' => $usuarioId];

        if ($filtro === 'mias') {
            $sql .= " AND p.usuario_id = :uid_mias";
            $params[':uid_mias'] = $usuarioId;
        }

        if ($busqueda !== '') {
            $sql .= " AND (p.titulo LIKE :q1
                    OR u.nombre LIKE :q2
                    OR EXISTS (SELECT 1 FROM pregunta_temas pt2
                                    JOIN temas t2 ON t2.id = pt2.tema_id
                                WHERE pt2.pregunta_id = p.id AND t2.nombre LIKE :q3))";
            $like = '%' . $busqueda . '%';
            $params[':q1'] = $params[':q2'] = $params[':q3'] = $like;
        }

        $sql .= " ORDER BY $orderBy";

        $db   = Database::getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);   // array asociativo, como usa la vista
    }

    public static function getPreguntasMasLikes(?int $limite = null): array
    {
        // pregunta_likes no tiene columna "id": se cuenta por usuario_id
        $sql = "
            SELECT
                p.id,
                p.titulo,
                p.fecha_creacion,
                u.nombre AS autor,
                COUNT(l.usuario_id) AS likes
            FROM preguntas p
            JOIN usuarios u ON u.id = p.usuario_id
            LEFT JOIN pregunta_likes l ON l.pregunta_id = p.id
            GROUP BY p.id, p.titulo, p.fecha_creacion, u.nombre
            ORDER BY likes DESC
        ";

        if ($limite !== null && $limite > 0) {
            $sql .= " LIMIT " . (int)$limite;
        }

        $db   = Database::getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Devuelve todos los temas (para rellenar los desplegables)
    public static function getTemas(): array
    {
        $db   = Database::getConnection();
        $stmt = $db->query("SELECT id, nombre FROM temas ORDER BY nombre");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Guarda una pregunta nueva y sus temas. Devuelve el id de la pregunta.
    public static function crearPregunta(int $usuarioId, string $titulo, string $descripcion, array $temasIds): int
    {
        $db = Database::getConnection();

        try {
            // Transacción: o se guarda todo, o no se guarda nada
            $db->beginTransaction();

            // 1. La pregunta
            $stmt = $db->prepare(
                "INSERT INTO preguntas (usuario_id, titulo, descripcion)
                 VALUES (:uid, :titulo, :descripcion)"
            );
            $stmt->execute([
                ':uid'         => $usuarioId,
                ':titulo'      => $titulo,
                ':descripcion' => $descripcion,
            ]);
            $preguntaId = (int) $db->lastInsertId();

            // 2. Sus temas (tabla intermedia)
            $stmtTema = $db->prepare(
                "INSERT INTO pregunta_temas (pregunta_id, tema_id) VALUES (:pregunta, :tema)"
            );
            foreach ($temasIds as $temaId) {
                $stmtTema->execute([':pregunta' => $preguntaId, ':tema' => $temaId]);
            }

            $db->commit();
            return $preguntaId;

        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    // Devuelve una pregunta con autor, likes y si el usuario la ha likeado/guardado. null si no existe.
    public static function getPregunta(int $id, int $usuarioId): ?array
    {
        $sql = "
            SELECT p.id, p.titulo, p.descripcion, p.fecha_creacion,
                u.nombre AS autor,
                (SELECT COUNT(*) FROM respuestas r WHERE r.pregunta_id = p.id) AS respuestas,
                (SELECT COUNT(*) FROM pregunta_likes l WHERE l.pregunta_id = p.id) AS likes,
                EXISTS(SELECT 1 FROM pregunta_likes l2
                        WHERE l2.pregunta_id = p.id AND l2.usuario_id = :uid_like) AS me_gusta,
                EXISTS(SELECT 1 FROM preguntas_guardadas g
                        WHERE g.pregunta_id = p.id AND g.usuario_id = :uid_guard) AS guardada
            FROM preguntas p
            JOIN usuarios u ON u.id = p.usuario_id
            WHERE p.id = :id
        ";
        $db   = Database::getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute([':uid_like' => $usuarioId, ':uid_guard' => $usuarioId, ':id' => $id]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ?: null;
    }

    // Respuestas de una pregunta (las más antiguas primero), con likes y si el usuario dio like
    public static function getRespuestas(int $preguntaId, int $usuarioId): array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare("
            SELECT r.id, r.contenido, r.fecha_creacion, u.nombre AS autor,
                (SELECT COUNT(*) FROM respuesta_likes rl WHERE rl.respuesta_id = r.id) AS likes,
                EXISTS(SELECT 1 FROM respuesta_likes rl2
                        WHERE rl2.respuesta_id = r.id AND rl2.usuario_id = :uid_like) AS me_gusta
            FROM respuestas r
            JOIN usuarios u ON u.id = r.usuario_id
            WHERE r.pregunta_id = :pid
            ORDER BY r.fecha_creacion ASC
        ");
        $stmt->execute([':uid_like' => $usuarioId, ':pid' => $preguntaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Respuestas a las que el usuario ha dado like (para Favoritos)
    public static function getRespuestasFavoritas(int $usuarioId): array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare("
            SELECT r.id, r.pregunta_id, r.contenido, r.fecha_creacion,
                   u.nombre AS autor,
                   p.titulo AS pregunta_titulo,
                   (SELECT COUNT(*) FROM respuesta_likes x WHERE x.respuesta_id = r.id) AS likes
            FROM respuesta_likes rl
            JOIN respuestas r ON r.id = rl.respuesta_id
            JOIN usuarios u   ON u.id = r.usuario_id
            JOIN preguntas p  ON p.id = r.pregunta_id
            WHERE rl.usuario_id = :uid
            ORDER BY rl.id DESC
        ");
        $stmt->execute([':uid' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Guarda una respuesta nueva. Devuelve su id.
    public static function crearRespuesta(int $preguntaId, int $usuarioId, string $contenido): int
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO respuestas (pregunta_id, usuario_id, contenido)
            VALUES (:pid, :uid, :contenido)
        ");
        $stmt->execute([':pid' => $preguntaId, ':uid' => $usuarioId, ':contenido' => $contenido]);
        return (int) $db->lastInsertId();
    }

    // Activa o desactiva un like / guardado. Devuelve el nuevo estado y el total.
    // $tipo sale de una lista blanca, así que las tablas nunca vienen del usuario.
    public static function alternar(string $tipo, int $id, int $usuarioId): array
    {
        $config = [
            'pregunta_like'    => ['tabla' => 'pregunta_likes',      'col' => 'pregunta_id'],
            'pregunta_guardar' => ['tabla' => 'preguntas_guardadas', 'col' => 'pregunta_id'],
            'respuesta_like'   => ['tabla' => 'respuesta_likes',     'col' => 'respuesta_id'],
        ];
        if (!isset($config[$tipo])) {
            throw new InvalidArgumentException('Tipo no válido');
        }
        $tabla = $config[$tipo]['tabla'];
        $col   = $config[$tipo]['col'];

        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT 1 FROM $tabla WHERE $col = :id AND usuario_id = :uid");
        $stmt->execute([':id' => $id, ':uid' => $usuarioId]);
        $existe = (bool) $stmt->fetchColumn();

        if ($existe) {
            $stmt = $db->prepare("DELETE FROM $tabla WHERE $col = :id AND usuario_id = :uid");
        } else {
            $stmt = $db->prepare("INSERT INTO $tabla ($col, usuario_id) VALUES (:id, :uid)");
        }
        $stmt->execute([':id' => $id, ':uid' => $usuarioId]);

        $stmt = $db->prepare("SELECT COUNT(*) FROM $tabla WHERE $col = :id");
        $stmt->execute([':id' => $id]);

        return ['activo' => !$existe, 'total' => (int) $stmt->fetchColumn()];
    }

    // ==================================================================
    // DOCUMENTOS
    // ==================================================================

    // Lista de documentos. Se puede filtrar por categoría y limitar cuántos salen.
    public static function getDocumentos(string $categoria = '', ?int $limite = null): array
    {
        $sql = "
            SELECT d.id, d.categoria, d.titulo, d.descripcion,
                   d.nombre_archivo, d.tipo_mime, d.fecha_subida,
                   u.nombre AS autor
            FROM documentos d
            JOIN usuarios u ON u.id = d.usuario_id
            WHERE 1 = 1
        ";
        $params = [];

        if ($categoria !== '') {
            $sql .= " AND d.categoria = :categoria";
            $params[':categoria'] = $categoria;
        }

        $sql .= " ORDER BY d.fecha_subida DESC, d.id DESC";

        if ($limite !== null && $limite > 0) {
            $sql .= " LIMIT " . (int) $limite;
        }

        $db   = Database::getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cuántos documentos hay en cada categoría. Devuelve ['Manuales' => 3, ...]
    public static function getConteoCategorias(): array
    {
        $db   = Database::getConnection();
        $stmt = $db->query("SELECT categoria, COUNT(*) AS total FROM documentos GROUP BY categoria");

        $conteos = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $conteos[$fila['categoria']] = (int) $fila['total'];
        }
        return $conteos;
    }

    // Devuelve un documento por su id (o null si no existe)
    public static function getDocumento(int $id): ?array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM documentos WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ?: null;
    }

    // Guarda los datos de un documento ya subido. Devuelve su id.
    public static function crearDocumento(int $usuarioId, string $categoria, string $titulo, string $descripcion,
                                          string $nombreArchivo, string $ruta, string $tipoMime): int
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO documentos (usuario_id, categoria, titulo, descripcion, nombre_archivo, ruta_almacenamiento, tipo_mime)
            VALUES (:uid, :categoria, :titulo, :descripcion, :nombre, :ruta, :mime)
        ");
        $stmt->execute([
            ':uid'         => $usuarioId,
            ':categoria'   => $categoria,
            ':titulo'      => $titulo,
            ':descripcion' => $descripcion,
            ':nombre'      => $nombreArchivo,
            ':ruta'        => $ruta,
            ':mime'        => $tipoMime,
        ]);
        return (int) $db->lastInsertId();
    }
        // ==================================================================
    // ADMIN
    // ==================================================================

    public static function getUsuarios(): array
    {
        $db   = Database::getConnection();
        $stmt = $db->query("SELECT id, nombre, email, rol, fecha_registro FROM usuarios ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getUsuario(int $id): ?array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare("SELECT id, nombre, email, rol FROM usuarios WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ?: null;
    }

    public static function crearUsuario(string $nombre, string $email, string $passwd, string $rol): bool
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO usuarios (nombre, email, passwd, rol) VALUES (:nombre, :email, :passwd, :rol)");
        return $stmt->execute([
            ':nombre' => $nombre,
            ':email'  => $email,
            ':passwd' => $passwd,
            ':rol'    => $rol,
        ]);
    }
    public static function eliminarPregunta(int $id): ?int
{
    $db = Database::getConnection();

    $stmt = $db->prepare("SELECT usuario_id FROM preguntas WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $autorId = $stmt->fetchColumn();
    if ($autorId === false) {
        return null;
    }

    try {
        $db->beginTransaction();

        $db->prepare("DELETE FROM respuesta_likes WHERE respuesta_id IN (SELECT id FROM respuestas WHERE pregunta_id = :id)")
           ->execute([':id' => $id]);
        $db->prepare("DELETE FROM respuestas WHERE pregunta_id = :id")->execute([':id' => $id]);
        $db->prepare("DELETE FROM pregunta_likes WHERE pregunta_id = :id")->execute([':id' => $id]);
        $db->prepare("DELETE FROM preguntas_guardadas WHERE pregunta_id = :id")->execute([':id' => $id]);
        $db->prepare("DELETE FROM pregunta_temas WHERE pregunta_id = :id")->execute([':id' => $id]);
        $db->prepare("DELETE FROM preguntas WHERE id = :id")->execute([':id' => $id]);

        $db->commit();
        return (int) $autorId;
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        throw $e;
    }
}
public static function getRespuestasPorUsuario(int $usuarioId): array
{
    $db   = Database::getConnection();
    $stmt = $db->prepare("
        SELECT r.id, r.pregunta_id, r.contenido, r.fecha_creacion,
               p.titulo AS pregunta_titulo,
               (SELECT COUNT(*) FROM respuesta_likes x WHERE x.respuesta_id = r.id) AS likes
        FROM respuestas r
        JOIN preguntas p ON p.id = r.pregunta_id
        WHERE r.usuario_id = :uid
        ORDER BY r.fecha_creacion DESC, r.id DESC
    ");
    $stmt->execute([':uid' => $usuarioId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public static function eliminarRespuesta(int $id): ?int
{
    $db = Database::getConnection();

    $stmt = $db->prepare("SELECT usuario_id FROM respuestas WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $autorId = $stmt->fetchColumn();
    if ($autorId === false) {
        return null;
    }

    try {
        $db->beginTransaction();
        $db->prepare("DELETE FROM respuesta_likes WHERE respuesta_id = :id")->execute([':id' => $id]);
        $db->prepare("DELETE FROM respuestas WHERE id = :id")->execute([':id' => $id]);
        $db->commit();
        return (int) $autorId;
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        throw $e;
    }
}
}
