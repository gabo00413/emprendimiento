<?php
// Ruta absoluta al archivo post.db dentro de la carpeta data
$dbFile = realpath(__DIR__ . '/../data') . '/post.db';

try {
    // Conexión a SQLite
    $db = new PDO("sqlite:$dbFile");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Crear tabla de usuarios
    $db->exec("CREATE TABLE IF NOT EXISTS usuarios (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        usuario TEXT NOT NULL UNIQUE,
        contrasena TEXT NOT NULL
    )");

    // Crear tabla de publicaciones
    $db->exec("CREATE TABLE IF NOT EXISTS posts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        usuario_id INTEGER,
        tipo TEXT CHECK(tipo IN ('imagen', 'video')),
        archivo TEXT NOT NULL,
        descripcion TEXT,
        fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(usuario_id) REFERENCES usuarios(id)
    )");

    // Insertar un usuario de prueba si no existe
    $usuario = 'admin';
    $contrasena = '123'; // Puedes usar password_hash() si deseas
    $stmt = $db->prepare("INSERT OR IGNORE INTO usuarios (usuario, contrasena) VALUES (?, ?)");
    $stmt->execute([$usuario, $contrasena]);

    echo "✅ Base de datos y tablas creadas correctamente en: $dbFile";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
