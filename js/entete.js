/**
 * En-tête « à la TF1 » : fondu tout en haut de la page,
 * puis bien visible (fond blanc) dès qu'on commence à descendre.
 * On ajoute simplement la classe « est-defile » sur l'en-tête : tout le style est dans style.css.
 */
(function () {
    var entete = document.getElementById('masthead');   // l'en-tête de Kadence
    if (!entete) return;                                 // sécurité : si absent, on ne fait rien

    function mettreAJour() {
        // window.scrollY = nombre de pixels déjà défilés depuis le haut
        entete.classList.toggle('est-defile', window.scrollY > 8);
    }

    mettreAJour();                                                   // au chargement (page rechargée au milieu)
    window.addEventListener('scroll', mettreAJour, { passive: true }); // à chaque défilement (passive = fluide)
})();

/**
 * Illustration du hero sur mobile : on mesure où commence le titre et où finit le 2e bouton,
 * et on place l'illustration exactement dans cette zone (variables CSS --illu-haut et --illu-hauteur).
 * Recalculé quand l'écran change (rotation, barre du navigateur, polices chargées).
 */
(function () {
    var hero = document.querySelector('.entry-content > .hero.alignfull');
    if (!hero) return;
    var titre = hero.querySelector('.hero__titre');
    var boutons = hero.querySelectorAll('.wp-block-button');
    if (!titre || !boutons.length) return;
    var dernier = boutons[boutons.length - 1];

    function placer() {
        var h = hero.getBoundingClientRect();
        var haut = titre.getBoundingClientRect().top - h.top - 8;      // 8 px au-dessus du titre
        var bas = dernier.getBoundingClientRect().bottom - h.top + 8;  // 8 px sous le bouton
        hero.style.setProperty('--illu-haut', Math.round(haut) + 'px');
        hero.style.setProperty('--illu-hauteur', Math.round(bas - haut) + 'px');
        hero.classList.add('hero--mesure');
    }
    placer();
    window.addEventListener('resize', placer);
    if (document.fonts && document.fonts.ready) document.fonts.ready.then(placer);
})();
