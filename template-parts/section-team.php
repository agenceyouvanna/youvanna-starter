<section class="section team-section reveal">
    <div class="container">
        <?php yv_section_header(get_sub_field('title'), get_sub_field('subtitle')); ?>
        <?php if (have_rows('members')): ?>
            <div class="grid grid-<?php echo intval(get_sub_field('columns') ?: 3); ?> team-grid">
                <?php while (have_rows('members')): the_row();
                    $photo = get_sub_field('photo');
                ?>
                    <div class="team-member">
                        <?php if ($photo): ?>
                            <img src="<?php echo esc_url($photo['sizes']['card'] ?? $photo['url']); ?>" alt="<?php echo esc_attr(get_sub_field('name')); ?>" loading="lazy">
                        <?php endif; ?>
                        <div class="team-member-info">
                            <h3><?php echo esc_html(get_sub_field('name')); ?></h3>
                            <?php if ($role = get_sub_field('role')): ?>
                                <span class="team-role"><?php echo esc_html($role); ?></span>
                            <?php endif; ?>
                            <?php if ($bio = get_sub_field('bio')): ?>
                                <p><?php echo esc_html($bio); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</section>