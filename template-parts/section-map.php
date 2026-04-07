<?php $map_url = get_sub_field('map_url'); if ($map_url): ?>
<section class="section map-section reveal">
    <div class="container">
        <?php yv_section_header(get_sub_field('title')); ?>
    </div>
    <div class="map-wrapper">
        <iframe src="<?php echo esc_url($map_url); ?>" width="100%" height="<?php echo intval(get_sub_field('height') ?: 450); ?>" style="border:0; display:block;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Google Maps"></iframe>
    </div>
</section>
<?php endif; ?>
