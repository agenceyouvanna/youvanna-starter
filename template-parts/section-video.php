<?php if (!get_sub_field('video_url')) return; ?>
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
                    <iframe src="<?php echo esc_url($embed_url); ?>" width="100%" height="450" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen loading="lazy" title="<?php echo esc_attr(get_sub_field('title') ?: 'Video'); ?>"></iframe>
                </div>
            <?php elseif (preg_match('/\.(mp4|webm|ogg)$/i', $url)): ?>
                <video controls preload="metadata" style="width:100%;border-radius:var(--radius);">
                    <source src="<?php echo esc_url($url); ?>" type="video/<?php echo esc_attr(pathinfo($url, PATHINFO_EXTENSION)); ?>">
                </video>
            <?php else:
                $oembed = wp_oembed_get($url);
                if ($oembed): ?>
                    <div class="video-wrapper"><?php echo wp_kses_post($oembed); ?></div>
                <?php endif;
            endif;
        endif; ?>
    </div>
</section>