<?php
/**
 * Fallback template — WordPress redirige vers front-page.php, page.php, single.php, archive.php selon le contexte.
 * Ce fichier est requis par WordPress pour valider le thème.
 */
get_header();
if (have_posts()):
    while (have_posts()): the_post(); ?>
        <article class="section">
            <div class="container">
                <h1><?php the_title(); ?></h1>
                <div><?php the_content(); ?></div>
            </div>
        </article>
    <?php endwhile;
endif;
get_footer();
