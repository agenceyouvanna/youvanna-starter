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
    'image'    => yv_image('hero_image', 'hero'),
    'title'    => yv_field('hero_title', get_bloginfo('name')),
    'subtitle' => yv_field('hero_subtitle', get_bloginfo('description')),
    'buttons'  => $buttons,
    'class'    => 'hero',
]);
?>

<!-- SERVICES -->
<?php if (have_rows('services')): ?>
<section class="section services-section reveal">
    <div class="container">
        <?php yv_section_header(yv_field('services_title', 'Nos Services'), yv_field('services_subtitle')); ?>
        <div class="grid grid-3">
            <?php while (have_rows('services')): the_row();
                $img = get_sub_field('image');
                yv_render_card([
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
        <div class="about-image">
            <?php $img = yv_image('about_image'); if ($img): ?>
                <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr(yv_field('about_title')); ?>" loading="lazy">
            <?php endif; ?>
        </div>
        <div class="about-content">
            <h2><?php echo esc_html(yv_field('about_title', 'À propos')); ?></h2>
            <div><?php echo wp_kses_post(yv_field('about_text')); ?></div>
            <?php $btn = get_field('about_button'); if ($btn): ?>
                <a href="<?php echo esc_url($btn['url']); ?>" class="btn btn-primary"><?php echo esc_html($btn['title']); ?></a>
            <?php endif; ?>
            <?php yv_render_stats(get_field('stats')); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- TESTIMONIALS — marquee horizontal -->
<?php if (have_rows('testimonials')):
    $testi_items = [];
    while (have_rows('testimonials')) { the_row();
        $testi_items[] = [
            'text' => get_sub_field('text'),
            'name' => get_sub_field('name'),
            'role' => get_sub_field('role'),
            'rating' => (int) get_sub_field('rating'),
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
                        <div class="testimonial-stars"><?php for ($i = 0; $i < $t['rating']; $i++) echo '&#9733;'; ?></div>
                        <blockquote><?php echo esc_html($t['text']); ?></blockquote>
                        <div class="testimonial-author">
                            <?php if ($t['photo']): ?><img src="<?php echo esc_url($t['photo']['sizes']['thumbnail']); ?>" alt="<?php echo esc_attr($t['name']); ?>"><?php endif; ?>
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
                        <div class="testimonial-stars"><?php for ($i = 0; $i < $t['rating']; $i++) echo '&#9733;'; ?></div>
                        <blockquote><?php echo esc_html($t['text']); ?></blockquote>
                        <div class="testimonial-author">
                            <?php if ($t['photo']): ?><img src="<?php echo esc_url($t['photo']['sizes']['thumbnail']); ?>" alt="<?php echo esc_attr($t['name']); ?>"><?php endif; ?>
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
<section class="cta-banner reveal" style="background-image: url('<?php echo esc_url(yv_image('cta_background')); ?>');">
    <div class="cta-overlay"></div>
    <div class="cta-content">
        <h2><?php echo esc_html(yv_field('cta_title', 'Prêt à démarrer ?')); ?></h2>
        <p><?php echo esc_html(yv_field('cta_text_home')); ?></p>
        <a href="<?php echo esc_url(yv_field('cta_button_link', '/contact')); ?>" class="btn btn-primary"><?php echo esc_html(yv_field('cta_button_text', 'Contactez-nous')); ?></a>
    </div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
