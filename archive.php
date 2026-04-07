<?php get_header(); ?>

<?php
yv_render_hero([
    'title'    => get_the_archive_title(),
    'subtitle' => get_the_archive_description(),
]);
?>

<section class="section blog-section">
    <div class="container">
        <?php if (have_posts()): ?>
            <div class="grid grid-3 blog-grid">
                <?php while (have_posts()): the_post(); ?>
                    <article class="card blog-card">
                        <a href="<?php the_permalink(); ?>" class="blog-card-link">
                            <?php if (has_post_thumbnail()): ?>
                                <img src="<?php echo esc_url(get_the_post_thumbnail_url(null, 'card')); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy">
                            <?php else: ?>
                                <div class="blog-card-placeholder">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="8" y1="3" x2="8" y2="15"/></svg>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <div class="blog-card-meta">
                                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                                    <?php
                                    $cats = get_the_category();
                                    if ($cats): ?>
                                        <span class="blog-card-cat"><?php echo esc_html($cats[0]->name); ?></span>
                                    <?php endif; ?>
                                </div>
                                <h3><?php the_title(); ?></h3>
                                <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 20)); ?></p>
                                <span class="card-link">Lire la suite &rarr;</span>
                            </div>
                        </a>
                    </article>
                <?php endwhile; ?>
            </div>
            <div class="pagination">
                <?php the_posts_pagination(['mid_size' => 2, 'prev_text' => '&larr;', 'next_text' => '&rarr;']); ?>
            </div>
        <?php else: ?>
            <p>Aucun article pour le moment.</p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
