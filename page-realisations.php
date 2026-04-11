<?php
/**
 * page-realisations.php — archive custom pour la Page WP "Réalisations"
 * (post_name = "realisations", post_id = 49 en prod).
 *
 * Auto-binding via la convention WordPress page-{slug}.php — pas besoin de Template Name.
 *
 * Affiche un hero + une grille de toutes les réalisations du CPT "realisation".
 * Pourquoi pas archive-realisation.php :
 *   On garde la Page WP comme landing de l'archive (hero éditable depuis l'admin
 *   sans toucher au thème), et le CPT est enregistré avec has_archive => false exprès.
 */
get_header(); ?>

<?php while (have_posts()): the_post(); ?>

<?php
yv_render_hero([
    'image_id' => yv_image_id('page_hero_image') ?: get_post_thumbnail_id(),
    'image'    => yv_image('page_hero_image', 'hero') ?: get_the_post_thumbnail_url(null, 'large'),
    'title'    => yv_field('page_hero_title') ?: get_the_title(),
    'subtitle' => yv_field('page_hero_subtitle'),
    'badge'    => yv_field('page_hero_badge'),
]);
?>

<?php endwhile; ?>

<?php
// Intro facultative — texte libre édité dans l'éditeur de la page 49
$intro = trim(get_post_field('post_content', get_queried_object_id()));
if ($intro): ?>
<section class="section section-narrow reveal">
    <div class="container container-narrow">
        <?php echo apply_filters('the_content', $intro); ?>
    </div>
</section>
<?php endif; ?>

<?php
// Récupérer toutes les réalisations
$realisations = get_posts([
    'post_type'      => 'realisation',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order date',
    'order'          => 'ASC',
    'post_status'    => 'publish',
]);

// Catégories distinctes pour le filtre visuel (pas de JS, c'est juste un repère visuel)
$categories = [];
foreach ($realisations as $r) {
    $cat = get_post_meta($r->ID, 'category', true) ?: 'Projet';
    if (!in_array($cat, $categories, true)) $categories[] = $cat;
}
?>

<?php if (!empty($realisations)): ?>
<section class="section realisations-archive reveal">
    <div class="container container-wide">

        <?php if (count($categories) > 1): ?>
        <div class="realisations-filters" role="list" aria-label="Catégories de réalisations">
            <span class="realisations-filters__item is-active" role="listitem">Tous les projets</span>
            <?php foreach ($categories as $cat): ?>
                <span class="realisations-filters__item" role="listitem"><?php echo esc_html($cat); ?></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="realisations-grid">
            <?php foreach ($realisations as $r):
                $cat      = get_post_meta($r->ID, 'category', true) ?: 'Projet';
                $client   = get_post_meta($r->ID, 'client_name', true) ?: get_the_title($r);
                $sector   = get_post_meta($r->ID, 'sector', true);
                $year     = get_post_meta($r->ID, 'project_year', true);
                $from     = get_post_meta($r->ID, 'gradient_from', true) ?: '#0f172a';
                $to       = get_post_meta($r->ID, 'gradient_to', true) ?: '#1e40af';
                $permalink = get_permalink($r);
            ?>
            <a href="<?php echo esc_url($permalink); ?>" class="realisation-card" aria-label="Voir la réalisation <?php echo esc_attr($client); ?>">
                <div class="realisation-card__visual" style="background: linear-gradient(135deg, <?php echo esc_attr($from); ?> 0%, <?php echo esc_attr($to); ?> 100%);">
                    <span class="realisation-card__category"><?php echo esc_html($cat); ?></span>
                    <span class="realisation-card__initials" aria-hidden="true"><?php
                        // 2 premières lettres du nom client, style monogramme
                        $initials = mb_substr(preg_replace('/[^A-Za-zÀ-ÿ]/u', '', $client), 0, 2);
                        echo esc_html(mb_strtoupper($initials));
                    ?></span>
                </div>
                <div class="realisation-card__body">
                    <div class="realisation-card__meta">
                        <?php if ($sector): ?><span><?php echo esc_html($sector); ?></span><?php endif; ?>
                        <?php if ($sector && $year): ?><span class="dot" aria-hidden="true">·</span><?php endif; ?>
                        <?php if ($year): ?><span><?php echo esc_html($year); ?></span><?php endif; ?>
                    </div>
                    <h2 class="realisation-card__title"><?php echo esc_html($client); ?></h2>
                    <?php $excerpt = get_the_excerpt($r); if ($excerpt): ?>
                        <p class="realisation-card__excerpt"><?php echo esc_html(wp_trim_words($excerpt, 22)); ?></p>
                    <?php endif; ?>
                    <span class="realisation-card__cta">Découvrir le projet <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

    </div>
</section>
<?php else: ?>
<section class="section reveal">
    <div class="container container-narrow">
        <p>Aucune réalisation publiée pour le moment. <a href="/contact/">Parlons de votre projet.</a></p>
    </div>
</section>
<?php endif; ?>

<?php if (function_exists('have_rows') && have_rows('sections')): ?>
<?php while (have_posts()): the_post(); ?>
    <?php while (have_rows('sections')): the_row(); ?>
        <?php get_template_part('template-parts/section', get_row_layout()); ?>
    <?php endwhile; ?>
<?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>
