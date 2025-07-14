<?php
$dbFile = realpath(__DIR__ . '/../data') . '/post.db';

if (!file_exists($dbFile)) {
    include_once __DIR__ . '/crear-db.php';
}

try {
    $db = new PDO("sqlite:$dbFile");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Error de conexión: " . $e->getMessage());
}
?>
