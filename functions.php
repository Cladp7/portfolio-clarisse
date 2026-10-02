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
