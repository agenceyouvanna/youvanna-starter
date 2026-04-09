</main>

<footer id="site-footer" role="contentinfo">
    <div class="footer-container">
        <div class="footer-brand">
            <a href="<?php echo esc_url(home_url()); ?>" class="footer-logo">
                <?php
                $yv_footer_logo_id = (int) get_theme_mod('yv_footer_logo_id', 0);
                if ($yv_footer_logo_id && wp_attachment_is_image($yv_footer_logo_id)):
                    echo wp_get_attachment_image($yv_footer_logo_id, 'full', false, [
                        'class' => 'custom-logo footer-custom-logo',
                        'alt'   => get_post_meta($yv_footer_logo_id, '_wp_attachment_image_alt', true) ?: get_bloginfo('name'),
                    ]);
                elseif (has_custom_logo()):
                    the_custom_logo();
                else: ?>
                    <span><?php bloginfo('name'); ?></span>
                <?php endif; ?>
            </a>
            <p class="footer-description"><?php echo esc_html(yv_option('footer_description', get_bloginfo('description'))); ?></p>
            <?php
            $socials = [
                'social_facebook'  => 'Facebook',
                'social_instagram' => 'Instagram',
                'social_linkedin'  => 'LinkedIn',
                'social_youtube'   => 'YouTube',
                'social_tiktok'    => 'TikTok',
            ];
            $has_social = false;
            foreach ($socials as $key => $label) {
                if (yv_option($key)) { $has_social = true; break; }
            }
            if ($has_social): ?>
            <div class="footer-social">
                <?php foreach ($socials as $key => $label):
                    $url = yv_option($key);
                    if ($url): ?>
                        <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr($label); ?>"><i class="fa-brands fa-<?php echo esc_attr(strtolower($label)); ?>"></i> <span class="screen-reader-text"><?php echo esc_html($label); ?></span></a>
                    <?php endif;
                endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <?php
        // Footer nav : si aucun menu assigné à la location "footer", fallback sur les pages top-level publiées
        // pour ne jamais afficher une colonne vide (bug sodental 2026-04).
        if (has_nav_menu('footer')) {
            echo '<nav class="footer-nav" aria-label="Navigation footer"><h3>Navigation</h3>';
            wp_nav_menu(['theme_location' => 'footer', 'container' => false, 'menu_class' => 'footer-menu', 'fallback_cb' => false]);
            echo '</nav>';
        } else {
            $fallback_pages = get_pages([
                'parent'      => 0,
                'sort_column' => 'menu_order,post_title',
                'number'      => 7,
                'post_status' => 'publish',
            ]);
            if (!empty($fallback_pages)) {
                echo '<nav class="footer-nav" aria-label="Navigation footer"><h3>Navigation</h3><ul class="footer-menu">';
                foreach ($fallback_pages as $fp) {
                    printf(
                        '<li><a href="%s">%s</a></li>',
                        esc_url(get_permalink($fp->ID)),
                        esc_html($fp->post_title)
                    );
                }
                echo '</ul></nav>';
            }
        }
        ?>

        <div class="footer-contact">
            <h3>Contact</h3>
            <?php $phone = yv_option('phone'); if ($phone): ?>
                <a href="tel:<?php echo esc_attr(preg_replace('/\s/', '', $phone)); ?>"><?php echo esc_html($phone); ?></a>
            <?php endif; ?>
            <?php $email = yv_option('email'); if ($email): ?>
                <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
            <?php endif; ?>
            <?php $address = yv_option('address'); if ($address): ?>
                <p><?php echo esc_html($address); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; <?php echo wp_date('Y') . ' ' . get_bloginfo('name'); ?>. Tous droits réservés.</p>
        <p>Site réalisé par <a href="https://youvanna.com" target="_blank" rel="noopener">Agence Youvanna</a></p>
    </div>
</footer>
<a href="#" class="back-to-top" aria-label="Retour en haut"><i class="fa-solid fa-chevron-up"></i></a>

<?php wp_footer(); ?>
</body>
</html>
