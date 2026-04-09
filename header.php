<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a href="#main-content" class="skip-link">Aller au contenu</a>

<header id="site-header" role="banner">
    <nav class="nav-container" aria-label="Navigation principale">
        <a href="<?php echo esc_url(home_url()); ?>" class="nav-logo" rel="home">
            <?php
            // IMPORTANT : ne JAMAIS utiliser the_custom_logo() ici — il génère déjà son propre <a>,
            // ce qui créerait un <a> imbriqué (invalide HTML5, casse le layout du header).
            // On extrait directement l'image via wp_get_attachment_image().
            $yv_logo_id = (int) get_theme_mod('custom_logo');
            if ($yv_logo_id && wp_attachment_is_image($yv_logo_id)):
                echo wp_get_attachment_image($yv_logo_id, 'full', false, [
                    'class'         => 'custom-logo',
                    'alt'           => get_post_meta($yv_logo_id, '_wp_attachment_image_alt', true) ?: get_bloginfo('name'),
                    'fetchpriority' => 'high',
                ]);
            else: ?>
                <span class="site-name"><?php bloginfo('name'); ?></span>
            <?php endif; ?>
        </a>

        <?php wp_nav_menu([
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'nav-menu',
            'fallback_cb'    => function() { echo '<ul class="nav-menu"></ul>'; },
        ]); ?>

        <div class="nav-cta">
            <?php $phone = yv_option('phone'); if ($phone): ?>
                <a href="tel:<?php echo esc_attr(preg_replace('/\s/', '', $phone)); ?>" class="nav-phone"><?php echo esc_html($phone); ?></a>
            <?php endif; ?>
            <a href="<?php echo esc_url(yv_option('cta_link', '/contact')); ?>" class="btn btn-primary btn-sm"><?php echo esc_html(yv_option('cta_text', 'Nous contacter')); ?></a>
        </div>

        <button class="nav-toggle" aria-label="Menu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>

        <div class="nav-mobile-cta">
            <?php if ($phone): ?>
                <a href="tel:<?php echo esc_attr(preg_replace('/\s/', '', $phone)); ?>" class="nav-phone"><?php echo esc_html($phone); ?></a>
            <?php endif; ?>
            <a href="<?php echo esc_url(yv_option('cta_link', '/contact')); ?>" class="btn btn-primary btn-sm"><?php echo esc_html(yv_option('cta_text', 'Nous contacter')); ?></a>
        </div>
    </nav>
</header>

<main id="main-content" role="main">
