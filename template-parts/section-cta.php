<?php if (!get_sub_field('title')) return; ?>
<?php
$bg = get_sub_field('background');
$cta_alt_raw = trim(wp_strip_all_tags(get_sub_field('title')));
$cta_alt = $cta_alt_raw !== '' ? $cta_alt_raw : get_bloginfo('name');
?>
<section class="cta-banner reveal">
    <?php if ($bg && !empty($bg['ID'])): ?>
        <?php echo wp_get_attachment_image($bg['ID'], 'hero', false, ['class' => 'hero-bg-img', 'loading' => 'lazy', 'alt' => $cta_alt]); ?>
    <?php endif; ?>
    <div class="cta-overlay"></div>
    <div class="cta-content">
        <h2><?php echo yv_format_title(get_sub_field('title')); ?></h2>
        <div class="cta-text"><?php echo wp_kses_post(get_sub_field('text')); ?></div>
        <?php
        $link = get_sub_field('button');
        $btn_url   = (is_array($link) && !empty($link['url']))   ? $link['url']   : '/contact/';
        $btn_label = (is_array($link) && !empty($link['title'])) ? $link['title'] : 'Demander un devis gratuit';
        ?>
        <a href="<?php echo esc_url($btn_url); ?>" class="btn btn-primary"><?php echo esc_html($btn_label); ?></a>
    </div>
</section>
