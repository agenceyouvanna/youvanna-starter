<?php $narrow = get_sub_field('narrow'); ?>
<section class="section text-section reveal">
    <div class="container <?php echo $narrow ? 'container-narrow' : ''; ?>">
        <?php yv_section_header(get_sub_field('title')); ?>
        <div class="text-section-content">
            <?php echo wp_kses_post(get_sub_field('content')); ?>
        </div>
    </div>
</section>