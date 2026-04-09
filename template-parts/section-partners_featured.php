<?php
/**
 * Section flex : Partenaires mis en avant par catégorie (taxonomie categorie_partenaire)
 * Permet d'afficher une section "Diamant", "Or", "Institutionnels"... qui se met
 * automatiquement à jour quand on assigne un partenaire à la catégorie côté admin.
 */
if (!post_type_exists('partenaire') || !taxonomy_exists('categorie_partenaire')) return;

$term_id = (int) get_sub_field('category');
if (!$term_id) return;
$term = get_term($term_id, 'categorie_partenaire');
if (!$term || is_wp_error($term)) return;

$partenaires = get_posts([
    'post_type'      => 'partenaire',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'tax_query'      => [[
        'taxonomy' => 'categorie_partenaire',
        'field'    => 'term_id',
        'terms'    => $term_id,
    ]],
]);
if (!$partenaires) return;

$title    = get_sub_field('title') ?: $term->name;
$subtitle = get_sub_field('subtitle');
$badge    = get_sub_field('badge');
$cols     = intval(get_sub_field('columns') ?: 3);
?>
<section class="section cards-section partners-featured-section reveal">
    <div class="container">
        <?php yv_section_header($title, $subtitle, $badge); ?>
        <div class="grid grid-<?php echo esc_attr($cols); ?>">
            <?php foreach ($partenaires as $p):
                $img_id = get_post_thumbnail_id($p->ID);
                $desc   = wp_strip_all_tags(get_the_excerpt($p)) ?: wp_trim_words(wp_strip_all_tags($p->post_content), 28);
                yv_render_card([
                    'image_id' => $img_id,
                    'title'    => $p->post_title,
                    'text'     => $desc,
                    'link'     => [
                        'url'    => get_permalink($p->ID),
                        'title'  => 'Découvrir',
                        'target' => '',
                    ],
                ]);
            endforeach; ?>
        </div>
    </div>
</section>
