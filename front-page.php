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
    'badge'    => yv_field('hero_badge', 'Agence web Le Mans'),
    'wave'     => false,
]);
?>

<!-- TECH / PARTNER LOGO BAND -->
<?php if (function_exists('have_rows') && have_rows('tech_logos')): ?>
<section class="tech-band" aria-label="Technologies et outils">
    <div class="container">
        <div class="tech-band__track">
            <?php while (have_rows('tech_logos')): the_row(); ?>
                <div class="tech-band__item">
                    <i class="<?php echo esc_attr(get_sub_field('icon')); ?>" aria-hidden="true"></i>
                    <span><?php echo esc_html(get_sub_field('label')); ?></span>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- FEATURED SERVICES — Alternating text+image blocks -->
<?php if (function_exists('have_rows') && have_rows('services')): ?>
<section class="section featured-services reveal">
    <div class="container">
        <?php yv_section_header(yv_field('services_title', 'Nos Services'), yv_field('services_subtitle')); ?>
    </div>
    <?php
    $svc_index = 0;
    while (have_rows('services')): the_row();
        $svc_index++;
        if ($svc_index > 3) break;
        $img = get_sub_field('image');
        $link = get_sub_field('link');
        $link_url = '';
        if (is_array($link) && !empty($link['url'])) {
            $link_url = $link['url'];
        }
        $reversed = ($svc_index % 2 === 0) ? ' fs-block--reversed' : '';
    ?>
    <div class="fs-block<?php echo esc_attr($reversed); ?>">
        <div class="container">
            <div class="fs-block__inner">
                <div class="fs-block__image">
                    <?php if ($img && !empty($img['ID'])): ?>
                        <?php echo wp_get_attachment_image($img['ID'], 'large', false, [
                            'loading' => 'lazy',
                            'alt' => esc_attr(get_sub_field('title')),
                        ]); ?>
                    <?php endif; ?>
                </div>
                <div class="fs-block__content">
                    <span class="fs-block__number"><?php echo esc_html(str_pad($svc_index, 2, '0', STR_PAD_LEFT)); ?></span>
                    <h3><?php echo esc_html(get_sub_field('title')); ?></h3>
                    <p><?php echo wp_kses_post(get_sub_field('description')); ?></p>
                    <?php if ($link_url): ?>
                        <a href="<?php echo esc_url($link_url); ?>" class="btn btn-outline" aria-label="En savoir plus sur <?php echo esc_attr(get_sub_field('title')); ?>">
                            <?php echo esc_html(is_array($link) && !empty($link['title']) ? $link['title'] : 'En savoir plus'); ?> <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
    <div class="fs-cta-wrap">
        <a href="<?php echo esc_url(home_url('/services/')); ?>" class="btn btn-primary btn-lg">
            Découvrir tous nos services <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
        </a>
    </div>
</section>
<?php endif; ?>

<!-- MARQUEE BAND -->
<?php if (function_exists('have_rows') && have_rows('marquee_items')): ?>
<div class="marquee-band" aria-hidden="true">
    <div class="marquee-band-track">
        <?php
        $marquee_texts = [];
        while (have_rows('marquee_items')): the_row();
            $marquee_texts[] = get_sub_field('text');
        endwhile;
        // Double pour le scroll infini
        foreach (array_merge($marquee_texts, $marquee_texts) as $item): ?>
            <span class="marquee-band-item"><?php echo esc_html($item); ?></span>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- PROCESS / METHODOLOGY -->
<?php if (function_exists('have_rows') && have_rows('process_steps')): ?>
<section class="section process-section reveal">
    <div class="container">
        <?php yv_section_header(yv_field('process_title', 'Notre méthodologie'), yv_field('process_subtitle')); ?>
        <div class="process-steps">
            <?php $step_i = 0; while (have_rows('process_steps')): the_row(); $step_i++; ?>
                <div class="process-step">
                    <div class="process-step__icon">
                        <i class="<?php echo esc_attr(get_sub_field('icon') ?: 'fa-solid fa-check'); ?>" aria-hidden="true"></i>
                    </div>
                    <span class="process-step__number"><?php echo esc_html(str_pad($step_i, 2, '0', STR_PAD_LEFT)); ?></span>
                    <h3 class="process-step__title"><?php echo esc_html(get_sub_field('title')); ?></h3>
                    <p class="process-step__text"><?php echo esc_html(get_sub_field('text')); ?></p>
                </div>
            <?php endwhile; ?>
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

<!-- PORTFOLIO / RÉALISATIONS PREVIEW -->
<?php
// Query featured realisations from CPT. Fallback to 3 most recent if no featured flag set.
$featured_realisations = get_posts([
    'post_type'      => 'realisation',
    'posts_per_page' => 3,
    'post_status'    => 'publish',
    'meta_key'       => 'is_featured',
    'meta_value'     => '1',
    'orderby'        => 'menu_order date',
    'order'          => 'ASC',
]);
if (empty($featured_realisations)) {
    $featured_realisations = get_posts([
        'post_type'      => 'realisation',
        'posts_per_page' => 3,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order date',
        'order'          => 'ASC',
    ]);
}
if (!empty($featured_realisations)): ?>
<section class="section portfolio-preview reveal">
    <div class="container">
        <?php yv_section_header('Nos réalisations', 'Des projets qui parlent d\'eux-mêmes'); ?>
        <div class="portfolio-grid">
            <?php foreach ($featured_realisations as $r):
                $r_client   = get_post_meta($r->ID, 'client_name', true) ?: get_the_title($r);
                $r_category = get_post_meta($r->ID, 'category', true) ?: 'Projet';
                $r_from     = get_post_meta($r->ID, 'gradient_from', true) ?: '#0f172a';
                $r_to       = get_post_meta($r->ID, 'gradient_to', true) ?: '#1e40af';
                $r_initials = mb_substr(preg_replace('/[^A-Za-zÀ-ÿ]/u', '', $r_client), 0, 2);
            ?>
            <div class="portfolio-card">
                <a href="<?php echo esc_url(get_permalink($r)); ?>" class="portfolio-card__link" aria-label="Voir la réalisation <?php echo esc_attr($r_client); ?>">
                    <div class="portfolio-card__image" style="background: linear-gradient(135deg, <?php echo esc_attr($r_from); ?> 0%, <?php echo esc_attr($r_to); ?> 100%);">
                        <span class="portfolio-card__monogram" aria-hidden="true"><?php echo esc_html(mb_strtoupper($r_initials)); ?></span>
                        <div class="portfolio-card__overlay">
                            <span class="portfolio-card__view">Voir le projet <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
                        </div>
                    </div>
                    <div class="portfolio-card__info">
                        <span class="portfolio-card__tag"><?php echo esc_html($r_category); ?></span>
                        <h3 class="portfolio-card__title"><?php echo esc_html($r_client); ?></h3>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="portfolio-cta-wrap">
            <a href="<?php echo esc_url(home_url('/realisations/')); ?>" class="btn btn-outline btn-lg">
                Voir toutes nos réalisations <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            </a>
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
<?php
$home_cta_alt_raw = trim(wp_strip_all_tags(yv_field('cta_title', 'Prêt à démarrer ?')));
$home_cta_alt = $home_cta_alt_raw !== '' ? $home_cta_alt_raw : get_bloginfo('name');
?>
<section class="cta-banner reveal">
    <?php $cta_bg_id = yv_image_id('cta_background'); if ($cta_bg_id): ?>
        <?php echo wp_get_attachment_image($cta_bg_id, 'hero', false, ['class' => 'hero-bg-img', 'loading' => 'lazy', 'alt' => $home_cta_alt]); ?>
    <?php endif; ?>
    <div class="cta-overlay"></div>
    <div class="cta-content">
        <h2><?php echo yv_format_title(yv_field('cta_title', 'Prêt à démarrer ?')); ?></h2>
        <div class="cta-text"><?php echo wp_kses_post(yv_field('cta_text_home')); ?></div>
        <a href="<?php echo esc_url(yv_field('cta_button_link', '/contact/')); ?>" class="btn btn-primary"><?php echo esc_html(yv_field('cta_button_text', 'Contactez-nous')); ?></a>
    </div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
