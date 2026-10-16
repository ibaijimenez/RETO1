<?php
require_once 'Router.php';
require "views/layout/head.php";
require "views/layout/sidebar.php";

Router::dispatch();
