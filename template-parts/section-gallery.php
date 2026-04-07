<?php $images = get_sub_field('images'); if (empty($images) || !is_array($images)) return; ?>
<section class="section gallery-section reveal">
    <div class="container">
        <?php yv_section_header(get_sub_field('title'), '', get_sub_field('badge')); ?>
        <?php if (!empty($images)): ?>
            <div class="grid grid-<?php echo intval(get_sub_field('columns') ?: 3); ?> gallery-grid">
                <?php foreach ($images as $img): if (empty($img['ID'])) continue; ?>
                    <a href="<?php echo esc_url($img['url']); ?>" class="gallery-item" aria-label="<?php echo esc_attr($img['alt'] ?: 'Voir l\'image en grand'); ?>">
                        <?php echo wp_get_attachment_image($img['ID'], 'card', false, ['loading' => 'lazy', 'alt' => esc_attr($img['alt'])]); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
