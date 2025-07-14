<?php
require_once 'db.php';             // ← ya crea $db con PDO

$usuario    = $_POST['usuario']    ?? '';
$contrasena = $_POST['clave'] ?? '';  // porque el campo se llama "clave"


if ($usuario && $contrasena) {

    // Crea la tabla si no existe
    $db->exec("CREATE TABLE IF NOT EXISTS usuarios(
                  id INTEGER PRIMARY KEY AUTOINCREMENT,
                  usuario TEXT UNIQUE,
                  contrasena TEXT)");

    // Inserta de forma segura (evita duplicados con INSERT OR IGNORE)
    $stmt = $db->prepare("INSERT OR IGNORE INTO usuarios (usuario, contrasena)
                          VALUES (?, ?)");
    $stmt->execute([$usuario, $contrasena]);

    header('Location: ../sections/login.html?registro=ok');
    exit;

} else {
    echo "❌ Faltan datos. <a href='../sections/registro.html'>Volver</a>";
}
?>
