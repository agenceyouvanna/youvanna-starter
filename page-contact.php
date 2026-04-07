<?php
/* Template Name: Contact */
get_header(); ?>

<?php
yv_render_hero([
    'image_id' => yv_image_id('page_hero_image'),
    'image'    => yv_image('page_hero_image', 'hero'),
    'title'    => yv_field('page_hero_title') ?: get_the_title(),
    'subtitle' => yv_field('page_hero_subtitle'),
]);
?>

<section class="section">
    <div class="container contact-grid">
        <div class="contact-form">
            <h2><?php echo esc_html(yv_field('contact_form_title', 'Envoyez-nous un message')); ?></h2>
            <?php
            $form_id = yv_field('contact_form_id');
            if ($form_id) {
                echo do_shortcode('[contact-form-7 id="' . intval($form_id) . '"]');
            } else {
                the_content();
            }
            ?>
        </div>
        <div class="contact-info">
            <h3>Nos coordonnées</h3>
            <?php $phone = yv_option('phone'); if ($phone): ?>
                <div class="contact-info-item">
                    <div class="icon"><i class="fa-solid fa-phone"></i></div>
                    <div><strong>Téléphone</strong><br><a href="tel:<?php echo esc_attr(preg_replace('/\s/', '', $phone)); ?>"><?php echo esc_html($phone); ?></a></div>
                </div>
            <?php endif; ?>
            <?php $email = yv_option('email'); if ($email): ?>
                <div class="contact-info-item">
                    <div class="icon"><i class="fa-solid fa-envelope"></i></div>
                    <div><strong>Email</strong><br><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></div>
                </div>
            <?php endif; ?>
            <?php $address = yv_option('address'); if ($address): ?>
                <div class="contact-info-item">
                    <div class="icon"><i class="fa-solid fa-location-dot"></i></div>
                    <div><strong>Adresse</strong><br><?php echo nl2br(esc_html($address)); ?></div>
                </div>
            <?php endif; ?>
            <?php $hours = yv_option('opening_hours'); if ($hours): ?>
                <div class="contact-info-item">
                    <div class="icon"><i class="fa-solid fa-clock"></i></div>
                    <div><strong>Horaires</strong><br><?php echo nl2br(esc_html($hours)); ?></div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
// Google Maps (from options or page field)
$show_map = yv_field('show_map');
$map_url = yv_option('maps_embed_url');
if ($show_map && $map_url): ?>
<section class="section map-section" style="padding: 0;">
    <iframe src="<?php echo esc_url($map_url); ?>" width="100%" height="450" style="border:0; display:block;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Google Maps"></iframe>
</section>
<?php endif; ?>

<?php get_footer(); ?>
