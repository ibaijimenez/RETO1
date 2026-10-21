<?php
session_start();
require_once 'Router.php';

Router::dispatch();
