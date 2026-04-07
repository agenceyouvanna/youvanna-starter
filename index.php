<?php
/**
 * Default template fallback (required by WordPress)
 * Falls back to archive-style listing for any unmatched query
 */
get_header(); ?>

<?php yv_render_hero(['title' => get_bloginfo('name')]); ?>

<section class="section">
    <div class="container">
        <?php if (have_posts()): ?>
            <div class="grid grid-3">
                <?php while (have_posts()): the_post(); ?>
                    <article class="card blog-card">
                        <a href="<?php the_permalink(); ?>" class="blog-card-link">
                            <?php if (has_post_thumbnail()): ?>
                                <?php echo get_the_post_thumbnail(null, 'card', ['loading' => 'lazy']); ?>
                            <?php else: ?>
                                <div class="blog-card-placeholder"></div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h3><?php echo esc_html(get_the_title()); ?></h3>
                                <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 20)); ?></p>
                            </div>
                        </a>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>Aucun contenu disponible.</p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>