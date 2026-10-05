<?php
/**
 * Page « Projets » : la liste de tous mes projets.
 * WordPress l'utilise automatiquement grâce à son nom : archive-{type de contenu}.php
 */

get_header();
?>

<div id="main" class="projet projets"> <!-- id="main" : cible du lien « Aller au contenu » (accessibilité) -->

    <!-- En-tête de la page -->
    <section class="projet__entete">
        <div class="projet__conteneur">
            <h1 class="projet__titre"><?php echo cl_t('Mes projets', 'My projects'); ?></h1>
            <p class="projets__intro"><?php echo cl_t('Sites, applications et maquettes réalisés pendant ma formation en web design.', 'Websites, apps and mock-ups created during my web design training.'); ?></p>
        </div>
    </section>

    <!-- La grille de cartes -->
    <section class="projet__section">
        <div class="projet__conteneur">
            <?php if (have_posts()) : ?>
                <div class="grille-projets">
                    <?php while (have_posts()) : the_post(); // la Boucle principale : tous les projets ?>
                        <?php get_template_part('template-parts/carte-projet'); ?>
                    <?php endwhile; ?>
                </div>
            <?php else : ?>
                <p><?php echo cl_t('Les projets arrivent bientôt ✨', 'Projects coming soon ✨'); ?></p>
            <?php endif; ?>
        </div>
    </section>

</div>

<?php get_footer(); ?>
