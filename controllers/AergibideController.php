<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/AergibideModel.php';

class AergibideController extends BaseController {
    
    public function index() {
        
    }
   
    public function show() {
        
    }
    
    public function store() {
        
    }
    
    public function destroy() {
        
    }
    
    public function destroyAll() {
        
    }
    public function favoritos(){
            if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php');
            exit;
        }
        require __DIR__ . '/../views/favoritos.php';
    }
    public function preguntas() {
        // Sin sesión, al login
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php');
            exit;
        }

        $filtro   = ($_GET['filtro'] ?? 'todas') === 'mias' ? 'mias' : 'todas';
        $busqueda = trim($_GET['q'] ?? '');
        $orden    = $_GET['orden'] ?? 'recientes';

        $preguntas = AergibideModel::getPreguntas((int) $_SESSION['usuario_id'], $filtro, $busqueda, $orden);

        require __DIR__ . '/../views/preguntas.php';
    }

    public function inicio() {
        require __DIR__ . '/../views/main.php';
    }

    public function listarMasGustadas()
    {
        $preguntasMasLikes = Pregunta::getPreguntasMasLikes();
        require_once 'views/main.php'; 
    }

    public function like() {
    if (!isset($_SESSION['usuario_id'])) {
        exit;
    }

    $preguntaId = (int) ($_POST['pregunta_id'] ?? 0);

    if ($preguntaId > 0) {
        AergibideModel::toggleLike((int) $_SESSION['usuario_id'], $preguntaId);
    }
}
}