<?php get_header(); ?>

<?php
yv_render_hero([
    'title' => 'Recherche : ' . esc_html(get_search_query()),
    'subtitle' => sprintf('%d resultat(s) trouve(s)', $wp_query->found_posts),
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
                            <?php endif; ?>
                            <div class="card-body">
                                <div class="blog-card-meta">
                                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
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
            <div style="text-align:center;padding:48px 0;">
                <p style="font-size:1.1rem;color:var(--color-text-light);margin-bottom:24px;">Aucun resultat pour cette recherche.</p>
                <a href="<?php echo esc_url(home_url()); ?>" class="btn btn-primary">Retour a l'accueil</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>