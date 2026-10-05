<?php
/**
 * Page « Contact » : WordPress l'utilise automatiquement grâce à son nom : page-{slug}.php
 * Pas de formulaire : l'hébergement gratuit bloque l'envoi d'e-mails par PHP,
 * les messages n'arriveraient jamais. Des liens directs, c'est plus fiable.
 */

// Sur /en/…, on affiche la version anglaise de cette page (dossier en/)
if (clarisse_en()) {
    require get_stylesheet_directory() . '/en/page-contact.php';
    return;
}


// Lien vers le CV (même adresse que sur la page Mon parcours)
$lien_cv = get_stylesheet_directory_uri() . '/documents/cv-clarisse-dupont.pdf';

get_header();
?>

<!-- On réutilise les classes existantes : même en-tête et mêmes cartes que Mon parcours -->
<div id="main" class="projet projets parcours contact"> <!-- contenu principal (cible du lien « Aller au contenu ») : Kadence fournit déjà la balise « main » -->

    <!-- 1. En-tête -->
    <section class="projet__entete">
        <div class="projet__conteneur">
            <h1 class="projet__titre">Me contacter</h1>
            <p class="projets__intro parcours__intro">Une question, une proposition de stage ? Un petit message suffit, la réponse arrive vite.</p>
        </div>
    </section>

    <!-- 2. Les 3 façons de me joindre : mêmes cartes que « Ma démarche » -->
    <section class="projet__section">
        <div class="projet__conteneur">
            <ul class="demarche">
                <li class="demarche__carte">
                    <h2 class="demarche__titre">E-mail</h2>
                    <!-- mailto: ouvre directement la messagerie du visiteur -->
                    <a class="contact__lien" href="mailto:dupont.clarisse@hotmail.com">dupont.clarisse@hotmail.com</a>
                </li>
                <li class="demarche__carte">
                    <h2 class="demarche__titre">LinkedIn</h2>
                    <!-- target="_blank" : nouvel onglet ; rel="noopener" : sécurité -->
                    <a class="contact__lien" href="https://www.linkedin.com/in/clarisse-dupont-5a5aa1316/" target="_blank" rel="noopener">Clarisse Dupont</a>
                </li>
                <li class="demarche__carte">
                    <h2 class="demarche__titre">GitHub</h2>
                    <a class="contact__lien" href="https://github.com/Cladp7" target="_blank" rel="noopener">github.com/Cladp7</a>
                </li>
            </ul>
        </div>
    </section>

    <!-- 3. Appel final : même carte que la fin de Mon parcours -->
    <section class="projet__section">
        <div class="projet__conteneur">
            <div class="parcours__appel parcours__appel--gauche">
                <h2 class="parcours__appel-titre">Disponible pour un stage</h2>
                <p>200 heures minimum, dès le 13 octobre 2026 en jours fixes, et à temps plein pendant les congés scolaires. Région indifférente (permis B et voiture). Échanges possibles en français ou en anglais.</p>
                <div class="projet__actions">
                    <a class="bouton bouton--primaire" href="<?php echo esc_url($lien_cv); ?>" target="_blank" rel="noopener">Télécharger mon CV</a>
                    <a class="bouton bouton--secondaire" href="<?php echo esc_url(get_post_type_archive_link('projet')); ?>">Voir mes projets</a>
                </div>
            </div>
        </div>
    </section>

</div>

<?php get_footer(); ?>
