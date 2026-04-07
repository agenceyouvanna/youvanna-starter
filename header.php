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
        <a href="<?php echo esc_url(home_url()); ?>" class="nav-logo">
            <?php if (has_custom_logo()): ?>
                <?php the_custom_logo(); ?>
            <?php else: ?>
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
