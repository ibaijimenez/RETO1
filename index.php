<?php
session_start();
require_once 'Router.php';
require 'views/login.php';
Router::dispatch();