<?php
/* Template Name: Contact */
get_header(); ?>

<?php
yv_render_hero([
    'image_id' => yv_image_id('page_hero_image'),
    'image'    => yv_image('page_hero_image', 'hero'),
    'title'    => yv_field('page_hero_title') ?: get_the_title(),
    'subtitle' => yv_field('page_hero_subtitle'),
    'buttons'  => [
        ['text' => 'Envoyer un message', 'url' => '#contact-form', 'class' => 'btn-primary'],
        ['text' => 'Appeler maintenant', 'url' => 'tel:' . preg_replace('/\s/', '', yv_option('phone', '')), 'class' => 'btn-outline btn-outline-light'],
    ],
]);
?>

<!-- Trust signals -->
<?php
$trust_1_t = yv_field('contact_trust_1_title') ?: 'Réponse rapide';
$trust_1_s = yv_field('contact_trust_1_sub') ?: 'Nous vous répondons en moins de 2 heures en journée';
$trust_2_t = yv_field('contact_trust_2_title') ?: 'Devis gratuit';
$trust_2_s = yv_field('contact_trust_2_sub') ?: 'Un tarif clair, sans engagement ni surprise';
$trust_3_t = yv_field('contact_trust_3_title') ?: 'Disponibles 7j/7';
$trust_3_s = yv_field('contact_trust_3_sub') ?: 'Service 24h/24 pour tous vos trajets en Sarthe';
?>
<section class="section contact-trust-section reveal">
    <div class="container">
        <div class="contact-trust-grid">
            <div class="contact-trust-item">
                <div class="contact-trust-icon"><i class="fa-solid fa-clock"></i></div>
                <div class="contact-trust-text">
                    <strong><?php echo esc_html($trust_1_t); ?></strong>
                    <span><?php echo esc_html($trust_1_s); ?></span>
                </div>
            </div>
            <div class="contact-trust-item">
                <div class="contact-trust-icon"><i class="fa-solid fa-comments"></i></div>
                <div class="contact-trust-text">
                    <strong><?php echo esc_html($trust_2_t); ?></strong>
                    <span><?php echo esc_html($trust_2_s); ?></span>
                </div>
            </div>
            <div class="contact-trust-item">
                <div class="contact-trust-icon"><i class="fa-solid fa-file-lines"></i></div>
                <div class="contact-trust-text">
                    <strong><?php echo esc_html($trust_3_t); ?></strong>
                    <span><?php echo esc_html($trust_3_s); ?></span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SCF sections before form (if any) -->
<?php
if (function_exists('have_rows') && have_rows('sections')):
    while (have_rows('sections')): the_row();
        get_template_part('template-parts/section', get_row_layout());
    endwhile;
endif;
?>

<!-- Contact form + info -->
<section class="section contact-form-section" id="contact-form">
    <div class="container">
        <?php
        $form_title = yv_field('contact_form_title') ?: 'Demande de devis gratuit';
        $form_subtitle = yv_field('contact_form_subtitle') ?: 'Remplissez le formulaire ci-dessous, nous vous répondons dans les meilleurs délais.';
        ?>
        <div class="contact-form-header reveal">
            <h2 class="section-title"><?php echo esc_html($form_title); ?></h2>
            <p class="section-subtitle"><?php echo esc_html($form_subtitle); ?></p>
        </div>
        <div class="contact-grid reveal">
            <div class="contact-form">
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
                <div class="contact-info-card">
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

                <!-- Mini FAQ -->
                <?php
                $faq_items = [];
                if (function_exists('have_rows') && have_rows('contact_mini_faq')):
                    while (have_rows('contact_mini_faq')): the_row();
                        $faq_items[] = ['q' => get_sub_field('question'), 'a' => get_sub_field('reponse')];
                    endwhile;
                endif;
                if (empty($faq_items)) {
                    $faq_items = [
                        ['q' => 'Sous quel délai obtenir une réponse ?', 'a' => 'Nous répondons généralement sous 2 heures en journée. Pour une urgence, appelez directement.'],
                        ['q' => 'Puis-je réserver pour ce soir ?', 'a' => 'Oui, selon disponibilité. Contactez-nous par téléphone pour les réservations de dernière minute.'],
                        ['q' => 'Quels modes de paiement acceptez-vous ?', 'a' => 'Espèces, carte bancaire et virement pour les entreprises. Un acompte peut être demandé pour les longs trajets.'],
                    ];
                }
                ?>
                <div class="contact-mini-faq">
                    <h4>Questions fréquentes</h4>
                    <?php foreach ($faq_items as $item): ?>
                    <details class="contact-faq-item">
                        <summary><?php echo esc_html($item['q']); ?></summary>
                        <p><?php echo wp_kses_post($item['a']); ?></p>
                    </details>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Google Maps
$show_map = yv_field('show_map');
$map_url = yv_option('maps_embed_url');
if ($show_map && $map_url): ?>
<section class="section map-section" style="padding: 0;">
    <iframe src="<?php echo esc_url($map_url); ?>" width="100%" height="450" style="border:0; display:block;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Google Maps"></iframe>
</section>
<?php endif; ?>

<!-- Final CTA -->
<?php
$final_title = yv_field('contact_final_title') ?: 'Prêt à réserver votre chauffeur ?';
$final_text = yv_field('contact_final_text') ?: 'Remplissez le formulaire ou appelez-nous directement. Nous sommes disponibles 7j/7 pour tous vos trajets.';
$final_btn = yv_field('contact_final_btn') ?: 'Remonter au formulaire';
?>
<section class="section contact-final-cta reveal">
    <div class="container">
        <div class="contact-final-cta-inner">
            <h2><?php echo esc_html($final_title); ?></h2>
            <p><?php echo esc_html($final_text); ?></p>
            <a href="#contact-form" class="btn btn-primary"><?php echo esc_html($final_btn); ?></a>
        </div>
    </div>
</section>

<?php get_footer(); ?>
