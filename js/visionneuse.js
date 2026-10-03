/* =========================================================
   VISIONNEUSE DES IMAGES DE PROJET (conseil d'Elodie)
   Avant : un clic ouvrait l'image dans un nouvel onglet.
   Maintenant : l'image s'ouvre en grand par-dessus la page.
   - Échap ou le bouton × ferme ; les flèches ← → passent d'une image à l'autre ;
   - un clic à côté de l'image ferme aussi ;
   - à la fermeture, le focus revient sur l'image cliquée (navigation au clavier).
   Sans JavaScript, le lien ouvre simplement l'image : rien n'est perdu.
   ========================================================= */
(function () {
  var galerie = document.querySelector('.galerie');
  if (!galerie || typeof HTMLDialogElement === 'undefined') return;

  var liens = Array.prototype.slice.call(galerie.querySelectorAll('.galerie__image'));
  if (!liens.length) return;

  var t = galerie.dataset; // textes FR ou EN, écrits par PHP sur la galerie
  var actuel = 0;
  var dernierLien = null;

  // La boîte de dialogue, créée une seule fois
  var boite = document.createElement('dialog');
  boite.className = 'visionneuse';
  boite.setAttribute('aria-label', t.titre || 'Visionneuse');
  boite.innerHTML =
    '<figure class="visionneuse__cadre">' +
      '<img class="visionneuse__image" alt="">' +
      '<figcaption class="visionneuse__legende"></figcaption>' +
    '</figure>' +
    '<button type="button" class="visionneuse__bouton visionneuse__fermer" aria-label="' + (t.fermer || 'Fermer') + '">×</button>' +
    '<button type="button" class="visionneuse__bouton visionneuse__precedent" aria-label="' + (t.precedent || 'Image précédente') + '">←</button>' +
    '<button type="button" class="visionneuse__bouton visionneuse__suivant" aria-label="' + (t.suivant || 'Image suivante') + '">→</button>' +
    '<p class="visionneuse__compteur" aria-live="polite"></p>';
  document.body.appendChild(boite);

  var img = boite.querySelector('.visionneuse__image');
  var legende = boite.querySelector('.visionneuse__legende');
  var compteur = boite.querySelector('.visionneuse__compteur');
  var plusieurs = liens.length > 1;
  boite.classList.toggle('visionneuse--seule', !plusieurs);

  function afficher(i) {
    actuel = (i + liens.length) % liens.length; // après la dernière, on revient à la première
    var lien = liens[actuel];
    var vignette = lien.querySelector('img');
    var titre = lien.closest('.galerie__item').querySelector('.galerie__titre');
    img.src = lien.href;
    img.alt = vignette ? vignette.alt : '';
    legende.textContent = titre ? titre.textContent : (vignette ? vignette.alt : '');
    compteur.textContent = plusieurs ? (actuel + 1) + ' / ' + liens.length : '';
  }

  liens.forEach(function (lien, i) {
    lien.addEventListener('click', function (e) {
      e.preventDefault();
      dernierLien = lien;
      afficher(i);
      boite.showModal();
      document.documentElement.classList.add('visionneuse-ouverte'); // bloque le défilement de la page
    });
  });

  boite.querySelector('.visionneuse__fermer').addEventListener('click', function () { boite.close(); });
  boite.querySelector('.visionneuse__precedent').addEventListener('click', function () { afficher(actuel - 1); });
  boite.querySelector('.visionneuse__suivant').addEventListener('click', function () { afficher(actuel + 1); });

  // Clic sur le fond sombre (pas sur l'image ni sur un bouton) = fermer
  boite.addEventListener('click', function (e) {
    if (e.target === boite || e.target.classList.contains('visionneuse__cadre')) boite.close();
  });

  boite.addEventListener('keydown', function (e) {
    if (!plusieurs) return;
    if (e.key === 'ArrowLeft') { e.preventDefault(); afficher(actuel - 1); }
    if (e.key === 'ArrowRight') { e.preventDefault(); afficher(actuel + 1); }
  });

  // Glisser du doigt sur mobile
  var departX = null;
  boite.addEventListener('touchstart', function (e) { departX = e.touches[0].clientX; }, { passive: true });
  boite.addEventListener('touchend', function (e) {
    if (departX === null || !plusieurs) return;
    var ecart = e.changedTouches[0].clientX - departX;
    if (Math.abs(ecart) > 48) afficher(actuel + (ecart < 0 ? 1 : -1));
    departX = null;
  });

  boite.addEventListener('close', function () {
    document.documentElement.classList.remove('visionneuse-ouverte');
    if (dernierLien) dernierLien.focus(); // le focus revient là où on était
  });
})();
