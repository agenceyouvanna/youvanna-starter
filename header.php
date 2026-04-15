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

        <?php $phone = yv_option('phone'); ?>
        <div class="nav-cta">
            <?php if ($phone): ?>
                <a href="tel:<?php echo esc_attr(preg_replace('/\s/', '', $phone)); ?>" class="nav-phone"><?php echo esc_html($phone); ?></a>
            <?php endif; ?>
            <a href="<?php echo esc_url(yv_option('cta_link', '/contact')); ?>" class="btn btn-primary btn-sm"><?php echo esc_html(yv_option('cta_text', 'Nous contacter')); ?></a>
        </div>

        <?php if (class_exists('\\Youvanna\\Shop\\Plugin')): ?>
            <div class="nav-actions">
                <?php if (is_user_logged_in()): ?>
                    <a href="<?php echo esc_url(admin_url('profile.php')); ?>" class="nav-account" aria-label="<?php esc_attr_e('Mon compte', 'youvanna-starter'); ?>" title="<?php esc_attr_e('Mon compte', 'youvanna-starter'); ?>">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </a>
                <?php else: ?>
                    <a href="<?php echo esc_url(wp_login_url((string) ($_SERVER['REQUEST_URI'] ?? home_url()))); ?>" class="nav-account" aria-label="<?php esc_attr_e('Se connecter', 'youvanna-starter'); ?>" title="<?php esc_attr_e('Se connecter', 'youvanna-starter'); ?>">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </a>
                <?php endif; ?>
                <button type="button" class="nav-cart yv-shop-cart-toggle" data-yv-shop-cart-toggle aria-label="<?php esc_attr_e('Ouvrir le panier', 'youvanna-starter'); ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    <span class="yv-shop-cart-toggle__count is-empty" data-yv-shop-cart-count>0</span>
                </button>
            </div>
        <?php endif; ?>

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
