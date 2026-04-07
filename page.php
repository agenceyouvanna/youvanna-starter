<?php get_header(); ?>

<?php
yv_render_hero([
    'image'    => yv_image('page_hero_image', 'hero') ?: get_the_post_thumbnail_url(null, 'large'),
    'title'    => yv_field('page_hero_title') ?: get_the_title(),
    'subtitle' => yv_field('page_hero_subtitle'),
]);
?>

<?php if (trim(wp_strip_all_tags(get_the_content()))): ?>
<section class="section page-content-section">
    <div class="container">
        <?php the_content(); ?>
    </div>
</section>
<?php endif; ?>

<?php if (have_rows('sections')): ?>
    <?php while (have_rows('sections')): the_row(); ?>
        <?php get_template_part('template-parts/section', get_row_layout()); ?>
    <?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>
