<?php
/**
 * Carte d'un projet (réutilisée sur la page Projets ET plus tard sur l'accueil).
 * S'utilise dans une Boucle : get_template_part('template-parts/carte-projet');
 */

// Niveau du titre : h2 sur la page Projets, h3 dans la section de l'accueil (sous un h2)
$niveau = isset($args['niveau']) ? $args['niveau'] : 'h2';

// Couleur choisie dans ACF (violet, magenta ou orange) → devient une classe CSS
$couleur = get_field('couleur_carte') ? get_field('couleur_carte') : 'violet';
?>

<article class="carte-projet carte-projet--<?php echo esc_attr($couleur); ?>">

    <!-- L'image du projet (image mise en avant), format 'large' : légère et nette -->
    <div class="carte-projet__image">
        <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('large'); ?>
        <?php endif; ?>
    </div>

    <!-- Le bas coloré de la carte -->
    <div class="carte-projet__contenu">
        <?php if (get_field('etiquette')) : ?>
            <span class="etiquette etiquette--<?php echo esc_attr($couleur); ?>"><?php echo esc_html(get_field('etiquette')); ?></span>
        <?php endif; ?>

        <<?php echo esc_attr($niveau); ?> class="carte-projet__titre">
            <!-- Le lien couvre toute la carte grâce au CSS (::after) : on peut cliquer n'importe où -->
            <a class="carte-projet__lien" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </<?php echo esc_attr($niveau); ?>>

        <?php if (has_excerpt()) : ?>
            <p class="carte-projet__texte"><?php echo esc_html(get_the_excerpt()); ?></p>
        <?php endif; ?>

        <span class="carte-projet__voir" aria-hidden="true"><?php echo cl_t('Voir le projet →', 'View project →'); ?></span>
    </div>

</article>
