<?php
/**
 * single-realisation.php — page détail d'une réalisation (Kelcible-style)
 *
 * Sections (dans l'ordre) :
 *   1. Hero gradient avec client, catégorie, secteur, année, lieu
 *   2. Contexte / Problématique
 *   3. Périmètre / Ce qu'on a fait
 *   4. Services activés (liste d'icônes)
 *   5. Visuel large (gradient XL pour l'instant — sera remplacé par un screenshot plus tard)
 *   6. Navigation projet précédent / suivant
 *   7. CTA "Échangeons sur votre projet"
 *
 * Pas de métriques / KPIs : user rule — on n'invente jamais de chiffres.
 */
get_header(); ?>

<?php while (have_posts()): the_post();
    $id       = get_the_ID();
    $client   = get_post_meta($id, 'client_name', true) ?: get_the_title();
    $category = get_post_meta($id, 'category', true) ?: 'Projet';
    $sector   = get_post_meta($id, 'sector', true);
    $year     = get_post_meta($id, 'project_year', true);
    $location = get_post_meta($id, 'project_location', true);
    $context  = get_post_meta($id, 'context', true);
    $scope    = get_post_meta($id, 'scope', true);
    $from     = get_post_meta($id, 'gradient_from', true) ?: '#0f172a';
    $to       = get_post_meta($id, 'gradient_to', true) ?: '#1e40af';
    $external = get_post_meta($id, 'external_url', true);

    // Services activés (repeater SCF)
    $services = [];
    if (function_exists('have_rows') && have_rows('services_used')) {
        while (have_rows('services_used')): the_row();
            $services[] = [
                'icon'  => get_sub_field('icon') ?: 'fa-solid fa-check',
                'label' => get_sub_field('label'),
            ];
        endwhile;
    }
?>

<article class="realisation-single">

    <!-- 1. HERO -->
    <section class="realisation-hero" style="background: linear-gradient(135deg, <?php echo esc_attr($from); ?> 0%, <?php echo esc_attr($to); ?> 100%);">
        <div class="container realisation-hero__inner">
            <div class="realisation-hero__breadcrumb">
                <a href="<?php echo esc_url(home_url('/realisations/')); ?>"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Toutes nos réalisations</a>
            </div>
            <div class="realisation-hero__meta">
                <span class="realisation-hero__category"><?php echo esc_html($category); ?></span>
                <?php if ($sector): ?><span class="realisation-hero__tag"><?php echo esc_html($sector); ?></span><?php endif; ?>
                <?php if ($year): ?><span class="realisation-hero__tag"><?php echo esc_html($year); ?></span><?php endif; ?>
                <?php if ($location): ?><span class="realisation-hero__tag"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> <?php echo esc_html($location); ?></span><?php endif; ?>
            </div>
            <h1 class="realisation-hero__title"><?php echo esc_html($client); ?></h1>
            <?php $excerpt = get_the_excerpt(); if ($excerpt): ?>
                <p class="realisation-hero__subtitle"><?php echo esc_html($excerpt); ?></p>
            <?php endif; ?>
        </div>
    </section>

    <!-- 2. CONTEXTE / PROBLÉMATIQUE -->
    <?php if ($context): ?>
    <section class="section realisation-context reveal">
        <div class="container container-narrow">
            <div class="realisation-context__grid">
                <div class="realisation-context__label">
                    <span class="eyebrow">Contexte</span>
                    <h2>La problématique</h2>
                </div>
                <div class="realisation-context__text">
                    <p><?php echo nl2br(esc_html($context)); ?></p>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- 3. PÉRIMÈTRE -->
    <?php if ($scope): ?>
    <section class="section realisation-scope bg-light reveal">
        <div class="container container-narrow">
            <div class="realisation-context__grid">
                <div class="realisation-context__label">
                    <span class="eyebrow">Périmètre</span>
                    <h2>Ce que nous avons livré</h2>
                </div>
                <div class="realisation-context__text">
                    <p><?php echo nl2br(esc_html($scope)); ?></p>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- 4. SERVICES ACTIVÉS -->
    <?php if (!empty($services)): ?>
    <section class="section realisation-services reveal">
        <div class="container">
            <div class="section-header text-center">
                <span class="eyebrow">Services activés</span>
                <h2>Les leviers déployés</h2>
            </div>
            <ul class="realisation-services__list">
                <?php foreach ($services as $svc): ?>
                    <li class="realisation-services__item">
                        <i class="<?php echo esc_attr($svc['icon']); ?>" aria-hidden="true"></i>
                        <span><?php echo esc_html($svc['label']); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
    <?php endif; ?>

    <!-- 5. VISUEL LARGE -->
    <section class="realisation-visual reveal">
        <div class="container container-wide">
            <div class="realisation-visual__frame" style="background: linear-gradient(135deg, <?php echo esc_attr($from); ?> 0%, <?php echo esc_attr($to); ?> 100%);">
                <span class="realisation-visual__watermark" aria-hidden="true"><?php echo esc_html($client); ?></span>
            </div>
        </div>
    </section>

    <!-- 6. NAVIGATION PROJET PRÉCÉDENT / SUIVANT -->
    <?php
    $prev = get_previous_post();
    $next = get_next_post();
    if ($prev || $next): ?>
    <section class="section realisation-nav reveal">
        <div class="container">
            <div class="realisation-nav__grid">
                <?php if ($prev): ?>
                    <a href="<?php echo esc_url(get_permalink($prev)); ?>" class="realisation-nav__link realisation-nav__link--prev">
                        <span class="realisation-nav__label"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Projet précédent</span>
                        <strong><?php echo esc_html(get_post_meta($prev->ID, 'client_name', true) ?: get_the_title($prev)); ?></strong>
                    </a>
                <?php else: ?><span></span><?php endif; ?>
                <?php if ($next): ?>
                    <a href="<?php echo esc_url(get_permalink($next)); ?>" class="realisation-nav__link realisation-nav__link--next">
                        <span class="realisation-nav__label">Projet suivant <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
                        <strong><?php echo esc_html(get_post_meta($next->ID, 'client_name', true) ?: get_the_title($next)); ?></strong>
                    </a>
                <?php else: ?><span></span><?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- 7. CTA FINAL -->
    <section class="cta-banner reveal">
        <div class="cta-overlay" style="background: linear-gradient(135deg, <?php echo esc_attr($from); ?> 0%, <?php echo esc_attr($to); ?> 100%); opacity: 1;"></div>
        <div class="cta-content">
            <h2>Un projet similaire en tête ?</h2>
            <div class="cta-text"><p>Parlons-en. Premier échange gratuit, devis détaillé sous 48 heures.</p></div>
            <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-primary">Démarrer mon projet <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
    </section>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>
