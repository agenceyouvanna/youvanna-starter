<?php
$items = [];
if (have_rows('items')) {
    while (have_rows('items')) { the_row();
        $items[] = [
            'text' => get_sub_field('text'),
            'name' => get_sub_field('name'),
            'role' => get_sub_field('role'),
            'rating' => (int) get_sub_field('rating'),
            'photo' => get_sub_field('photo'),
        ];
    }
}
if (empty($items)) return;
// Determine rows: 1 row if <= 4, 2 rows if > 4
$use_marquee = count($items) > 3;
$rows = $use_marquee && count($items) > 6 ? 2 : 1;
?>
<section class="section testimonials-section reveal">
    <div class="container">
        <?php yv_section_header(get_sub_field('title')); ?>
    </div>
    <?php if ($use_marquee): ?>
        <?php
        // Split items into rows
        if ($rows === 2) {
            $mid = ceil(count($items) / 2);
            $row1 = array_slice($items, 0, $mid);
            $row2 = array_slice($items, $mid);
        } else {
            $row1 = $items;
            $row2 = [];
        }
        ?>
        <div class="testimonials-marquee">
            <div class="marquee-track" data-direction="left">
                <?php foreach (array_merge($row1, $row1) as $t): // duplicate for seamless loop ?>
                    <div class="testimonial-card">
                        <div class="testimonial-stars"><?php for ($i = 0; $i < $t['rating']; $i++) echo '&#9733;'; ?></div>
                        <blockquote><?php echo esc_html($t['text']); ?></blockquote>
                        <div class="testimonial-author">
                            <?php if ($t['photo']): ?>
                                <img src="<?php echo esc_url($t['photo']['sizes']['thumbnail']); ?>" alt="<?php echo esc_attr($t['name']); ?>">
                            <?php endif; ?>
                            <div>
                                <strong><?php echo esc_html($t['name']); ?></strong>
                                <?php if ($t['role']): ?><span><?php echo esc_html($t['role']); ?></span><?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (!empty($row2)): ?>
                <div class="marquee-track" data-direction="right">
                    <?php foreach (array_merge($row2, $row2) as $t): ?>
                        <div class="testimonial-card">
                            <div class="testimonial-stars"><?php for ($i = 0; $i < $t['rating']; $i++) echo '&#9733;'; ?></div>
                            <blockquote><?php echo esc_html($t['text']); ?></blockquote>
                            <div class="testimonial-author">
                                <?php if ($t['photo']): ?>
                                    <img src="<?php echo esc_url($t['photo']['sizes']['thumbnail']); ?>" alt="<?php echo esc_attr($t['name']); ?>">
                                <?php endif; ?>
                                <div>
                                    <strong><?php echo esc_html($t['name']); ?></strong>
                                    <?php if ($t['role']): ?><span><?php echo esc_html($t['role']); ?></span><?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="container">
            <div class="grid grid-3">
                <?php foreach ($items as $t): ?>
                    <div class="testimonial-card">
                        <div class="testimonial-stars"><?php for ($i = 0; $i < $t['rating']; $i++) echo '&#9733;'; ?></div>
                        <blockquote><?php echo esc_html($t['text']); ?></blockquote>
                        <div class="testimonial-author">
                            <?php if ($t['photo']): ?>
                                <img src="<?php echo esc_url($t['photo']['sizes']['thumbnail']); ?>" alt="<?php echo esc_attr($t['name']); ?>">
                            <?php endif; ?>
                            <div>
                                <strong><?php echo esc_html($t['name']); ?></strong>
                                <?php if ($t['role']): ?><span><?php echo esc_html($t['role']); ?></span><?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</section>