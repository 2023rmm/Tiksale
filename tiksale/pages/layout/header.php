<?php
require_once __DIR__ . '/../../includes/session_init.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['email'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tiksale dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- ONE CSS SOURCE -->
    <link rel="stylesheet" href="../assets/css/dashboard.css">

    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="dashboard-body">
<div class="dashboard-container">
