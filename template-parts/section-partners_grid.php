<?php
/**
 * Section flex : Grille de partenaires
 * Query automatique le CPT "partenaire" — source unique côté admin.
 * Admin ajoute/modifie les partenaires dans le CPT, la grille se met à jour partout.
 */
if (!post_type_exists('partenaire')) return;

$partenaires = get_posts([
    'post_type'      => 'partenaire',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
]);
// Ne garder que ceux avec un logo
$partenaires = array_filter($partenaires, fn($p) => (bool) get_post_thumbnail_id($p->ID));
if (!$partenaires) return;

$title    = get_sub_field('title') ?: 'Tous nos partenaires';
$subtitle = get_sub_field('subtitle');
$badge    = get_sub_field('badge');
$cols     = intval(get_sub_field('columns') ?: 5);
?>
<section class="section partners-grid-section reveal">
    <div class="container">
        <?php yv_section_header($title, $subtitle, $badge); ?>
        <div class="partners-grid grid-cols-<?php echo esc_attr($cols); ?>">
            <?php foreach ($partenaires as $p):
                $img_id = get_post_thumbnail_id($p->ID);
            ?>
                <a href="<?php echo esc_url(get_permalink($p->ID)); ?>" class="partner-card" title="<?php echo esc_attr($p->post_title); ?>">
                    <?php echo wp_get_attachment_image($img_id, 'medium', false, [
                        'loading' => 'lazy',
                        'alt'     => esc_attr($p->post_title),
                        'class'   => 'partner-card-logo',
                    ]); ?>
                    <span class="partner-card-name"><?php echo esc_html($p->post_title); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
