<?php get_header(); ?>

<?php while (have_posts()): the_post(); ?>

<?php
yv_render_hero([
    'image_id' => get_post_thumbnail_id(),
    'image' => get_the_post_thumbnail_url(null, 'hero'),
    'title' => get_the_title(),
    'subtitle' => get_the_date(),
]);
?>

<article class="section article-section">
    <div class="container container-narrow">
        <div class="article-meta">
            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
            <?php
            $cats = get_the_category();
            if ($cats): ?>
                <span class="article-cat"><?php echo esc_html($cats[0]->name); ?></span>
            <?php endif; ?>
        </div>
        <div class="article-content">
            <?php the_content(); ?>
        </div>
        <div class="article-nav">
            <?php
            $prev = get_previous_post();
            $next = get_next_post();
            if ($prev): ?>
                <a href="<?php echo esc_url(get_permalink($prev)); ?>" class="btn btn-outline">&larr; <?php echo esc_html($prev->post_title); ?></a>
            <?php endif; ?>
            <?php if ($next): ?>
                <a href="<?php echo esc_url(get_permalink($next)); ?>" class="btn btn-outline"><?php echo esc_html($next->post_title); ?> &rarr;</a>
            <?php endif; ?>
        </div>
    </div>
</article>

<?php endwhile; ?>

<?php get_footer(); ?>
