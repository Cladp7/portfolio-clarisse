<?php

/**
 * Modèle d'une fiche projet.
 * WordPress l'utilise automatiquement grâce à son nom :
 * single-{clé du type de contenu}.php → single-projet.php
 */

get_header(); // affiche l'en-tête de Kadence (ton nom + le menu)
?>

<main class="projet">

    <?php while (have_posts()) : the_post(); // la Boucle : ici, elle ne contient qu'un seul projet 
    ?>

        <!-- =====================================================
         1. EN-TÊTE DU PROJET
         ===================================================== -->
        <section class="projet__entete">
            <div class="projet__conteneur">

                <!-- Fil d'Ariane : « Projets / Nom du projet » -->
                <p class="projet__ariane">
                    <!-- get_post_type_archive_link('projet') = l'adresse de la page /projets/ -->
                    <a href="<?php echo esc_url(get_post_type_archive_link('projet')); ?>"><?php echo cl_t('Projets', 'Projects'); ?></a>
                    / <?php the_title(); ?>
                </p>

                <?php if (get_field('etiquette')) : // on affiche le badge seulement s'il est rempli 
                ?>
                    <?php $couleur = get_field('couleur_carte') ? get_field('couleur_carte') : 'violet'; // même couleur que la carte = la catégorie du projet ?>
                    <span class="etiquette etiquette--<?php echo esc_attr($couleur); ?>"><?php echo esc_html(get_field('etiquette')); ?></span>
                <?php endif; ?>

                <h1 class="projet__titre"><?php the_title(); ?></h1>

                <?php if (has_excerpt()) : // l'extrait sert d'introduction 
                ?>
                    <p class="projet__intro"><?php echo esc_html(get_the_excerpt()); ?></p>
                <?php endif; ?>

                <?php if (has_post_thumbnail()) : // la grande capture du projet 
                ?>
                    <div class="projet__visuel">
                        <?php the_post_thumbnail('full'); // 'full' = l'image en taille originale 
                        ?>
                    </div>
                <?php endif; ?>

            </div>
        </section>

        <!-- =====================================================
         2. CONTEXTE, OBJECTIF ET OUTILS
         ===================================================== -->
        <section class="projet__section">
            <div class="projet__conteneur projet__colonnes">

                <div class="projet__texte">
                    <h2 class="titre-section"><?php echo cl_t('Le contexte', 'Context'); ?></h2>
                    <?php // wp_kses_post : garde les <p> créés par ACF, mais retire tout code dangereux 
                    ?>
                    <?php echo wp_kses_post(get_field('contexte')); ?>

                    <h2 class="titre-section"><?php echo cl_t('L’objectif', 'Goal'); ?></h2>
                    <?php echo wp_kses_post(get_field('objectif')); ?>
                </div>

                <?php $outils = get_field('outils'); // un tableau, ex. ['WordPress', 'HTML/CSS'] 
                ?>
                <?php if ($outils) : ?>
                    <aside class="projet__encadre">
                        <h3 class="projet__encadre-titre"><?php echo cl_t('Outils et techniques', 'Tools and techniques'); ?></h3>
                        <ul class="pastilles">
                            <?php foreach ($outils as $outil) : // une pastille par outil coché 
                            ?>
                                <li class="pastille"><?php echo esc_html($outil); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </aside>
                <?php endif; ?>

            </div>
        </section>

        <!-- =====================================================
         3. LA DÉMARCHE (répéteur « etapes »)
         ===================================================== -->
        <?php $etapes = get_field('etapes'); // toutes les étapes d'un coup (traduites sur /en/) ?>
        <?php if ($etapes) : // seulement s'il y a au moins une étape 
        ?>
            <section class="projet__section">
                <div class="projet__conteneur">
                    <h2 class="titre-section"><?php echo cl_t('La démarche', 'Process'); ?></h2>
                    <ol class="etapes">
                        <?php foreach ($etapes as $etape) : // une ligne du répéteur = une étape 
                        ?>
                            <li class="etape">
                                <h3 class="etape__titre"><?php echo esc_html($etape['titre_etape']); ?></h3>
                                <div class="etape__texte"><?php echo wp_kses_post(wpautop($etape['description_etape'])); ?></div>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </section>
        <?php endif; ?>

        <!-- =====================================================
         4. EN IMAGES (galerie expliquée)
         ===================================================== -->
        <?php $images = get_field('galerie'); // un tableau d'images 
        ?>
        <?php if ($images) : ?>
            <section class="projet__section">
                <div class="projet__conteneur">
                    <h2 class="titre-section"><?php echo cl_t('En images', 'In pictures'); ?></h2>
                    <div class="galerie" data-titre="<?php echo esc_attr(cl_t('Visionneuse des images', 'Image viewer')); ?>" data-fermer="<?php echo esc_attr(cl_t('Fermer', 'Close')); ?>" data-precedent="<?php echo esc_attr(cl_t('Image précédente', 'Previous image')); ?>" data-suivant="<?php echo esc_attr(cl_t('Image suivante', 'Next image')); ?>">
                        <?php foreach ($images as $image) : ?>
                            <figure class="galerie__item<?php echo (!empty($image['height']) && $image['height'] > $image['width']) ? ' galerie__item--portrait' : ''; // capture en hauteur (téléphone) ?>">

                                <!-- L'image : clic = ouverture en grand dans la visionneuse (js/visionneuse.js) -->
                                <a class="galerie__image" href="<?php echo esc_url($image['url']); ?>" aria-label="<?php echo esc_attr(cl_t('Agrandir : ', 'Enlarge: ') . $image['alt']); ?>">
                                    <img src="<?php echo esc_url($image['sizes']['large']); ?>"
                                        alt="<?php echo esc_attr($image['alt']); ?>"
                                        loading="lazy"> <!-- lazy : l'image se charge seulement quand on arrive dessus -->
                                </a>

                                <?php if ($image['caption'] || $image['description']) : // || veut dire « ou » 
                                ?>
                                    <figcaption class="galerie__texte">
                                        <?php if ($image['caption']) : // la Légende → le titre 
                                        ?>
                                            <h3 class="galerie__titre"><?php echo esc_html($image['caption']); ?></h3>
                                        <?php endif; ?>
                                        <?php if ($image['description']) : // la Description → l'explication 
                                        ?>
                                            <p><?php echo esc_html($image['description']); ?></p>
                                        <?php endif; ?>
                                    </figcaption>
                                <?php endif; ?>

                            </figure>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- =====================================================
         5. CE QUE J'AI APPRIS
         ===================================================== -->
        <?php if (get_field('apprentissages')) : ?>
            <section class="projet__section">
                <div class="projet__conteneur">
                    <div class="projet__appris">
                        <h2 class="projet__appris-titre"><?php echo cl_t('Ce que j’ai appris', 'What I learned'); ?></h2>
                        <div class="projet__appris-texte"><?php echo wp_kses_post(get_field('apprentissages')); ?></div> <!-- conteneur pour couler le texte sur 2 colonnes -->
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- =====================================================
         6. LIENS ET PROJET SUIVANT
         ===================================================== -->
        <section class="projet__section">
            <div class="projet__conteneur projet__liens">

                <div class="projet__actions">
                    <?php if (get_field('lien_site')) : // bouton affiché SEULEMENT si le lien existe 
                    ?>
                        <a class="bouton bouton--primaire" href="<?php echo esc_url(get_field('lien_site')); ?>" target="_blank" rel="noopener"><?php echo (strpos(get_field('lien_site'), 'github.com') !== false) ? cl_t('Voir le code sur GitHub', 'View the code on GitHub') : cl_t('Voir le site en ligne', 'Visit the live site'); // lien GitHub = projet de code ?></a>
                    <?php endif; ?>
                    <?php if (get_field('lien_figma')) : ?>
                        <a class="bouton bouton--secondaire" href="<?php echo esc_url(get_field('lien_figma')); ?>" target="_blank" rel="noopener"><?php echo cl_t('Voir la maquette Figma', 'View the Figma mock-up'); ?></a>
                    <?php endif; ?>
                </div>

                <?php
                // « Projet suivant » = le projet affiché juste après dans la liste (du plus récent au plus ancien).
                // Arrivé au dernier, on repart au premier : la navigation fait une boucle, aucun projet n'est un cul-de-sac.
                $suivant = get_previous_post();
                if (!$suivant) {
                    $premier = get_posts(array('post_type' => 'projet', 'numberposts' => 1, 'orderby' => 'date', 'order' => 'DESC'));
                    $suivant = ($premier && $premier[0]->ID !== get_the_ID()) ? $premier[0] : null;
                }
                ?>
                <?php if ($suivant) : ?>
                    <a class="projet__suivant" href="<?php echo esc_url(get_permalink($suivant)); ?>">
                        <span class="projet__suivant-label"><?php echo cl_t('Projet suivant', 'Next project'); ?></span>
                        <?php echo esc_html(get_the_title($suivant)); ?> →
                    </a>
                <?php endif; ?>

            </div>
        </section>

        <!-- =====================================================
         7. APPEL AU CONTACT (rapport du 03/10 : chaque étude de cas finit sur une action)
         Même bloc que sur « Mon parcours ».
         ===================================================== -->
        <section class="projet__section">
            <div class="projet__conteneur">
                <div class="parcours__appel projet__appel">
                    <h2 class="parcours__appel-titre"><?php echo cl_t('Envie de travailler ensemble ?', 'Shall we work together?'); ?></h2>
                    <p><?php echo cl_t('Je cherche un stage de 200 heures, dès le 13 octobre 2026, idéalement dans l’éducatif.', 'I am looking for a 200-hour internship from 13 October 2026, ideally in education.'); ?></p>
                    <div class="projet__actions">
                        <a class="bouton bouton--primaire" href="<?php echo esc_url(get_permalink(get_page_by_path('contact'))); ?>"><?php echo cl_t('Me contacter', 'Contact me'); ?></a>
                        <a class="bouton bouton--secondaire" href="<?php echo esc_url(get_post_type_archive_link('projet')); ?>"><?php echo cl_t('Voir tous mes projets', 'See all my projects'); ?></a>
                    </div>
                </div>
            </div>
        </section>

    <?php endwhile; ?>

</main>

<?php get_footer(); // affiche le pied de page de Kadence 
?>