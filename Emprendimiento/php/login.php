<?php
session_start();
require_once 'db.php';

$usuario = $_POST['usuario'] ?? '';
$contrasena = $_POST['clave'] ?? '';


$stmt = $db->prepare("SELECT * FROM usuarios WHERE usuario = ? AND contrasena = ?");
$stmt->execute([$usuario, $contrasena]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $_SESSION['usuario'] = $user['usuario'];
    $_SESSION['usuario_id'] = $user['id'];
    header("Location: ../sections/galeria.php");
    exit;
} else {
    // Redirige con mensaje de error opcional
    header("Location: ../sections/login.html?error=1");
    exit;
}
?>
