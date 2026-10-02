<?php
/**
 * Page « Projets » : la liste de tous mes projets.
 * WordPress l'utilise automatiquement grâce à son nom : archive-{type de contenu}.php
 */

get_header();
?>

<main class="projet projets">

    <!-- En-tête de la page -->
    <section class="projet__entete">
        <div class="projet__conteneur">
            <h1 class="projet__titre">Mes projets</h1>
            <p class="projets__intro">Sites, applications et maquettes réalisés pendant ma formation en web design.</p>
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
                <p>Les projets arrivent bientôt ✨</p>
            <?php endif; ?>
        </div>
    </section>

</main>

<?php get_footer(); ?>
