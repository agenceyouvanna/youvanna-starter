<?php
// IMPORTANT: read sub_fields of the flexible content row BEFORE calling
// have_rows('cards'), because have_rows switches the sub-field context into
// the nested repeater and subsequent get_sub_field() calls would miss the
// outer row's layout/title/etc.
$layout   = get_sub_field('layout') ?: 'grid';
$cols     = intval(get_sub_field('columns') ?: 3);
$title    = get_sub_field('title');
$subtitle = get_sub_field('subtitle');
$badge    = get_sub_field('badge');
if (!have_rows('cards')) return;
$layout_class = 'cards-layout-' . sanitize_html_class($layout);
$grid_class = 'grid-' . $cols;
// Featured + horizontal + minimal + steps override the columns grid
if (in_array($layout, ['featured', 'horizontal', 'minimal', 'steps'], true)) {
    $grid_class = '';
}
?>
<section class="section cards-section cards-section--<?php echo esc_attr($layout); ?> reveal">
    <div class="container">
        <?php yv_section_header($title, $subtitle, $badge); ?>
        <?php if (have_rows('cards')): ?>
            <div class="cards-wrap <?php echo esc_attr(trim($layout_class . ' ' . ($grid_class ? 'grid ' . $grid_class : ''))); ?>">
                <?php $idx = 0; while (have_rows('cards')): the_row(); $idx++;
                    $img = get_sub_field('image');
                    $link_raw = get_sub_field('link');
                    $has_link = is_array($link_raw) && !empty($link_raw['url']);
                    $link_url = $has_link ? esc_url($link_raw['url']) : '';
                    $link_target = $has_link && !empty($link_raw['target']) ? ' target="' . esc_attr($link_raw['target']) . '"' : '';
                    $title = get_sub_field('title');
                    $desc  = get_sub_field('description');
                    $icon  = get_sub_field('icon') ?: '';
                ?>
                    <?php if ($layout === 'minimal' || $layout === 'steps'): ?>
                        <?php if ($has_link): ?>
                            <a href="<?php echo $link_url; ?>" class="card card-clickable card-<?php echo esc_attr($layout); ?>" data-n="<?php echo esc_attr(str_pad($idx, 2, '0', STR_PAD_LEFT)); ?>"<?php echo $link_target; ?>>
                        <?php else: ?>
                            <div class="card card-<?php echo esc_attr($layout); ?>" data-n="<?php echo esc_attr(str_pad($idx, 2, '0', STR_PAD_LEFT)); ?>">
                        <?php endif; ?>
                            <div class="card-number" aria-hidden="true"><?php echo esc_html(str_pad($idx, 2, '0', STR_PAD_LEFT)); ?></div>
                            <div class="card-body">
                                <h3><?php echo esc_html($title); ?></h3>
                                <?php if ($desc): ?><div class="card-text"><?php echo wp_kses_post(wpautop($desc)); ?></div><?php endif; ?>
                                <?php if ($has_link): ?><span class="card-link"><?php echo esc_html($link_raw['title'] ?? 'En savoir plus'); ?></span><?php endif; ?>
                            </div>
                        <?php echo $has_link ? '</a>' : '</div>'; ?>
                    <?php else: ?>
                        <?php yv_render_card([
                            'image_id' => $img && !empty($img['ID']) ? $img['ID'] : 0,
                            'image' => $img ? ($img['sizes']['card'] ?? $img['url']) : '',
                            'icon'  => $icon,
                            'title' => $title,
                            'text'  => $desc,
                            'link'  => $link_raw,
                        ]); ?>
                    <?php endif; ?>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
