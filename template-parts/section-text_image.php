<?php if (!get_sub_field('title') && !get_sub_field('text')) return; ?>
<section class="section text-image-section <?php echo get_sub_field('image_position') === 'left' ? 'reverse' : ''; ?> reveal">
    <div class="container grid grid-2">
        <div class="ti-content">
            <h2><?php echo yv_format_title(get_sub_field('title')); ?></h2>
            <div><?php echo wp_kses_post(get_sub_field('text')); ?></div>
            <?php $link = get_sub_field('link'); if ($link && is_array($link)): ?>
                <a href="<?php echo esc_url($link['url']); ?>" class="btn btn-primary"><?php echo esc_html($link['title'] ?? 'En savoir plus'); ?></a>
            <?php endif; ?>
        </div>
        <div class="ti-image image-frame">
            <?php $img = get_sub_field('image'); if ($img && !empty($img['ID'])): ?>
                <?php echo wp_get_attachment_image($img['ID'], 'large', false, ['loading' => 'lazy', 'alt' => esc_attr(get_sub_field('title'))]); ?>
            <?php elseif ($img): ?>
                <img src="<?php echo esc_url($img['sizes']['large'] ?? $img['url']); ?>" alt="<?php echo esc_attr(get_sub_field('title')); ?>" loading="lazy" width="<?php echo esc_attr($img['sizes']['large-width'] ?? $img['width'] ?? ''); ?>" height="<?php echo esc_attr($img['sizes']['large-height'] ?? $img['height'] ?? ''); ?>">
            <?php endif; ?>
            <?php $ti_badge_num = get_sub_field('badge_number'); if ($ti_badge_num): ?>
                <div class="image-stat-badge is-bottom-right">
                    <?php $ti_badge_ico = get_sub_field('badge_icon'); if (!$ti_badge_ico) $ti_badge_ico = 'fa-solid fa-medal'; ?>
                    <div class="image-stat-badge__icon"><i class="<?php echo esc_attr($ti_badge_ico); ?>"></i></div>
                    <div class="image-stat-badge__text">
                        <span class="image-stat-badge__number stat-number"><?php echo esc_html($ti_badge_num); ?></span>
                        <span class="image-stat-badge__label"><?php echo esc_html(get_sub_field('badge_label')); ?></span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
