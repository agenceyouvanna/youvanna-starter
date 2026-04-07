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
                                <img src="<?php echo esc_url(get_the_post_thumbnail_url(null, 'card')); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy">
                            <?php endif; ?>
                            <div class="card-body">
                                <h3><?php the_title(); ?></h3>
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