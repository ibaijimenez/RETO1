<?php
require_once __DIR__ . '/Database.php';

class AergibideModel {
    
    public static function getAll() {
        
    }
    
    public static function getById($id) {
        
    }
    
    // registrar usuario nuevo
    public static function create($datos) {
        $db = Database::getConnection();

        $stmt = $db->prepare(
            "INSERT INTO usuarios (nombre, apellidos, correo, passwd, departamento)
             VALUES (:nombre, :apellidos, :correo, :passwd, :departamento)"
        );

        return $stmt->execute([
            ':nombre'       => $datos['nombre'],
            ':apellidos'    => $datos['apellidos'],
            ':correo'       => $datos['correo'],
            ':contrasena'   => ':passwd' => $datos['contrasena'],, 
            ':departamento' => $datos['departamento']
        ]);

    }

    // comprobar si existe correo ya registrado
    public static function existeCorreo($correo) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT 1 FROM usuarios WHERE correo = c");
        $stmt->execute(['c' => $correo]);
        return (bool) $stmt->fetch();
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
        $stmt=$db -> prepare("SELECT * FROM usuarios where  nombre=:usuario AND passwd=:contrasena ");
        $stmt->execute($datos); 
        return $stmt->fetch(); 
    }

    public static function getPreguntas(int $usuarioId, string $filtro = 'todas', string $busqueda = '', string $orden = 'recientes'): array
    {
        // Lista blanca de ordenaciones (evita inyección SQL)
        $ordenes = [
            'recientes'  => 'p.fecha_creacion DESC',
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
        $sql = "
            SELECT 
                p.id, 
                p.titulo, 
                p.fecha_creacion,
                u.nombre AS autor,
                COUNT(l.id) AS likes
            FROM preguntas p
            JOIN usuarios u ON u.id = p.usuario_id
            LEFT JOIN pregunta_likes l ON l.pregunta_id = p.id
            GROUP BY p.id
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

    public static function toggleLike($usuarioId, $preguntaId) {
    $db = Database::getConnection();

    // Miramos si ya le había dado like
    $stmt = $db->prepare("SELECT * FROM pregunta_likes WHERE pregunta_id = :p AND usuario_id = :u");
    $stmt->execute([':p' => $preguntaId, ':u' => $usuarioId]);

    if ($stmt->fetch()) {
        // Ya tenía like: lo quitamos
        $stmt = $db->prepare("DELETE FROM pregunta_likes WHERE pregunta_id = :p AND usuario_id = :u");
    } else {
        // No tenía like: lo añadimos
        $stmt = $db->prepare("INSERT INTO pregunta_likes (pregunta_id, usuario_id) VALUES (:p, :u)");
    }
    $stmt->execute([':p' => $preguntaId, ':u' => $usuarioId]);
}
}
