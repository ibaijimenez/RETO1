<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/AergibideModel.php';

class FavoritosController extends BaseController {

    public function favoritos() {
        $this->comprobarSesion();
        $uid = (int) $_SESSION['usuario_id'];

        $filtro = $_GET['filtro'] ?? 'todas';
        if (!in_array($filtro, ['todas', 'preguntas', 'respuestas'], true)) {
            $filtro = 'todas';
        }
        $orden = ($_GET['orden'] ?? 'recientes') === 'likes' ? 'likes' : 'recientes';

        // Preguntas con like del usuario
        $todas     = AergibideModel::getPreguntas($uid, 'todas', '', 'recientes');
        $preguntas = array_values(array_filter($todas, fn($p) => !empty($p['me_gusta'])));

        // Respuestas con like del usuario
        $respuestas = AergibideModel::getRespuestasFavoritas($uid);

        // Contadores de las pestañas (antes de filtrar)
        $totalPreguntas  = count($preguntas);
        $totalRespuestas = count($respuestas);
        $total           = $totalPreguntas + $totalRespuestas;

        if ($orden === 'likes') {
            usort($preguntas,  fn($a, $b) => $b['likes'] <=> $a['likes']);
            usort($respuestas, fn($a, $b) => $b['likes'] <=> $a['likes']);
        }

        if ($filtro === 'preguntas')  { $respuestas = []; }
        if ($filtro === 'respuestas') { $preguntas  = []; }

        require __DIR__ . '/../views/favoritos.php';
    }
}