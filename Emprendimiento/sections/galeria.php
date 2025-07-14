<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Galería – YachaykunaLink</title>
  <link rel="stylesheet" href="../css/styles.css" />
  <link rel="stylesheet" href="../css/galeria.css" />
  <style>
    .mensaje-flotante {
      position: fixed;
      top: 20px;
      right: 20px;
      background-color: #28a745;
      color: white;
      padding: 12px 18px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
      display: none;
      z-index: 1000;
      font-weight: bold;
    }
    .mensaje-flotante.error {
      background-color: #dc3545;
    }
  </style>
</head>
<body>
<header class="header">
  <div class="header__content">
    <div class="logo">
      <img src="../img/Logo.png" alt="Logo YachaykunaLink">
      <span>YachaykunaLink</span>
    </div>

    <!-- Botón hamburguesa -->
    <button id="nav-toggle" class="nav-toggle" aria-label="Abrir menú" aria-expanded="false">
      <span class="bar"></span><span class="bar"></span><span class="bar"></span>
    </button>

    <!-- Menú de navegación -->
    <ul id="nav-menu" class="nav-menu">
      <li><a href="../index.html" class="nav__link nav__link--active">Inicio</a></li>
      <li><a href="../sections/silabo.html" class="nav__link">Programa</a></li>
      <li><a href="../sections/recursos.html" class="nav__link">Recursos</a></li>
      <li><a href="../sections/galeria.php" class="nav__link">Galeria</a></li>
      <li><a href="../sections/contacto.html" class="nav__link">Contacto</a></li>
    </ul>
  </div>
</header>

<section class="hero--small galeria-intro">
  <div class="contenedor">
    <h1>Galería de Publicaciones</h1>
    <p>Explora las fotos y videos subidos por nuestros usuarios.</p>
  </div>
</section>

<!-- 🟨 Mensaje flotante -->
<div id="mensaje" class="mensaje-flotante"></div>

<?php if (isset($_SESSION['usuario'])): ?>
<section class="gallery-wrap">
  <div id="actionBtns" style="margin:2rem 0">
    <a href="../php/logout.php" class="btn red" style="margin-left:1rem">Cerrar sesión</a>
  </div>
</section>

<section id="formSection" class="gallery-wrap">
  <h3>Nueva Publicación</h3>
  <form id="pubForm">
    <label>Título</label>
    <input type="text" id="pubTitulo" name="titulo" required>
    <label>Descripción</label>
    <textarea id="pubDesc" name="descripcion" rows="2" required></textarea>
    <label>Autor</label>
    <input type="text" id="pubAutor" name="autor" required>
    <div style="margin-top:1rem">
      <button type="submit" class="btn">Subir publicación</button>
    </div>
  </form>
</section>
<?php endif; ?>

<section class="gallery-wrap">
  <h3>Fotos</h3>
  <div id="fotoGrid" class="gallery-grid fotos"></div>
</section>

<section class="gallery-wrap">
  <h3>Videos</h3>
  <div id="videoGrid" class="gallery-grid videos"></div>
</section>

<?php if (!isset($_SESSION['usuario'])): ?>
  <div style="text-align:center;margin:2rem 0">
    <a href="login.html" class="btn">Modificar</a>
  </div>
<?php endif; ?>

<script src="../js/galeria.js" defer></script>

<!-- Cloudinary Upload Widget -->
<script src="https://widget.cloudinary.com/v2.0/global/all.js"></script>
<script>
  const widget = cloudinary.createUploadWidget({
    cloudName: 'dbrx00iuc',
    uploadPreset: 'galeria_estudiantes',
    sources: ['local', 'camera', 'url'],
    multiple: false,
    resourceType: 'auto'
  }, (error, result) => {
    if (!error && result && result.event === "success") {
      fetch('../php/save_post.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          url: result.info.secure_url,
          tipo: result.info.resource_type,
          titulo: "Sin título",
          descripcion: "",
          autor: "<?= $_SESSION['usuario'] ?? 'Desconocido' ?>"
        })
      }).then(() => location.reload());
    }
  });

  // Asegurarse de que el botón existe antes de agregar el listener
  const uploadBtn = document.getElementById("upload_widget");
  if (uploadBtn) {
    uploadBtn.addEventListener("click", () => widget.open(), false);
  }

  // Función para mostrar mensajes en vivo
  function mostrarMensaje(texto, esError = false) {
    const msg = document.getElementById("mensaje");
    msg.textContent = texto;
    msg.className = "mensaje-flotante" + (esError ? " error" : "");
    msg.style.display = "block";
    setTimeout(() => msg.style.display = "none", 3000);
  }
</script>

</body>
</html>
