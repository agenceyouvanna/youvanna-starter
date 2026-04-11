<?php
/**
 * Blog home template (used when a static front page is set and this page is the posts page)
 */
get_header();

$blog_page_id = get_option('page_for_posts');
$hero_image_id = get_post_meta($blog_page_id, 'page_hero_image', true);
$hero_title = get_post_meta($blog_page_id, 'page_hero_title', true) ?: 'Blog';
$hero_subtitle = get_post_meta($blog_page_id, 'page_hero_subtitle', true);

yv_render_hero([
    'image_id' => $hero_image_id,
    'title'    => $hero_title,
    'subtitle' => $hero_subtitle,
]);

// Categories filter
$categories = get_categories(['hide_empty' => true, 'exclude' => [1]]);
if ($categories): ?>
<section class="section blog-filters-section" style="padding-top: 40px; padding-bottom: 0;">
    <div class="container">
        <div class="blog-filters">
            <a href="<?php echo esc_url(get_permalink($blog_page_id)); ?>" class="blog-filter-pill<?php echo !is_category() ? ' active' : ''; ?>">Tous les articles</a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>" class="blog-filter-pill<?php echo is_category($cat->term_id) ? ' active' : ''; ?>"><?php echo esc_html($cat->name); ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section blog-section">
    <div class="container">
        <?php if (have_posts()):
            $first = true;
            $count = 0;
        ?>
            <?php while (have_posts()): the_post(); $count++; ?>
                <?php if ($first && $count === 1): $first = false; ?>
                    <!-- Featured post (first one, large) -->
                    <article class="blog-featured reveal">
                        <a href="<?php the_permalink(); ?>" class="blog-featured-link">
                            <div class="blog-featured-image">
                                <?php if (has_post_thumbnail()): ?>
                                    <?php the_post_thumbnail('large', ['loading' => 'eager']); ?>
                                <?php else: ?>
                                    <div class="blog-card-placeholder" style="height:400px;">
                                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="8" y1="3" x2="8" y2="15"/></svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="blog-featured-content">
                                <div class="blog-card-meta">
                                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                                    <?php $cats = get_the_category(); if ($cats): ?>
                                        <span class="blog-card-cat"><?php echo esc_html($cats[0]->name); ?></span>
                                    <?php endif; ?>
                                </div>
                                <h2><?php echo esc_html(get_the_title()); ?></h2>
                                <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 40)); ?></p>
                                <span class="card-link">Lire l'article <span aria-hidden="true">&rarr;</span></span>
                            </div>
                        </a>
                    </article>

                    <?php
                    // Check if there are more posts
                    $remaining = new WP_Query(['posts_per_page' => -1, 'post_type' => 'post', 'post__not_in' => [get_the_ID()]]);
                    if ($remaining->have_posts()):
                    ?>
                    <div class="blog-grid-header reveal">
                        <h2 class="section-title">Tous nos articles</h2>
                    </div>
                    <div class="grid grid-3 blog-grid">
                    <?php wp_reset_postdata(); endif; ?>

                <?php else: ?>
                    <article class="card blog-card reveal">
                        <a href="<?php the_permalink(); ?>" class="blog-card-link">
                            <?php if (has_post_thumbnail()): ?>
                                <?php the_post_thumbnail('card', ['loading' => 'lazy']); ?>
                            <?php else: ?>
                                <div class="blog-card-placeholder">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="8" y1="3" x2="8" y2="15"/></svg>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <div class="blog-card-meta">
                                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                                    <?php $cats = get_the_category(); if ($cats): ?>
                                        <span class="blog-card-cat"><?php echo esc_html($cats[0]->name); ?></span>
                                    <?php endif; ?>
                                </div>
                                <h3><?php echo esc_html(get_the_title()); ?></h3>
                                <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 20)); ?></p>
                                <span class="card-link">Lire la suite <span aria-hidden="true">&rarr;</span></span>
                            </div>
                        </a>
                    </article>
                <?php endif; ?>
            <?php endwhile; ?>
            </div>

            <div class="pagination">
                <?php the_posts_pagination(['mid_size' => 2, 'prev_text' => '&larr;', 'next_text' => '&rarr;']); ?>
            </div>
        <?php else: ?>
            <div class="blog-empty reveal">
                <i class="fa-solid fa-pen-nib" style="font-size: 3rem; color: var(--color-primary); margin-bottom: 20px;"></i>
                <h2>Nos articles arrivent bientôt</h2>
                <p>Nous préparons des guides pratiques pour vous aider dans votre stratégie digitale. Revenez nous voir prochainement.</p>
                <a href="/contact/" class="btn btn-primary">Contactez-nous en attendant</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Blog CTA -->
<section class="section blog-cta-section reveal">
    <div class="container">
        <div class="blog-cta-card">
            <div class="blog-cta-content">
                <h2>Un projet web en tête ?</h2>
                <p>Nos experts vous accompagnent de la conception au lancement. Premier échange gratuit et sans engagement.</p>
                <div class="blog-cta-buttons">
                    <a href="/contact/" class="btn btn-primary">Discuter de votre projet</a>
                    <a href="/services/" class="btn btn-outline">Nos services</a>
                </div>
            </div>
            <div class="blog-cta-stats">
                <div class="blog-cta-stat">
                    <span class="stat-number">24h</span>
                    <span>Réponse garantie</span>
                </div>
                <div class="blog-cta-stat">
                    <span class="stat-number">0</span>
                    <span>Engagement requis</span>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Render SCF sections if any exist on the blog page
if (function_exists('have_rows') && have_rows('sections', $blog_page_id)):
    while (have_rows('sections', $blog_page_id)): the_row();
        get_template_part('template-parts/section', get_row_layout());
    endwhile;
endif;
?>

<?php get_footer(); ?>
