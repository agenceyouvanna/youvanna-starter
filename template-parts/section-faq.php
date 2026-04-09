<?php
// Capture parent row fields BEFORE initializing the items sub-loop (have_rows
// changes the active SCF row context, which would break get_sub_field later).
$faq_title = get_sub_field('title');
$faq_badge = get_sub_field('badge');
if (!have_rows('items')) return;
?>
<section class="section faq-section reveal">
    <div class="container container-narrow">
        <?php yv_section_header($faq_title, '', $faq_badge); ?>
        <?php if (have_rows('items')): ?>
            <div class="faq-list">
                <?php while (have_rows('items')): the_row(); ?>
                    <details class="faq-item">
                        <summary><?php echo esc_html(get_sub_field('question')); ?></summary>
                        <div class="faq-answer"><?php echo wp_kses_post(get_sub_field('answer')); ?></div>
                    </details>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
