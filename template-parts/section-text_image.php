<section class="section text-image-section <?php echo get_sub_field('image_position') === 'left' ? 'reverse' : ''; ?> reveal">
    <div class="container grid grid-2">
        <div class="ti-content">
            <h2><?php echo esc_html(get_sub_field('title')); ?></h2>
            <div><?php echo wp_kses_post(get_sub_field('text')); ?></div>
            <?php if ($link = get_sub_field('link')): ?>
                <a href="<?php echo esc_url($link['url']); ?>" class="btn btn-primary"><?php echo esc_html($link['title']); ?></a>
            <?php endif; ?>
        </div>
        <div class="ti-image">
            <?php $img = get_sub_field('image'); if ($img && !empty($img['ID'])): ?>
                <?php echo wp_get_attachment_image($img['ID'], 'large', false, ['loading' => 'lazy', 'alt' => esc_attr(get_sub_field('title'))]); ?>
            <?php elseif ($img): ?>
                <img src="<?php echo esc_url($img['sizes']['large'] ?? $img['url']); ?>" alt="<?php echo esc_attr(get_sub_field('title')); ?>" loading="lazy">
            <?php endif; ?>
        </div>
    </div>
</section>
