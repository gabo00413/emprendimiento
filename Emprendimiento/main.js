/* ============ MENÚ HAMBURGUESA ============ */
const toggle = document.getElementById('nav-toggle');
const menu   = document.getElementById('nav-menu');

if (toggle && menu) {
  toggle.addEventListener('click', () => {
    const expanded = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', !expanded);
    menu.classList.toggle('menu-open');
  });
}

/* ============ GALERÍA (solo si existe) ============ */
const fotoGrid  = document.getElementById('fotoGrid');
const videoGrid = document.getElementById('videoGrid');

if (fotoGrid && videoGrid) {
  /* ---- CONFIG ---- */
  const defaultUser = { user: 'admin', pass: '123' };
  const LS_KEY = 'galeriaExtra';

  /* ---- ELEMENTOS ---- */
  const loginModal = document.getElementById('loginModal');
  const pubModal   = document.getElementById('pubModal');
  const actionBtns = document.getElementById('actionBtns');
  const addFotoBtn = document.getElementById('addFotoBtn');
  const addVideoBtn= document.getElementById('addVideoBtn');
  const loginBtn   = document.getElementById('loginBtn');
  const cancelPub  = document.getElementById('cancelPub');
  const savePub    = document.getElementById('savePub');

  /* ---- ESTADO ---- */
  let pubType = 'foto';   // foto | video

  /* ---- HELPERS LS ---- */
  const saveLS = arr => localStorage.setItem(LS_KEY, JSON.stringify(arr));
  const getLS  = ()  => JSON.parse(localStorage.getItem(LS_KEY) || '[]');

  /* ---- DIBUJA PUBLICACIONES GUARDADAS ---- */
  getLS().forEach(obj => addCardToDOM(obj, false));

  /* ---- LOGIN ---- */
  if (loginModal && loginBtn) {
    loginModal.classList.add('open');   // muestra modal al entrar

    loginBtn.onclick = () => {
      const u = document.getElementById('userInput').value.trim();
      const p = document.getElementById('passInput').value.trim();
      if (u === defaultUser.user && p === defaultUser.pass) {
        loginModal.classList.remove('open');
        actionBtns.style.display = 'block';
      } else {
        alert('Credenciales incorrectas');
      }
    };
  }

  /* ---- NUEVA FOTO / VIDEO ---- */
  if (addFotoBtn)  addFotoBtn.onclick  = () => openPubModal('foto');
  if (addVideoBtn) addVideoBtn.onclick = () => openPubModal('video');

  function openPubModal(type) {
    pubType = type;
    document.getElementById('pubTitle').textContent =
      type === 'foto' ? 'Nueva Foto' : 'Nuevo Video';
    document.getElementById('pubFile').value = '';
    document.getElementById('pubTitulo').value =
    document.getElementById('pubAutor').value =
    document.getElementById('pubDesc').value  = '';
    pubModal.classList.add('open');
  }

  if (cancelPub) cancelPub.onclick = () => pubModal.classList.remove('open');

  if (savePub) savePub.onclick = async () => {
    const titulo = document.getElementById('pubTitulo').value.trim();
    const autor  = document.getElementById('pubAutor').value.trim();
    const desc   = document.getElementById('pubDesc').value.trim();
    const fileEl = document.getElementById('pubFile');
    if (!titulo || !autor || !fileEl.files.length) {
      alert('Completa todos los campos'); return;
    }

    const file = fileEl.files[0];

    /* evita duplicados */
    if (getLS().some(p => p.name === file.name)) {
      alert('Ya existe un archivo con ese nombre'); return;
    }

    /* convierte a DataURL */
    const dataURL = await new Promise(res => {
      const fr = new FileReader();
      fr.onload = () => res(fr.result);
      fr.readAsDataURL(file);
    });

    const obj = {
      id:     Date.now(),
      type:   pubType,    // foto | video
      name:   file.name,
      titulo, autor, desc,
      data:   dataURL
    };

    /* guarda */
    addCardToDOM(obj, true);
    const arr = getLS();
    arr.push(obj); saveLS(arr);

    pubModal.classList.remove('open');
  };

  /* ---- UTILIDAD PARA CREAR UNA TARJETA ---- */
  function addCardToDOM(obj, allowDelete) {
    const fig = document.createElement('figure');
    fig.className = 'gallery-card';
    fig.dataset.id = obj.id;

    const media = obj.type === 'foto'
      ? `<img src="${obj.data}" alt="${obj.titulo}">`
      : `<video controls><source src="${obj.data}" type="${obj.data.split(';')[0].split(':')[1]}"></video>`;

    fig.innerHTML = `
      ${media}
      <figcaption><strong>${obj.titulo}</strong><br>${obj.type === 'foto' ? 'Foto' : 'Autor'}: ${obj.autor}</figcaption>
    `;

    if (allowDelete) {
      const delBtn = document.createElement('button');
      delBtn.textContent = 'Eliminar';
      delBtn.className   = 'btn red';
      delBtn.style.margin = '.5rem';
      delBtn.onclick = () => removeCard(obj.id);
      fig.appendChild(delBtn);
    }

    (obj.type === 'foto' ? fotoGrid : videoGrid).appendChild(fig);
  }

  /* ---- ELIMINAR ---- */
  function removeCard(id) {
    if (!confirm('¿Eliminar esta publicación?')) return;
    const arr = getLS().filter(o => o.id !== id);
    saveLS(arr);
    document.querySelector(`figure[data-id="${id}"]`)?.remove();
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.getElementById('nav-toggle');
  const menu = document.getElementById('nav-menu');

  toggle.addEventListener('click', () => {
    menu.classList.toggle('show');
    const expanded = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', !expanded);
  });
});
