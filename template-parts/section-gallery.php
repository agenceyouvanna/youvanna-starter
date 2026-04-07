<section class="section gallery-section reveal">
    <div class="container">
        <?php yv_section_header(get_sub_field('title')); ?>
        <?php $images = get_sub_field('images'); if ($images): ?>
            <div class="grid grid-<?php echo intval(get_sub_field('columns') ?: 3); ?> gallery-grid">
                <?php foreach ($images as $img): ?>
                    <a href="<?php echo esc_url($img['url']); ?>" class="gallery-item" target="_blank">
                        <img src="<?php echo esc_url($img['sizes']['card'] ?? $img['sizes']['medium_large'] ?? $img['url']); ?>" alt="<?php echo esc_attr($img['alt']); ?>" loading="lazy">
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
