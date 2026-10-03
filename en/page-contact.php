<?php
/**
 * VERSION ANGLAISE de la page « Contact » (/en/contact/), chargée par page-contact.php
 * Pas de formulaire : l'hébergement gratuit bloque l'envoi d'e-mails par PHP,
 * les messages n'arriveraient jamais. Des liens directs, c'est plus fiable.
 */

// Lien vers le CV (même adresse que sur la page Mon parcours)
$lien_cv = get_stylesheet_directory_uri() . '/documents/cv-clarisse-dupont.pdf';

get_header();
?>

<!-- On réutilise les classes existantes : même en-tête et mêmes cartes que Mon parcours -->
<main class="projet projets parcours contact">

    <!-- 1. En-tête -->
    <section class="projet__entete">
        <div class="projet__conteneur">
            <h1 class="projet__titre">Contact me</h1>
            <p class="projets__intro parcours__intro">A question, an internship offer? A short message is all it takes, and you will get a quick reply.</p>
        </div>
    </section>

    <!-- 2. Les 3 façons de me joindre : mêmes cartes que « Ma démarche » -->
    <section class="projet__section">
        <div class="projet__conteneur">
            <ul class="demarche">
                <li class="demarche__carte">
                    <h2 class="demarche__titre">Email</h2>
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
            <div class="parcours__appel">
                <h2 class="parcours__appel-titre">Available for an internship</h2>
                <p>200 hours minimum, from 13 October 2026 on set weekdays, full time during school holidays and from 31 May 2027. Any region (driving licence and own car). Happy to work in French or in English.</p>
                <div class="projet__actions">
                    <a class="bouton bouton--primaire" href="<?php echo esc_url($lien_cv); ?>" target="_blank" rel="noopener">Download my CV (in French)</a>
                    <a class="bouton bouton--secondaire" href="<?php echo esc_url(get_post_type_archive_link('projet')); ?>">See my projects</a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
