<?php
// Ce fichier s'exécute automatiquement quand le thème enfant est actif.

// Version anglaise du portfolio (adresses /en/…, traductions, bouton FR / EN)
require_once get_stylesheet_directory() . '/inc/langue.php';

// On "accroche" notre fonction au moment où WordPress charge les styles et scripts du site.
add_action('wp_enqueue_scripts', 'clarisse_charger_styles');

function clarisse_charger_styles()
{
    // On charge la feuille de style de NOTRE thème enfant (style.css).
    wp_enqueue_style(
        'kadence-child-style',                      // un nom unique pour identifier ce fichier
        get_stylesheet_uri(),                       // le chemin vers le style.css du thème enfant
        array('kadence-global'),                  // on le charge APRÈS le CSS de Kadence, pour pouvoir le surcharger
        filemtime(get_stylesheet_directory() . '/style.css') // la date de dernière modification du fichier : dès qu'on enregistre style.css,
        // le numéro change et le navigateur recharge le CSS tout seul (plus besoin de Ctrl + F5)
    );
}
/* =========================================================
   EN-TÊTE QUI RESTE EN HAUT (sticky) : petit script js/entete.js
   « true » = chargé en bas de page, après le HTML (la page s'affiche plus vite).
   ========================================================= */
add_action('wp_enqueue_scripts', 'clarisse_charger_scripts');

function clarisse_charger_scripts()
{
    wp_enqueue_script(
        'clarisse-entete',
        get_stylesheet_directory_uri() . '/js/entete.js',
        array(),
        filemtime(get_stylesheet_directory() . '/js/entete.js'),
        true
    );
    // Visionneuse des images : seulement sur les pages projet (là où il y a une galerie)
    if (is_singular('projet')) {
        wp_enqueue_script(
            'clarisse-visionneuse',
            get_stylesheet_directory_uri() . '/js/visionneuse.js',
            array(),
            filemtime(get_stylesheet_directory() . '/js/visionneuse.js'),
            true
        );
    }
}

/* =========================================================
   CONVERSION AUTOMATIQUE EN WEBP
   Quand j'importe une image PNG ou JPG dans la médiathèque,
   WordPress crée ses différentes tailles directement en WebP
   (plus léger → site plus rapide, idéal pour InfinityFree).
   ========================================================= */
add_filter('image_editor_output_format', 'clarisse_images_en_webp');

function clarisse_images_en_webp($formats)
{
    $formats['image/png']  = 'image/webp';   // les PNG (tes captures d'écran) → WebP
    $formats['image/jpeg'] = 'image/webp';   // les JPG (photos) → WebP
    return $formats;                         // on renvoie la liste modifiée à WordPress
}


/* =========================================================
   PAGE « PROJETS » (archive du type de contenu projet)
   On demande à Kadence : en-tête transparent (le dégradé passe derrière le menu,
   comme sur les autres pages) et pas de bandeau de titre gris par défaut :
   c'est notre modèle archive-projet.php qui affiche son propre titre.
   « kadence_post_layout » est un filtre prévu par Kadence pour modifier sa mise en page.
   ========================================================= */
add_filter('kadence_post_layout', 'clarisse_mise_en_page_projets');

function clarisse_mise_en_page_projets($layout)
{
    if (is_post_type_archive('projet') || is_page('mon-parcours') || is_page('contact')) {   // pages Projets, Mon parcours et Contact
        $layout['transparent'] = 'enable';  // en-tête transparent
        $layout['title']       = 'hide';    // pas de bandeau « Projets » gris
        $layout['vpadding']    = 'hide';    // pas de marges automatiques : on gère nous-mêmes
    }
    return $layout;
}


/* =========================================================
   PIED DE PAGE
   On remplace le texte par défaut de Kadence (« Thème WordPress par Kadence WP »)
   par mon nom et mes liens. « theme_mod_footer_html_content » est un filtre
   automatique de WordPress sur le réglage du pied de page de Kadence.
   ========================================================= */
add_filter('theme_mod_footer_html_content', 'clarisse_pied_de_page');

function clarisse_pied_de_page($contenu)
{
    return '{copyright} {year} Clarisse Dupont'
        . ' · <a href="https://www.linkedin.com/in/clarisse-dupont-5a5aa1316/" target="_blank" rel="noopener">LinkedIn</a>'
        . ' · <a href="https://github.com/Cladp7" target="_blank" rel="noopener">GitHub</a>';
}


/* =========================================================
   SECTION « PROJETS PHARES » SUR L'ACCUEIL
   On l'ajoute automatiquement après le contenu de la page d'accueil (le hero),
   grâce au filtre « the_content ». Le HTML est dans template-parts/accueil-projets.php.
   ========================================================= */
add_filter('the_content', 'clarisse_ajouter_projets_phares');

function clarisse_ajouter_projets_phares($contenu)
{
    // Seulement sur la page d'accueil, et seulement pour le contenu principal
    if (! is_front_page() || ! in_the_loop() || ! is_main_query()) {
        return $contenu;
    }
    ob_start();                                                   // on « enregistre » ce qui va être affiché…
    get_template_part('template-parts/accueil-projets');
    return $contenu . ob_get_clean();                             // …et on l'ajoute après le hero
}


/* =========================================================
   Aperçu du lien quand on partage le portfolio (WhatsApp, Messenger, LinkedIn…)
   Sans ces balises « Open Graph », les applis prenaient la 1re grande image de la page
   (la carte de La Vallée du Savoir) : l'aperçu ne représentait pas le portfolio.
   - Partout : l'image apercu-portfolio.jpg (1200 × 630 px, le format conseillé)
   - Sur une page projet : la couverture du projet + son résumé
   ========================================================= */
add_action('wp_head', 'clarisse_apercu_partage', 5);

function clarisse_apercu_partage()
{
    $titre       = wp_get_document_title();
    $description = get_bloginfo('description');                       // « Ancienne institutrice bilingue, future web designer »
    $image       = get_stylesheet_directory_uri() . '/images/apercu-portfolio.jpg';
    global $wp;
    $adresse     = home_url($wp->request ? trailingslashit($wp->request) : '/');  // l'adresse de la page affichée

    // Page d'un projet : on montre CE projet
    if (is_singular('projet')) {
        if (has_excerpt()) {
            $description = wp_strip_all_tags(get_the_excerpt());
        }
        if (has_post_thumbnail()) {
            $image = get_the_post_thumbnail_url(null, 'full');
        }
    }

    echo '<meta property="og:type" content="website">' . "\n";
    echo '<meta property="og:site_name" content="Clarisse Dupont – Portfolio">' . "\n";
    echo '<meta property="og:locale" content="' . (clarisse_en() ? 'en_GB' : 'fr_BE') . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($titre) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($adresse) . '">' . "\n";
    echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
}


/* =========================================================
   Nouvelles options pour les fiches projet (ACF), sans toucher au groupe de champs :
   - une 4e couleur de catégorie « nuit » (bleu nuit) pour les projets back-end
   - de nouveaux outils dans la liste à cocher
   ========================================================= */
add_filter('acf/load_field/name=couleur_carte', 'clarisse_couleur_nuit');

function clarisse_couleur_nuit($champ)
{
    $champ['choices']['nuit'] = 'Bleu nuit (back-end)';
    return $champ;
}

add_filter('acf/load_field/name=outils', 'clarisse_outils_supplementaires');

function clarisse_outils_supplementaires($champ)
{
    foreach (array('PHP', 'Voyager', 'MySQL', 'Vite', 'Agent IA (Copilot)') as $outil) {
        $champ['choices'][$outil] = $outil;
    }
    return $champ;
}


/* Page « Mes projets » : TOUS les projets sur une seule page (WordPress s'arrêtait à 10) */
add_action('pre_get_posts', 'clarisse_tous_les_projets');

function clarisse_tous_les_projets($requete)
{
    if (!is_admin() && $requete->is_main_query() && $requete->is_post_type_archive('projet')) {
        $requete->set('posts_per_page', -1);   // -1 = aucune limite
    }
}

/* =========================================================
   COMPÉTENCES (page Mon parcours, FR et EN)
   Retour de l'évaluateur : une compétence sans projet qui la montre
   ne vaut qu'une déclaration. Chaque pastille est donc un LIEN vers
   le projet qui la prouve. Ce qui n'est pas encore prouvé par un projet
   va dans « En cours d'apprentissage ».
   ========================================================= */
function clarisse_competences()
{
    // [texte FR, texte EN, slug du projet qui la prouve]
    $groupes = array(
        array(cl_t('Design UX/UI', 'UX/UI design'), array(
            array('Maquettes Figma', 'Figma mock-ups', 'fastfit'),
            array('Prototypes interactifs', 'Interactive prototypes', 'lingopop'),
            array('Hiérarchie visuelle', 'Visual hierarchy', 'don-de-sang'),
            array('États des composants', 'Component states', 'car-wash'),
            array('Lois UX (Hick, divulgation progressive)', 'UX laws (Hick, progressive disclosure)', 'black-friday'),
            array('Design fonctionnel et émotionnel', 'Functional and emotional design', 'webinaire-ia'),
        )),
        array(cl_t('Développement web', 'Web development'), array(
            array('HTML/CSS', 'HTML/CSS', 'la-boucle'),
            array('JavaScript', 'JavaScript', 'cinq-bibliotheques-js'),
            array('Tailwind', 'Tailwind', 'philippe-noel'),
            array('Vite', 'Vite', 'memia'),
            array('Git et GitHub', 'Git and GitHub', 'memia'),
            array('WordPress', 'WordPress', 'la-vallee-du-savoir'),
            array('PHP', 'PHP', 'la-boucle'),
            array('Laravel', 'Laravel', 'y-a-un-truc-qui-blog'),
            array('MySQL', 'MySQL', 'y-a-un-truc-qui-blog'),
        )),
    );
    $atouts = array(
        cl_t('Français : langue maternelle', 'French: native'),
        cl_t('Anglais : C1 (CCALI 2021)', 'English: C1 (CCALI 2021)'),
        cl_t('Pédagogie', 'Teaching skills'),
        cl_t('Prise de parole', 'Public speaking'),
        cl_t('Persévérance', 'Perseverance'),
        cl_t('Curiosité', 'Curiosity'),
    );
    $en_cours = array(
        cl_t('Recherche utilisateur', 'User research'),
        cl_t('Personas', 'Personas'),
        cl_t('Parcours utilisateur', 'User journeys'),
        cl_t('Accessibilité (WCAG)', 'Accessibility (WCAG)'),
    );
    ?>
    <section class="projet__section">
        <div class="projet__conteneur">
            <h2 class="titre-section"><?php echo cl_t('Compétences', 'Skills'); ?></h2>
            <p class="competences__intro"><?php echo cl_t('Chaque compétence mène au projet qui la montre.', 'Each skill links to the project that shows it.'); ?></p>
            <div class="competences">
                <?php foreach ($groupes as $groupe) : ?>
                    <div class="competences__carte">
                        <h3 class="competences__titre"><?php echo esc_html($groupe[0]); ?></h3>
                        <ul class="pastilles">
                            <?php foreach ($groupe[1] as $c) :
                                $projet = get_page_by_path($c[2], OBJECT, 'projet');
                                $texte  = cl_t($c[0], $c[1]);
                                if (!$projet) {                                   // projet introuvable : simple pastille
                                    echo '<li class="pastille">' . esc_html($texte) . '</li>';
                                    continue;
                                }
                                $titre = get_the_title($projet);                  // titre traduit en anglais si besoin
                            ?>
                                <li>
                                    <a class="pastille pastille--lien" href="<?php echo esc_url(get_permalink($projet)); ?>"
                                       aria-label="<?php echo esc_attr($texte . cl_t(' : voir le projet ', ': see the project ') . $titre); ?>">
                                        <?php echo esc_html($texte); ?> <span aria-hidden="true">→</span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
                <div class="competences__carte">
                    <h3 class="competences__titre"><?php echo cl_t('Langues et atouts', 'Languages and strengths'); ?></h3>
                    <ul class="pastilles">
                        <?php foreach ($atouts as $a) : ?><li class="pastille"><?php echo esc_html($a); ?></li><?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <div class="competences__apprentissage">
                <h3 class="competences__titre"><?php echo cl_t('En cours d’apprentissage (2e année)', 'Currently learning (2nd year)'); ?></h3>
                <p><?php echo cl_t('Étudiés cette année, notamment lors d’une étude UX menée en groupe : ils rejoindront le portfolio dès qu’un projet les montrera.', 'Studied this year, notably in a group UX study: they will join the portfolio as soon as a project shows them.'); ?></p>
                <ul class="pastilles">
                    <?php foreach ($en_cours as $a) : ?><li class="pastille pastille--en-cours"><?php echo esc_html($a); ?></li><?php endforeach; ?>
                </ul>
            </div>
        </div>
    </section>
    <?php
}

/* Le logo est un lien vers l'accueil, mais Kadence l'affiche en image de fond : le lien n'a donc
   aucun texte. On lui ajoute un nom lisible par les lecteurs d'écran (WCAG 2.4.4).
   Kadence n'offre pas de filtre pour ce lien : on « relit » le HTML de l'en-tête avant de l'afficher. */
add_action('kadence_before_header', function () {
    ob_start();
}, 1);
add_action('kadence_after_header', function () {
    $html = ob_get_clean();
    $nom  = esc_attr(cl_t('Miss Clarisse – retour à l’accueil', 'Miss Clarisse – back to the home page'));
    echo str_replace('rel="home">', 'rel="home" aria-label="' . $nom . '">', $html);
}, 99);
