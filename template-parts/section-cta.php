<?php if (!get_sub_field('title')) return; ?>
<?php $bg = get_sub_field('background'); ?>
<section class="cta-banner reveal">
    <?php if ($bg && !empty($bg['ID'])): ?>
        <?php echo wp_get_attachment_image($bg['ID'], 'hero', false, ['class' => 'hero-bg-img', 'loading' => 'lazy', 'alt' => '']); ?>
    <?php endif; ?>
    <div class="cta-overlay"></div>
    <div class="cta-content">
        <h2><?php echo esc_html(get_sub_field('title')); ?></h2>
        <p><?php echo esc_html(get_sub_field('text')); ?></p>
        <?php $link = get_sub_field('button'); if ($link && is_array($link)): ?>
            <a href="<?php echo esc_url($link['url']); ?>" class="btn btn-primary"><?php echo esc_html($link['title'] ?? 'En savoir plus'); ?></a>
        <?php endif; ?>
    </div>
</section>
