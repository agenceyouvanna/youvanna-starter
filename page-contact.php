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
<section class="section contact-trust-section reveal">
    <div class="container">
        <div class="contact-trust-grid">
            <div class="contact-trust-item">
                <div class="contact-trust-icon"><i class="fa-solid fa-clock"></i></div>
                <div class="contact-trust-text">
                    <strong>Réponse sous 24h</strong>
                    <span>Chaque demande reçoit une réponse personnalisée</span>
                </div>
            </div>
            <div class="contact-trust-item">
                <div class="contact-trust-icon"><i class="fa-solid fa-comments"></i></div>
                <div class="contact-trust-text">
                    <strong>Premier échange gratuit</strong>
                    <span>Discutons de votre projet, sans engagement</span>
                </div>
            </div>
            <div class="contact-trust-item">
                <div class="contact-trust-icon"><i class="fa-solid fa-file-lines"></i></div>
                <div class="contact-trust-text">
                    <strong>Devis détaillé offert</strong>
                    <span>Proposition claire, transparente, sans surprise</span>
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
        <div class="contact-form-header reveal">
            <h2 class="section-title">Parlons de votre projet</h2>
            <p class="section-subtitle">Décrivez-nous votre besoin. Nous vous recontactons avec une proposition adaptée.</p>
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
                <div class="contact-mini-faq">
                    <h4>Questions fréquentes</h4>
                    <details class="contact-faq-item">
                        <summary>Quel est le délai pour obtenir un devis ?</summary>
                        <p>Nous vous envoyons un devis détaillé sous 48 heures après notre premier échange. Chaque proposition est personnalisée selon vos besoins.</p>
                    </details>
                    <details class="contact-faq-item">
                        <summary>Comment se déroule un premier échange ?</summary>
                        <p>Nous organisons un appel téléphonique ou une visio de 20 à 30 minutes pour comprendre votre projet, vos objectifs et vos contraintes. C'est gratuit et sans engagement.</p>
                    </details>
                    <details class="contact-faq-item">
                        <summary>Intervenez-vous partout en France ?</summary>
                        <p>Oui, nous travaillons avec des clients dans toute la France. La plupart de nos échanges se font en visio et par téléphone, le tout aussi efficacement qu'en présentiel.</p>
                    </details>
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
<section class="section contact-final-cta reveal">
    <div class="container">
        <div class="contact-final-cta-inner">
            <h2>Préférez-vous qu'on vous appelle ?</h2>
            <p>Laissez-nous votre numéro via le formulaire ci-dessus et nous vous rappelons dans les 24 heures.</p>
            <a href="#contact-form" class="btn btn-primary">Remonter au formulaire</a>
        </div>
    </div>
</section>

<?php get_footer(); ?>
