</main>

<footer id="site-footer" role="contentinfo">
    <div class="footer-container">
        <div class="footer-brand">
            <a href="<?php echo esc_url(home_url()); ?>" class="footer-logo">
                <?php if (has_custom_logo()): the_custom_logo(); else: ?>
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
                        <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener" aria-label="<?php echo $label; ?>"><?php echo $label; ?></a>
                    <?php endif;
                endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="footer-nav">
            <h3>Navigation</h3>
            <?php wp_nav_menu(['theme_location' => 'footer', 'container' => false, 'menu_class' => 'footer-menu', 'fallback_cb' => false]); ?>
        </div>

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
        <p>&copy; <?php echo date('Y') . ' ' . get_bloginfo('name'); ?>. Tous droits réservés.</p>
        <p>Site réalisé par <a href="https://youvanna.com" target="_blank" rel="noopener">Agence Youvanna</a></p>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
