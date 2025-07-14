<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['usuario_id'])) {
    die("❌ No has iniciado sesion.");
}

$tipo = $_POST['tipo'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$titulo = $_POST['titulo'] ?? '';
$autor = $_POST['autor'] ?? '';

// Validar archivo
if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] != 0) {
    die("❌ Error al subir archivo.");
}

$permitidos = [
    'imagen' => ['image/jpeg', 'image/png', 'image/webp'],
    'video' => ['video/mp4', 'video/webm']
];

$mime = mime_content_type($_FILES['archivo']['tmp_name']);
if (!in_array($mime, $permitidos[$tipo] ?? [])) {
    die("❌ Tipo de archivo no permitido.");
}

// Subir archivo
$nombre_archivo = uniqid() . '-' . basename($_FILES['archivo']['name']);
$ruta_destino = realpath(__DIR__ . '/../img/publicaciones');

if (!is_dir($ruta_destino)) {
    mkdir($ruta_destino, 0755, true);
}

$destino = $ruta_destino . '/' . $nombre_archivo;

if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $destino)) {
    die("❌ No se pudo mover el archivo.");
}

// Guardar en BD
$stmt = $db->prepare("INSERT INTO posts (usuario_id, tipo, archivo, titulo, autor, descripcion) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([
    $_SESSION['usuario_id'],
    $tipo,
    $nombre_archivo,
    $titulo,
    $autor,
    $descripcion
]);

echo "✅ Publicacion subida.";
?>
