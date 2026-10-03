<?php
/**
 * VERSION ANGLAISE DU PORTFOLIO (sans extension)
 * ------------------------------------------------
 * Principe : chaque page française a son équivalent anglais sous /en/ :
 *   /                  → /en/
 *   /projets/          → /en/projects/
 *   /projets/memia/    → /en/projects/memia/
 *   /mon-parcours/     → /en/my-journey/
 *   /contact/          → /en/contact/
 * C'est le MÊME contenu WordPress (mêmes images, mêmes champs) :
 * seuls les textes changent. Les traductions des projets sont rangées
 * dans un champ caché « traduction_en » (format JSON) de chaque projet.
 */

/* ---------------------------------------------------------
   1. Sommes-nous sur une page anglaise ?
   On regarde simplement si l'adresse commence par /en/
   --------------------------------------------------------- */
function clarisse_en()
{
    static $en = null;                                            // calculé une seule fois par page
    if ($en === null) {
        $chemin = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $en = (bool) preg_match('#^/en(/|$)#', $chemin);
    }
    return $en;
}

/* Petit raccourci pour les textes des modèles : cl_t('Bonjour', 'Hello') */
function cl_t($fr, $en)
{
    return clarisse_en() ? $en : $fr;
}

/* Correspondance des adresses FR ↔ EN (premier dossier de l'adresse) */
function clarisse_dossiers()
{
    return array('projets' => 'projects', 'mon-parcours' => 'my-journey', 'contact' => 'contact');
}

/* Adresse française → adresse anglaise équivalente (et inversement) */
function clarisse_chemin_en($chemin_fr)
{
    $chemin = '/' . ltrim($chemin_fr, '/');
    foreach (clarisse_dossiers() as $fr => $en) {
        if (preg_match('#^/' . $fr . '(/|$)#', $chemin)) {
            $chemin = preg_replace('#^/' . $fr . '#', '/' . $en, $chemin);
            break;
        }
    }
    return '/en' . ($chemin === '/' ? '/' : $chemin);
}

function clarisse_chemin_fr($chemin_en)
{
    $chemin = preg_replace('#^/en#', '', '/' . ltrim($chemin_en, '/'));
    $chemin = $chemin === '' ? '/' : $chemin;
    foreach (clarisse_dossiers() as $fr => $en) {
        if (preg_match('#^/' . $en . '(/|$)#', $chemin)) {
            $chemin = preg_replace('#^/' . $en . '#', '/' . $fr, $chemin);
            break;
        }
    }
    return $chemin;
}

/* ---------------------------------------------------------
   2. Les adresses /en/… existent pour WordPress (règles de réécriture)
   --------------------------------------------------------- */
add_action('init', 'clarisse_regles_en');

function clarisse_regles_en()
{
    $accueil = (int) get_option('page_on_front');
    add_rewrite_rule('^en/?$', 'index.php?page_id=' . $accueil, 'top');
    add_rewrite_rule('^en/projects/?$', 'index.php?post_type=projet', 'top');
    add_rewrite_rule('^en/projects/([^/]+)/?$', 'index.php?projet=$matches[1]', 'top');
    add_rewrite_rule('^en/my-journey/?$', 'index.php?pagename=mon-parcours', 'top');
    add_rewrite_rule('^en/contact/?$', 'index.php?pagename=contact', 'top');

    // On « rafraîchit » les règles une seule fois (après chaque nouvelle version de ce fichier)
    if (get_option('clarisse_regles_en') !== '1') {
        flush_rewrite_rules(false);
        update_option('clarisse_regles_en', '1');
    }
}

/* WordPress ne doit pas « corriger » /en/… vers l'adresse française */
add_filter('redirect_canonical', function ($redirection) {
    return clarisse_en() ? false : $redirection;
});

/* ---------------------------------------------------------
   3. Sur une page anglaise, TOUS les liens internes deviennent anglais
   (menu, cartes, « projet suivant »…) : on modifie home_url().
   Les fichiers (images, CSS, CV…) ne sont pas touchés.
   --------------------------------------------------------- */
add_filter('home_url', 'clarisse_liens_en', 10, 2);

function clarisse_liens_en($url, $chemin)
{
    if (!clarisse_en() || is_admin() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return $url;
    }
    $p = parse_url($url, PHP_URL_PATH) ?: '/';
    if (preg_match('#^/(en(/|$)|wp-|feed|xmlrpc)#', $p)) {          // déjà anglais, ou fichier WordPress
        return $url;
    }
    return str_replace($p, clarisse_chemin_en($p), $url);
}

/* ---------------------------------------------------------
   4. Les traductions des contenus (projets + accueil)
   --------------------------------------------------------- */
add_action('init', function () {
    foreach (array('projet', 'page') as $type) {
        register_post_meta($type, 'traduction_en', array(
            'type'          => 'string',          // un texte JSON
            'single'        => true,
            'show_in_rest'  => true,              // modifiable par l'API (pour la mise en ligne)
            'auth_callback' => function () { return current_user_can('edit_posts'); },
        ));
    }
});

/* Lit la traduction d'un contenu : renvoie un tableau (ou un tableau vide) */
function clarisse_traduction($post_id = null)
{
    $post_id = $post_id ?: get_the_ID();
    $json = get_post_meta($post_id, 'traduction_en', true);
    $data = $json ? json_decode($json, true) : null;
    return is_array($data) ? $data : array();
}

/* Les champs ACF (contexte, objectif, étapes…) en anglais */
add_filter('acf/format_value', 'clarisse_acf_en', 20, 3);

function clarisse_acf_en($valeur, $post_id, $champ)
{
    if (!clarisse_en() || !is_numeric($post_id)) {
        return $valeur;
    }
    $t = clarisse_traduction($post_id);
    if (isset($t[$champ['name']])) {
        $valeur = $t[$champ['name']];
        if (in_array($champ['type'], array('textarea', 'wysiwyg'), true)) {
            $valeur = wpautop($valeur);                               // mêmes paragraphes que la version française
        }
    }
    return $valeur;
}

/* Titre et résumé des projets */
add_filter('the_title', function ($titre, $id = 0) {
    if (clarisse_en() && $id && !is_admin()) {
        $t = clarisse_traduction($id);
        if (!empty($t['title'])) { return $t['title']; }
    }
    return $titre;
}, 10, 2);

add_filter('get_the_excerpt', function ($resume, $post = null) {
    if (clarisse_en() && $post) {
        $t = clarisse_traduction($post->ID);
        if (!empty($t['excerpt'])) { return $t['excerpt']; }
    }
    return $resume;
}, 10, 2);

/* Contenu de la page d'accueil (le hero) en anglais */
add_filter('the_content', function ($contenu) {
    if (clarisse_en() && is_front_page() && in_the_loop()) {
        $t = clarisse_traduction();
        if (!empty($t['content'])) { return do_blocks($t['content']); }
    }
    return $contenu;
}, 5);

/* ---------------------------------------------------------
   5. Réglages généraux en anglais : langue de la page, slogan, titre d'onglet
   --------------------------------------------------------- */
add_filter('language_attributes', function ($attributs) {
    return clarisse_en() ? 'lang="en-GB"' : $attributs;
});

add_filter('option_blogdescription', function ($slogan) {
    return (clarisse_en() && !is_admin()) ? 'Former bilingual teacher, future web designer' : $slogan;
});

add_filter('document_title_parts', function ($parties) {
    if (!clarisse_en()) { return $parties; }
    if (is_post_type_archive('projet')) { $parties['title'] = 'Projects'; }
    if (is_page('mon-parcours'))        { $parties['title'] = 'My journey'; }
    if (is_page('contact'))             { $parties['title'] = 'Contact'; }
    if (is_singular('projet'))          { $parties['title'] = get_the_title(get_queried_object_id()); }
    return $parties;
});

/* ---------------------------------------------------------
   6. Le menu : libellés en anglais + bouton FR / EN
   --------------------------------------------------------- */
add_filter('nav_menu_item_title', function ($titre) {
    if (!clarisse_en()) { return $titre; }
    $traductions = array('Mes projets' => 'My projects', 'Mon parcours' => 'My journey', 'Me contacter' => 'Contact me', 'Accueil' => 'Home');
    return $traductions[trim(wp_strip_all_tags($titre))] ?? $titre;
});

/* Lien vers la même page dans l'autre langue */
function clarisse_lien_autre_langue()
{
    $chemin = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $base   = untrailingslashit(get_option('home'));               // l'adresse du site, sans le filtre
    return $base . (clarisse_en() ? clarisse_chemin_fr($chemin) : clarisse_chemin_en($chemin));
}

add_filter('wp_nav_menu_items', function ($liens) {
    $lien  = esc_url(clarisse_lien_autre_langue());
    $texte = clarisse_en() ? 'FR' : 'EN';
    $nom   = clarisse_en() ? 'Version française' : 'English version';
    $langue = clarisse_en() ? 'fr' : 'en';
    return $liens . '<li class="menu-item menu-langue"><a href="' . $lien . '" hreflang="' . $langue . '" lang="' . $langue . '" aria-label="' . $nom . '">' . $texte . '</a></li>';
});

/* Pour Google : chaque page indique sa version dans l'autre langue */
add_action('wp_head', function () {
    $ici    = clarisse_en() ? 'en' : 'fr';
    $autre  = clarisse_en() ? 'fr' : 'en';
    echo '<link rel="alternate" hreflang="' . $autre . '" href="' . esc_url(clarisse_lien_autre_langue()) . '">' . "\n";
}, 6);

/* Les liens du menu saisis « à la main » (ex. /projets) deviennent aussi anglais */
add_filter('nav_menu_link_attributes', function ($attributs) {
    if (!clarisse_en() || empty($attributs['href']) || strpos($attributs['href'], 'hreflang') !== false) {
        return $attributs;
    }
    $base = untrailingslashit(get_option('home'));
    $href = $attributs['href'];
    $chemin = (strpos($href, $base) === 0) ? substr($href, strlen($base)) : $href;
    if (strpos($chemin, '/') === 0 && !preg_match('#^/(en(/|$)|wp-)#', $chemin)) {
        $attributs['href'] = $base . clarisse_chemin_en($chemin);
    }
    return $attributs;
});

/* ---------------------------------------------------------
   7. Textes alternatifs des images en anglais (accessibilité)
   Rangés par nom de fichier : ça marche aussi en ligne, même si les numéros d'images changent.
   --------------------------------------------------------- */
function clarisse_alts_en()
{
    return array(
        'blog-vue-blade' => 'Blade code: loop displaying the products',
        'blog-couverture' => 'Laravel code: product routes and Product model',
        'pn-menu' => 'Philippe Noël website: full-screen mobile menu',
        'pn-mobile' => 'Philippe Noël website: hero on a smartphone',
        'pn-desktop' => 'Philippe Noël website: hero on desktop',
        'pn-couverture' => 'Philippe Noël website: desktop and smartphone versions',
        'pn-couverture-cadre' => 'Philippe Noël website: desktop and smartphone versions',
        'memia-editeur' => 'memIA: editor for the top and bottom text',
        'memia-variantes' => 'memIA: the three meme variants (absurd, ironic, relatable)',
        'memia-saisie' => 'memIA: screen for entering the situation',
        'memia-couverture' => 'memIA: the three screens of the app on mobile',
        'dds-apres' => 'Blood Donation: improved version',
        'dds-avant' => 'Blood Donation: original card',
        'dondesang-couverture' => 'Blood Donation: cover',
        'bf3' => 'Black Friday: categories',
        'bf2' => 'Black Friday: sorting',
        'bf1' => 'Black Friday: home screen',
        'blackfriday-couverture' => 'Black Friday: cover',
        'cw3' => 'Car Wash: payment',
        'cw2' => 'Car Wash: amount',
        'cw1' => 'Car Wash: identification',
        'carwash-couverture' => 'Car Wash: cover',
        'web-emotionnel' => 'AI Webinar: emotional version',
        'web-fonctionnel' => 'AI Webinar: functional version',
        'webinaire-couverture' => 'AI Webinar: cover',
        'ff-programmes' => 'FastFit: programmes',
        'ff-hero' => 'FastFit: hero',
        'fastfit-couverture' => 'FastFit: cover',
        'lb-contenus' => 'La Boucle: content',
        'lb-code-functions' => 'La Boucle: functions.php',
        'lb-code-index' => 'La Boucle: index.php',
        'laboucle-couverture' => 'La Boucle: cover',
        'lp3' => 'LingoPop: list of lessons',
        'lp2' => 'LingoPop: home screen',
        'lp1' => 'LingoPop: language choice',
        'lingopop-couverture' => 'LingoPop: cover',
        'Capture-decran-2026-09-26-141645' => 'La Clairière aux Mélodies page: nursery rhyme cards',
        'Capture-decran-2026-09-26-141715' => 'La Bibliothèque Enchantée page: illustrated story cards',
        'Capture-decran-2026-09-26-141456' => 'La Vallée du Savoir: home page of the website',
        'Capture-decran-2026-09-26-141616' => 'Les Histoires page: two cards, read or listen to a story',
        'exam-front-graphiques'   => 'Bar chart and doughnut chart made with Chart.js',
        'exam-front-slider'       => 'Full-screen carousel made with Swiper',
        'exam-front-scrollreveal' => 'Six cards that appear on scroll with ScrollReveal',
        'exam-front-alerte'       => 'Delete confirmation dialog made with SweetAlert2',
    );
}

function clarisse_alt_en($id_ou_url, $alt)
{
    $fichier = is_numeric($id_ou_url) ? get_attached_file($id_ou_url) : $id_ou_url;
    $nom = preg_replace('/(-scaled|-\d+x\d+)$/', '', pathinfo((string) $fichier, PATHINFO_FILENAME));
    $alts = clarisse_alts_en();
    return $alts[$nom] ?? $alt;
}

/* Images mises en avant (cartes, en-tête des projets) */
add_filter('wp_get_attachment_image_attributes', function ($attributs, $image) {
    if (clarisse_en()) {
        $attributs['alt'] = clarisse_alt_en($image->ID, $attributs['alt'] ?? '');
    }
    return $attributs;
}, 10, 2);

/* Images de la galerie (champ ACF) */
add_filter('acf/format_value/name=galerie', function ($images) {
    if (clarisse_en() && is_array($images)) {
        foreach ($images as $i => $image) {
            if (isset($image['ID'])) {
                $images[$i]['alt'] = clarisse_alt_en($image['ID'], $image['alt']);
            }
        }
    }
    return $images;
}, 30);

/* ---------------------------------------------------------
   7 bis. Légendes de la galerie en anglais (titre + explication sous chaque image)
   Rangées par nom de fichier, comme les textes alternatifs.
   --------------------------------------------------------- */
function clarisse_legendes_en()
{
    return array(
        'bf1' => array('The home screen', 'Search, categories as pills and popular products, with the old price crossed out.'),
        'bf2' => array('Sorting', 'The “Sort by” menu offers the five options in the brief.'),
        'bf3' => array('Categories', 'The pills scroll sideways to leave room for the products.'),
        'cw1' => array('Identifying yourself', 'The car-wash card number, with a button that becomes active once the field is filled in.'),
        'cw2' => array('Choosing the amount', 'Ready-made amounts to tap, an “other amount” option, and the chosen one outlined in yellow.'),
        'cw3' => array('Choosing how to pay', 'Six payment methods recognisable by their logo, then confirmation.'),
        'dds-avant' => array('The original card', 'Lots of information at the same level and a QR code that is awkward on a phone.'),
        'dds-apres' => array('The improved version', 'A clear hierarchy, consistent icons and two real actions: sign up or call.'),
        'ff-hero' => array('The hero', 'A strong three-word title, a coral subtitle and a single call to action.'),
        'ff-programmes' => array('The programmes', 'Three consistent cards: picture, length, level, short description and button.'),
        'lb-code-index' => array('The WordPress Loop', 'index.php: the Loop shows each item with its image, title, categories and an excerpt.'),
        'lb-code-functions' => array('The functions.php file', 'Theme settings: featured images and clean loading of the stylesheet.'),
        'lb-contenus' => array('The content', 'Twenty films, series, books, comics and manga, each with its featured image.'),
        'Capture-decran-2026-09-26-141616' => array('Two paths, depending on the mood', 'Read alone or listen to a story: each world has its own colour so children find their way easily.'),
        'Capture-decran-2026-09-26-141715' => array('Stories that appear on their own', 'A query loop shows each new story, with its illustration and an excerpt.'),
        'Capture-decran-2026-09-26-141645' => array('Same logic for the nursery rhymes', 'The same card pattern, so children keep their bearings from one world to the next.'),
        'lp1' => array('Choosing a language', 'Six languages, a level shown for each and a clearly visible selected state before moving on.'),
        'lp2' => array('Tracking progress', 'Day streak, daily goal and badges: simple markers that make you want to come back.'),
        'lp3' => array('Choosing a lesson', 'Short lessons by topic, with their length, level and progress.'),
        'web-fonctionnel' => array('Functional version', 'Clear and simple: information in a grid, a short form and an unambiguous button.'),
        'web-emotionnel' => array('Emotional version', 'Dark background, gradients and an informal tone to spark curiosity, with the same form.'),
        'exam-front-slider' => array('The Swiper carousel', 'Full screen and looping, with arrows and pagination.'),
        'exam-front-scrollreveal' => array('The ScrollReveal cards', 'Six cards that come in from the left, the bottom or the right as you scroll.'),
        'exam-front-alerte' => array('The SweetAlert2 confirmation', 'Before deleting, a dialog asks you to confirm: the action cannot be undone.'),
        'memia-saisie' => array('Describing the situation', 'One field and one button: you know straight away what to do.'),
        'memia-variantes' => array('Choosing the tone', 'Three variants, absurd, ironic or relatable, and the one you pick is outlined.'),
        'memia-editeur' => array('Editing the text', 'The top and bottom text can still be changed before the meme is laid out.'),
        'pn-desktop' => array('The desktop version', 'The mock-up\'s hero: full-screen photo, title on the left and a horizontal menu.'),
        'pn-mobile' => array('The smartphone version', 'The same hero rearranged: photo at the top, text and button below.'),
        'pn-menu' => array('The mobile menu', 'The burger button opens a full-screen menu with well-spaced links.'),
        'blog-couverture' => array('Routes and model', 'web.php sends the products to the view, and the Product model is linked to its category.'),
        'blog-vue-blade' => array('The Blade view', 'A @foreach loop shows each product in a card: category, name, excerpt, price and link.'),
    );
}

/* Galerie en anglais : légende et explication traduites */
add_filter('acf/format_value/name=galerie', function ($images) {
    if (clarisse_en() && is_array($images)) {
        $legendes = clarisse_legendes_en();
        foreach ($images as $i => $image) {
            $nom = preg_replace('/(-scaled|-\d+x\d+)$/', '', pathinfo((string) get_attached_file($image['ID'] ?? 0), PATHINFO_FILENAME));
            if (isset($legendes[$nom])) {
                $images[$i]['caption'] = $legendes[$nom][0];
                $images[$i]['description'] = $legendes[$nom][1];
            }
        }
    }
    return $images;
}, 31);

/* ---------------------------------------------------------
   8. Réparation des liens « accueil » en anglais
   Le filtre home_url (section 3) transforme l'adresse d'accueil « http://site/ » en
   « http:/en//en/site/en/ » : WordPress en a besoin tel quel pour reconnaître les adresses /en/…
   (le corriger dans le filtre casse les pages anglaises), mais le lien du logo et la balise canonical
   étaient cassés. On répare donc seulement le HTML envoyé au visiteur, juste avant l'affichage.
   --------------------------------------------------------- */
add_action('template_redirect', function () {
    if (clarisse_en()) {
        ob_start(function ($html) {
            return preg_replace('#(https?):/en//en/([^/"\'\s]+)/en/#', '$1://$2/en/', $html);
        });
    }
}, 1);
/* En anglais, la barre d'admin (visible seulement par Clarisse connectée) affichait un avertissement PHP
   venant du script « Personnaliser » de WordPress, qui lit l'adresse d'accueil : on ne le charge pas en anglais. */
add_action('wp_before_admin_bar_render', function () {
    if (clarisse_en()) {
        remove_action('wp_before_admin_bar_render', 'wp_customize_support_script');
    }
}, 0);
