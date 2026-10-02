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
