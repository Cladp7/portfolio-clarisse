<?php
/**
 * Section « Projets phares » de la page d'accueil.
 * Affiche les 3 derniers projets avec les mêmes cartes que la page Projets.
 */

// Requête personnalisée : les 3 projets les plus récents
$projets_phares = new WP_Query(array(
    'post_type'      => 'projet',
    'posts_per_page' => 3,
));

if ($projets_phares->have_posts()) : ?>

    <section class="accueil-projets alignfull">
        <div class="projet__conteneur">

            <!-- En-tête de section : titre + phrase à gauche, lien « Tous les projets » à droite -->
            <div class="accueil-projets__entete">
                <div>
                    <h2 class="titre-section"><?php echo cl_t('Projets phares', 'Featured projects'); ?></h2>
                    <p class="accueil-projets__intro"><?php echo cl_t('Design, intégration et WordPress : trois projets, trois facettes.', 'Design, front-end and WordPress: three projects, three sides of my work.'); ?></p>
                </div>
                <a class="accueil-projets__tous" href="<?php echo esc_url(get_post_type_archive_link('projet')); ?>"><?php echo cl_t('Tous les projets →', 'All projects →'); ?></a>
            </div>

            <div class="grille-projets">
                <?php while ($projets_phares->have_posts()) : $projets_phares->the_post(); ?>
                    <?php get_template_part('template-parts/carte-projet', null, array('niveau' => 'h3')); ?>
                <?php endwhile; ?>
            </div>

        </div>
    </section>

    <?php wp_reset_postdata(); // on rend la main à la page d'accueil (convention de Julien : juste après la boucle) ?>

<?php endif;
