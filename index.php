<?php
$pagina = $_GET['pagina'] ?? 'login';

if ($pagina === 'registrar') {
    require 'views/registrar.php';
} else {
    require 'views/login.php';
}