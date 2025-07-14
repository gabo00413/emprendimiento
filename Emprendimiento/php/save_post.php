<?php
session_start();
require_once 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

$url = $data['url'] ?? '';
$tipo = $data['tipo'] ?? '';
$titulo = $data['titulo'] ?? 'Sin título';
$descripcion = $data['descripcion'] ?? '';
$autor = $data['autor'] ?? 'Anónimo';
$usuario_id = $_SESSION['usuario_id'] ?? null;

if (!$usuario_id || !$url || !$tipo) {
  http_response_code(400);
  echo "Datos insuficientes";
  exit;
}

$stmt = $db->prepare("INSERT INTO posts (usuario_id, url, tipo, titulo, descripcion, autor, fecha) VALUES (?, ?, ?, ?, ?, ?, datetime('now'))");
$stmt->execute([$usuario_id, $url, $tipo, $titulo, $descripcion, $autor]);

echo "Publicación guardada";
