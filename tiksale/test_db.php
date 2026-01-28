<?php
require_once __DIR__ . '/config/Database.php';

$db = new Database();
$conn = $db->getConnection();

if ($conn) {
    echo "✅ Database connected successfully!";
} else {
    echo "❌ Connection failed";
}
