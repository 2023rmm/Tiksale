<?php
require_once __DIR__ . '/session_init.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /tiksale/pages/login.php');
    exit;
}
