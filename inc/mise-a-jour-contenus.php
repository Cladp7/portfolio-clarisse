<?php
/**
 * MISE À JOUR DES CONTENUS (une seule fois par site)
 *
 * Ce que ça fait : ajoute les nouvelles images (dossier « mise-a-jour/ » du thème) dans la
 * médiathèque, puis met à jour 5 projets :
 *   - La Boucle et Y a un truc qui blog : nouvelle couverture + nouvelles images de la galerie
 *   - La Vallée du Savoir et Une page, cinq bibliothèques JS : couverture au même format que les autres
 *   - Black Friday : précise que c'était le tout premier travail (français + anglais)
 *   - l'icône de l'onglet du navigateur : le nouveau logo
 *
 * Quand : à la première visite du tableau de bord par une administratrice, en local puis en ligne.
 * Ensuite, une option « clarisse_maj_2026_10_06 » est enregistrée : le code ne refait plus rien.
 * Une fois les deux sites à jour, ce fichier et le dossier « mise-a-jour/ » peuvent être supprimés.
 */

add_action('admin_init', function () {
    try {
        clarisse_mise_a_jour_contenus();
    } catch (Throwable $erreur) {                     // en cas de souci : rien ne casse, le tableau de bord reste accessible
        error_log('Mise à jour des contenus : ' . $erreur->getMessage());
    }
});

function clarisse_mise_a_jour_contenus()
{
    if (get_option('clarisse_maj_2026_10_06') || !current_user_can('manage_options') || !function_exists('update_field')) {
        return;
    }

    // Les outils de WordPress pour ajouter une image à la médiathèque
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    // 1. Les nouvelles images : nom du fichier => texte alternatif, légende (titre), description
    $images = array(
        'lb-accueil'               => array('La Boucle : page d’accueil avec le logo, le menu et les derniers contenus', 'L’accueil', 'Le logo, le menu des catégories, la recherche et les derniers contenus en cartes.'),
        'lb-cartes'                => array('La Boucle : cartes de films et de séries', 'Les cartes de contenu', 'Une même template part partout : image mise en avant, titre, catégorie, extrait et lien « Lire la suite ».'),
        'laboucle-couverture-site' => array('La Boucle : l’accueil et les cartes de contenu', '', ''),
        'blog-accueil'             => array('Y a un truc qui blog : page d’accueil du blog', 'L’accueil du blog', 'Le blog fourni par le professeur, avec l’entrée « Produits » dans le menu, qui mène à la boutique.'),
        'blog-produit'             => array('Y a un truc qui blog : page détail d’un produit', 'La page détail d’un produit', 'Nom, prix, image et catégorie, avec des produits suggérés, dans le gabarit du blog.'),
        'blog-couverture-site'     => array('Y a un truc qui blog : l’accueil et une page produit', '', ''),
        'vallee-couverture'        => array('La Vallée du Savoir : page d’accueil du site', '', ''),
        'cinqjs-couverture'        => array('Une page, cinq bibliothèques JS : le carrousel et les graphiques', '', ''),
        'icone-miss-clarisse'      => array('Miss Clarisse', '', ''),
    );
    $ids = array();
    foreach ($images as $nom => $textes) {
        $ids[$nom] = clarisse_image_mediatheque($nom, $textes);
        if (!$ids[$nom]) {
            return;                                   // un problème : on réessaiera à la prochaine visite
        }
    }

    // 2. Couvertures et galeries
    clarisse_maj_projet('la-boucle', $ids['laboucle-couverture-site'], array($ids['lb-accueil'], $ids['lb-cartes'], clarisse_image_existante('lb-contenus')));
    clarisse_maj_projet('y-a-un-truc-qui-blog', $ids['blog-couverture-site'], array($ids['blog-accueil'], $ids['blog-produit'], clarisse_image_existante('blog-vue-blade')));
    clarisse_maj_projet('la-vallee-du-savoir', $ids['vallee-couverture']);
    clarisse_maj_projet('cinq-bibliotheques-js', $ids['cinqjs-couverture']);

    // L'icône de l'onglet du navigateur : le nouveau logo
    update_option('site_icon', $ids['icone-miss-clarisse']);

    // 3. Black Friday : le tout premier travail
    $bf = get_page_by_path('black-friday', OBJECT, 'projet');
    if ($bf) {
        wp_update_post(array(
            'ID'           => $bf->ID,
            'post_excerpt' => 'Mon tout premier projet de design : le prototype d’une application de shopping pour le Black Friday, avec recherche, catégories, tri et filtres.',
        ));
        $contexte = (string) get_field('contexte', $bf->ID, false);
        if (strpos($contexte, 'premier travail') === false) {
            update_field('contexte', trim($contexte) . ' C’est le tout premier travail que j’ai réalisé en formation : un prototype encore sommaire, que je garde pour montrer le chemin parcouru.', $bf->ID);
        }
        $en = clarisse_traduction($bf->ID);
        if ($en) {
            $en['excerpt'] = 'My very first design project: the prototype of a shopping app for Black Friday, with search, categories, sorting and filters.';
            if (strpos($en['contexte'] ?? '', 'very first') === false) {
                $en['contexte'] = trim($en['contexte'] ?? '') . ' It was the very first piece of work I did in my training: a still basic prototype, which I keep to show how far I have come.';
            }
            update_post_meta($bf->ID, 'traduction_en', wp_slash(wp_json_encode($en, JSON_UNESCAPED_UNICODE)));
        }
    }

    update_option('clarisse_maj_2026_10_06', current_time('mysql'));
}

/* Retrouve une image déjà dans la médiathèque grâce à son nom de fichier (0 si absente) */
function clarisse_image_existante($nom)
{
    global $wpdb;
    return (int) $wpdb->get_var($wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s ORDER BY post_id DESC LIMIT 1",
        '%/' . $wpdb->esc_like($nom) . '.webp'
    ));
}

/* Ajoute une image du dossier « mise-a-jour/ » à la médiathèque (sauf si elle y est déjà) */
function clarisse_image_mediatheque($nom, $textes)
{
    $id = clarisse_image_existante($nom);
    if (!$id) {
        $source = get_stylesheet_directory() . '/mise-a-jour/' . $nom . '.webp';
        $copie  = wp_tempnam($nom . '.webp');
        if (!file_exists($source) || !copy($source, $copie)) {
            return 0;
        }
        $id = media_handle_sideload(array('name' => $nom . '.webp', 'tmp_name' => $copie), 0);
        if (is_wp_error($id)) {
            @unlink($copie);
            return 0;
        }
    }
    update_post_meta($id, '_wp_attachment_image_alt', $textes[0]);
    wp_update_post(array('ID' => $id, 'post_excerpt' => $textes[1], 'post_content' => $textes[2]));
    return $id;
}

/* Change la couverture (image mise en avant) et, si on la donne, la galerie d'un projet */
function clarisse_maj_projet($slug, $couverture, $galerie = null)
{
    $projet = get_page_by_path($slug, OBJECT, 'projet');
    if (!$projet) {
        return;
    }
    set_post_thumbnail($projet->ID, $couverture);
    if ($galerie) {
        update_field('galerie', array_values(array_filter($galerie)), $projet->ID);
    }
}
