document.addEventListener("DOMContentLoaded", () => {
  const fotos = document.getElementById("fotoGrid");
  const videos = document.getElementById("videoGrid");

  const formSection = document.getElementById("formSection");
  const pubForm = document.getElementById("pubForm");
  const pubTitulo = document.getElementById("pubTitulo");
  const pubDesc = document.getElementById("pubDesc");
  const pubAutor = document.getElementById("pubAutor");

  // Función para mostrar mensajes flotantes
  function mostrarMensaje(texto, esError = false) {
    const msg = document.getElementById("mensaje");
    if (!msg) return; // por si el div no existe
    msg.textContent = texto;
    msg.className = "mensaje-flotante" + (esError ? " error" : "");
    msg.style.display = "block";
    setTimeout(() => msg.style.display = "none", 3000);
  }

  // Mostrar formulario si los botones existen
  document.getElementById("addFotoBtn")?.addEventListener("click", () => {
    formSection.style.display = "block";
  });

  document.getElementById("addVideoBtn")?.addEventListener("click", () => {
    formSection.style.display = "block";
  });

  // Datos temporales para guardar antes de subir a Cloudinary
  let tempPostData = {};

  const widget = cloudinary.createUploadWidget({
    cloudName: 'dbrx00iuc',
    uploadPreset: 'galeria_estudiantes',
    sources: ['local', 'camera', 'url'],
    multiple: false,
    resourceType: 'auto'
  }, (error, result) => {
    if (!error && result && result.event === "success") {
      fetch("../php/save_post.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          url: result.info.secure_url,
          tipo: result.info.resource_type,
          titulo: tempPostData.titulo,
          descripcion: tempPostData.descripcion,
          autor: tempPostData.autor
        })
      }).then(() => {
        mostrarMensaje("✅ Publicación subida exitosamente");
        setTimeout(() => location.reload(), 1000);
      });
    }
  });

  pubForm?.addEventListener("submit", (e) => {
    e.preventDefault();

    tempPostData = {
      titulo: pubTitulo.value.trim(),
      descripcion: pubDesc.value.trim(),
      autor: pubAutor.value.trim()
    };

    widget.open();
  });

  // Cargar publicaciones
  fetch("../php/get_posts.php")
    .then(res => res.json())
    .then(data => {
      data.forEach(post => {
        const fig = document.createElement("figure");
        fig.className = "gallery-card";
        fig.dataset.id = post.id;

        let contenido = "";
        if (post.tipo === "image") {
          contenido = `<img src="${post.url}" alt="${post.descripcion}">`;
        } else if (post.tipo === "video") {
          contenido = `<video controls><source src="${post.url}" type="video/mp4"></video>`;
        }

        const eliminarBtn = post.es_mio
          ? `<button class="btn eliminar-btn" data-id="${post.id}">🗑</button>`
          : "";

        fig.innerHTML = `
          ${contenido}
          <figcaption>
            <strong>${post.titulo || 'Sin título'}</strong><br>
            Autor: ${post.autor || 'Desconocido'}<br>
            ${post.descripcion || ''}<br>
            ${eliminarBtn}
          </figcaption>
        `;

        if (post.tipo === "image") fotos.appendChild(fig);
        else if (post.tipo === "video") videos.appendChild(fig);
      });

      // Funcionalidad eliminar sin recargar
      document.querySelectorAll(".eliminar-btn").forEach(btn => {
        btn.onclick = async () => {
          if (!confirm("¿Eliminar esta publicación?")) return;
          const id = btn.dataset.id;
          const fd = new FormData();
          fd.append("id", id);

          const res = await fetch("../php/eliminar.php", {
            method: "POST",
            body: fd
          });

          const txt = await res.text();
          if (txt.includes("✅")) {
  mostrarMensaje("✅ Publicación eliminada con éxito.");
  setTimeout(() => location.reload(), 1000);
} else {
  mostrarMensaje("❌ Publicacion eliminada con exito.", true);
  setTimeout(() => location.reload(), 1000);
}

        };
      });
    });
});
