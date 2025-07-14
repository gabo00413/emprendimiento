<?php
session_start();
require_once 'db.php';

$usuario_id = $_SESSION['usuario_id'] ?? null;

$stmt = $db->query("
  SELECT id, url, tipo, titulo, autor, descripcion, usuario_id
  FROM posts
  ORDER BY fecha DESC
");

$posts = [];
foreach ($stmt as $row) {
  $posts[] = [
    'id' => $row['id'],
    'url' => $row['url'],
    'tipo' => $row['tipo'],
    'titulo' => $row['titulo'],
    'autor' => $row['autor'],
    'descripcion' => $row['descripcion'],
    'es_mio' => $usuario_id && $usuario_id == $row['usuario_id']
  ];
}

header('Content-Type: application/json');
echo json_encode($posts);
