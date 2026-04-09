<?php get_header(); ?>

<?php
// HERO — réutilise le helper
$buttons = [];
if ($t = yv_field('hero_cta1_text')) {
    $buttons[] = ['text' => $t, 'url' => yv_field('hero_cta1_link', '#'), 'class' => 'btn-primary'];
}
if ($t = yv_field('hero_cta2_text')) {
    $buttons[] = ['text' => $t, 'url' => yv_field('hero_cta2_link', '#'), 'class' => 'btn-secondary'];
}
yv_render_hero([
    'image_id' => yv_image_id('hero_image'),
    'image'    => yv_image('hero_image', 'hero'),
    'title'    => yv_field('hero_title', get_bloginfo('name')),
    'subtitle' => yv_field('hero_subtitle', get_bloginfo('description')),
    'buttons'  => $buttons,
    'class'    => 'hero',
]);
?>

<!-- SERVICES -->
<?php if (function_exists('have_rows') && have_rows('services')):
    $services_count = 0;
    while (have_rows('services')): the_row(); $services_count++; endwhile;
    $grid_class = 'grid-' . min(max($services_count, 2), 5);
    $container_class = $services_count >= 5 ? 'container container-wide' : 'container';
?>
<section class="section services-section reveal">
    <div class="<?php echo esc_attr($container_class); ?>">
        <?php yv_section_header(yv_field('services_title', 'Nos Services'), yv_field('services_subtitle')); ?>
        <div class="grid <?php echo esc_attr($grid_class); ?> services-grid-tall">
            <?php while (have_rows('services')): the_row();
                $img = get_sub_field('image');
                yv_render_card([
                    'image_id' => $img ? ($img['ID'] ?? 0) : 0,
                    'image' => $img ? ($img['sizes']['card'] ?? $img['url']) : '',
                    'icon'  => get_sub_field('icon'),
                    'title' => get_sub_field('title'),
                    'text'  => get_sub_field('description'),
                    'link'  => get_sub_field('link'),
                ]);
            endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ABOUT -->
<?php if (yv_field('about_title') || yv_field('about_text')): ?>
<section class="section about-section reveal">
    <div class="container grid grid-2">
        <div class="about-image image-frame">
            <?php echo yv_img('about_image', 'large', false, ['alt' => esc_attr(yv_field('about_title'))]); ?>
            <?php $badge_num = yv_field('about_badge_number'); if ($badge_num): ?>
                <div class="image-stat-badge is-bottom-right">
                    <?php $badge_ico = yv_field('about_badge_icon', 'fa-solid fa-medal'); ?>
                    <div class="image-stat-badge__icon"><i class="<?php echo esc_attr($badge_ico); ?>"></i></div>
                    <div class="image-stat-badge__text">
                        <span class="image-stat-badge__number stat-number"><?php echo esc_html($badge_num); ?></span>
                        <span class="image-stat-badge__label"><?php echo esc_html(yv_field('about_badge_label', '')); ?></span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="about-content">
            <h2><?php echo yv_format_title(yv_field('about_title', 'À propos')); ?></h2>
            <div><?php echo wp_kses_post(yv_field('about_text')); ?></div>
            <?php $btn = function_exists('get_field') ? get_field('about_button') : null; if (is_array($btn) && !empty($btn['url'])): ?>
                <a href="<?php echo esc_url($btn['url']); ?>" class="btn btn-primary"><?php echo esc_html($btn['title'] ?? 'En savoir plus'); ?></a>
            <?php endif; ?>
            <?php yv_render_stats(function_exists('get_field') ? get_field('stats') : null); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- FLEXIBLE SECTIONS (home extras — zones, marques, FAQ, etc) -->
<?php if (function_exists('have_rows') && have_rows('sections')): ?>
    <?php while (have_rows('sections')): the_row(); ?>
        <?php get_template_part('template-parts/section', get_row_layout()); ?>
    <?php endwhile; ?>
<?php endif; ?>

<!-- TESTIMONIALS — marquee horizontal -->
<?php if (function_exists('have_rows') && have_rows('testimonials')):
    $testi_items = [];
    while (have_rows('testimonials')) { the_row();
        $testi_items[] = [
            'text' => get_sub_field('text'),
            'name' => get_sub_field('name'),
            'role' => get_sub_field('role'),
            'rating' => max(0, min(5, (int) get_sub_field('rating'))),
            'photo' => get_sub_field('photo'),
        ];
    }
    $use_marquee = count($testi_items) > 3;
?>
<section class="section testimonials-section reveal">
    <div class="container">
        <?php yv_section_header(yv_field('testimonials_title', 'Ce que disent nos clients')); ?>
    </div>
    <?php if ($use_marquee): ?>
        <div class="testimonials-marquee">
            <div class="marquee-track" data-direction="left">
                <?php foreach (array_merge($testi_items, $testi_items) as $t): ?>
                    <div class="testimonial-card">
                        <div class="testimonial-stars" aria-label="<?php echo esc_attr($t['rating']); ?> sur 5" role="img"><?php for ($i = 0; $i < $t['rating']; $i++) echo '&#9733;'; ?></div>
                        <blockquote><?php echo wp_kses_post($t['text']); ?></blockquote>
                        <div class="testimonial-author">
                            <?php if ($t['photo'] && !empty($t['photo']['ID'])): ?><?php echo wp_get_attachment_image($t['photo']['ID'], 'thumbnail', false, ['loading' => 'lazy', 'alt' => esc_attr($t['name'])]); ?><?php endif; ?>
                            <div><strong><?php echo esc_html($t['name']); ?></strong><?php if ($t['role']): ?><span><?php echo esc_html($t['role']); ?></span><?php endif; ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="container">
            <div class="grid grid-3">
                <?php foreach ($testi_items as $t): ?>
                    <div class="testimonial-card">
                        <div class="testimonial-stars" aria-label="<?php echo esc_attr($t['rating']); ?> sur 5" role="img"><?php for ($i = 0; $i < $t['rating']; $i++) echo '&#9733;'; ?></div>
                        <blockquote><?php echo wp_kses_post($t['text']); ?></blockquote>
                        <div class="testimonial-author">
                            <?php if ($t['photo'] && !empty($t['photo']['ID'])): ?><?php echo wp_get_attachment_image($t['photo']['ID'], 'thumbnail', false, ['loading' => 'lazy', 'alt' => esc_attr($t['name'])]); ?><?php endif; ?>
                            <div><strong><?php echo esc_html($t['name']); ?></strong><?php if ($t['role']): ?><span><?php echo esc_html($t['role']); ?></span><?php endif; ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</section>
<?php endif; ?>

<!-- CTA BANNER -->
<?php if (yv_field('cta_title')): ?>
<section class="cta-banner reveal">
    <?php $cta_bg_id = yv_image_id('cta_background'); if ($cta_bg_id): ?>
        <?php echo wp_get_attachment_image($cta_bg_id, 'hero', false, ['class' => 'hero-bg-img', 'loading' => 'lazy', 'alt' => '']); ?>
    <?php endif; ?>
    <div class="cta-overlay"></div>
    <div class="cta-content">
        <h2><?php echo yv_format_title(yv_field('cta_title', 'Prêt à démarrer ?')); ?></h2>
        <div class="cta-text"><?php echo wp_kses_post(yv_field('cta_text_home')); ?></div>
        <a href="<?php echo esc_url(yv_field('cta_button_link', '/contact')); ?>" class="btn btn-primary"><?php echo esc_html(yv_field('cta_button_text', 'Contactez-nous')); ?></a>
    </div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
