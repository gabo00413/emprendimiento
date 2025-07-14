<?php
session_start();
require_once 'db.php';

$carpeta = realpath(__DIR__ . '/../img/publicaciones/');
if (!$carpeta || !is_dir($carpeta)) {
    die("❌ Error: No se pudo acceder a la carpeta de publicaciones.");
}

$archivos = scandir($carpeta);
$usuario_id = 1; // Cambia si el ID del usuario creador es distinto

$insertados = 0;
$omitidos = 0;

foreach ($archivos as $archivo) {
    if ($archivo === '.' || $archivo === '..') continue;

    $rutaCompleta = $carpeta . $archivo;
    $ext = pathinfo($archivo, PATHINFO_EXTENSION);
    $ext = strtolower($ext);

    // Determinar tipo
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
        $tipo = 'imagen';
    } elseif (in_array($ext, ['mp4', 'mov', 'avi'])) {
        $tipo = 'video';
    } else {
        $omitidos++;
        continue; // No es válido
    }

    // Verificar si ya existe
    $stmt = $db->prepare("SELECT COUNT(*) FROM posts WHERE archivo = ?");
    $stmt->execute([$archivo]);
    if ($stmt->fetchColumn() > 0) {
        $omitidos++;
        continue; // Ya existe
    }

    // Insertar
    $descripcion = ucfirst(pathinfo($archivo, PATHINFO_FILENAME));
  $stmt = $db->prepare("
  INSERT INTO posts (archivo, tipo, titulo, autor, descripcion, fecha)
  VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
");
$stmt->execute([$archivo, $tipo, $titulo, $autor, $descripcion]);

    $insertados++;
}

echo "✅ Publicaciones insertadas: $insertados<br>";
echo "⚠️ Archivos omitidos: $omitidos<br>";
echo "<a href='../sections/galeria.php'>Ir a galería</a>";
