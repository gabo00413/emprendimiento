<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["error" => "❌ No has iniciado sesion."]);
    exit;
}

$id = intval($_POST['id'] ?? 0);

// Obtener info del post
$stmt = $db->prepare("SELECT url, usuario_id FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

// Validaciones
if (!$post) {
    echo json_encode(["error" => "❌ Publicacion no encontrada."]);
    exit;
}

if ($post['usuario_id'] != $_SESSION['usuario_id']) {
    echo json_encode(["error" => "❌ No tienes permiso para eliminar esta publicacion."]);
    exit;
}

// Eliminar de la base de datos
$stmt = $db->prepare("DELETE FROM posts WHERE id = ?");
$stmt->execute([$id]);

echo json_encode(["success" => "✅ Publicacion eliminada con exito."]);
?>
