<?php $bg = get_sub_field('bg_color') ?: 'light'; ?>
<section class="section numbers-section numbers-<?php echo esc_attr($bg); ?> reveal">
    <div class="container">
        <?php yv_section_header(get_sub_field('title')); ?>
        <?php yv_render_stats(get_sub_field('items'), 'stats-grid stats-grid-wide'); ?>
    </div>
</section>
