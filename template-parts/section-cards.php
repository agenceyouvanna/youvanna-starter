<section class="section cards-section reveal">
    <div class="container">
        <?php yv_section_header(get_sub_field('title'), get_sub_field('subtitle'), get_sub_field('badge')); ?>
        <?php if (have_rows('cards')): ?>
            <div class="grid grid-<?php echo intval(get_sub_field('columns') ?: 3); ?>">
                <?php while (have_rows('cards')): the_row();
                    $img = get_sub_field('image');
                    yv_render_card([
                        'image' => $img ? ($img['sizes']['card'] ?? $img['url']) : '',
                        'title' => get_sub_field('title'),
                        'text'  => get_sub_field('description'),
                        'link'  => get_sub_field('link'),
                    ]);
                endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
