<section class="section video-section reveal">
    <div class="container container-narrow">
        <?php yv_section_header(get_sub_field('title')); ?>
        <?php
        $url = get_sub_field('video_url');
        if ($url):
            // Convert YouTube/Vimeo URLs to embed
            $embed_url = '';
            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w-]+)/', $url, $m)) {
                $embed_url = 'https://www.youtube-nocookie.com/embed/' . $m[1];
            } elseif (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) {
                $embed_url = 'https://player.vimeo.com/video/' . $m[1];
            }
            if ($embed_url): ?>
                <div class="video-wrapper">
                    <iframe src="<?php echo esc_url($embed_url); ?>" width="100%" height="450" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen loading="lazy" title="Video"></iframe>
                </div>
            <?php endif;
        endif; ?>
    </div>
</section>