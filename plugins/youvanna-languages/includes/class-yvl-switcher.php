<?php
defined('ABSPATH') || exit;

/**
 * Language switcher — outputs a frontend language selector.
 *
 * Renders as a floating dropdown or can be used via shortcode [yvl_switcher].
 * Also adds a helper function yvl_switcher() for theme integration.
 */
class YVL_Switcher {

    public static function init(): void {
        if (is_admin()) return;

        add_shortcode('yvl_switcher', [__CLASS__, 'shortcode']);
        add_action('wp_footer', [__CLASS__, 'render_floating'], 50);
        add_action('wp_head', [__CLASS__, 'inline_styles'], 50);
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_scripts']);
    }

    /**
     * Enqueue frontend JS for the switcher toggle.
     */
    public static function enqueue_scripts(): void {
        $langs = YVL_Languages::enabled();
        if (count($langs) < 2) return;

        wp_enqueue_script('yvl-switcher', YVL_URL . 'assets/js/switcher.js', [], YVL_VERSION, true);
    }

    /**
     * Shortcode: [yvl_switcher style="dropdown|flags|list"]
     */
    public static function shortcode($atts): string {
        $atts = shortcode_atts(['style' => 'dropdown'], $atts);
        return self::build_html($atts['style']);
    }

    /**
     * Render floating switcher in footer.
     */
    public static function render_floating(): void {
        $langs = YVL_Languages::enabled();
        if (count($langs) < 2) return;

        $show = get_option('yvl_show_switcher', 'floating');
        if ($show !== 'floating') return;

        echo self::build_html('floating');
    }

    /**
     * Build the switcher HTML.
     */
    public static function build_html(string $style = 'dropdown'): string {
        $langs = YVL_Languages::enabled();
        if (count($langs) < 2) return '';

        $current = YVL_Languages::current();
        $current_info = YVL_Languages::get_info($current);

        $class = 'yvl-switcher yvl-switcher--' . esc_attr($style);

        $html = '<div class="' . $class . '">';

        if ($style === 'floating') {
            $html .= '<button class="yvl-switcher__toggle" aria-label="' . esc_attr__('Choisir la langue', 'youvanna-languages') . '" aria-expanded="false">';
            $html .= '<span class="yvl-switcher__current">' . esc_html(strtoupper($current)) . '</span>';
            $html .= '</button>';
        }

        $html .= '<ul class="yvl-switcher__list" role="listbox">';

        foreach ($langs as $lang) {
            $info = YVL_Languages::get_info($lang);
            if (!$info) continue;

            $url = YVL_Router::url_for_lang($lang);
            $active = ($lang === $current) ? ' yvl-switcher__item--active' : '';

            $html .= '<li class="yvl-switcher__item' . $active . '" role="option">';
            $html .= '<a href="' . esc_url($url) . '" hreflang="' . esc_attr($lang) . '" lang="' . esc_attr($lang) . '">';
            $html .= '<span class="yvl-switcher__code">' . esc_html(strtoupper($lang)) . '</span>';
            $html .= '<span class="yvl-switcher__name">' . esc_html($info['native']) . '</span>';
            $html .= '</a></li>';
        }

        $html .= '</ul></div>';

        return $html;
    }

    /**
     * Inline CSS for the switcher.
     */
    public static function inline_styles(): void {
        $langs = YVL_Languages::enabled();
        if (count($langs) < 2) return;
        ?>
        <style id="yvl-switcher-css">
        .yvl-switcher--floating{position:fixed;bottom:24px;left:24px;z-index:9999;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif}
        .yvl-switcher__toggle{display:flex;align-items:center;justify-content:center;width:48px;height:48px;border-radius:50%;background:var(--color-primary,#2563eb);color:#fff;border:none;cursor:pointer;font-size:14px;font-weight:700;box-shadow:0 4px 12px rgba(0,0,0,.15);transition:all .2s}
        .yvl-switcher__toggle:hover{transform:scale(1.05);box-shadow:0 6px 16px rgba(0,0,0,.2)}
        .yvl-switcher__list{display:none;position:absolute;bottom:56px;left:0;list-style:none;margin:0;padding:4px 0;background:#fff;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,.12);min-width:180px;overflow:hidden}
        .yvl-switcher--floating .yvl-switcher__toggle[aria-expanded="true"]+.yvl-switcher__list{display:block;animation:yvlFadeIn .2s ease}
        @keyframes yvlFadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
        .yvl-switcher__item a{display:flex;gap:10px;align-items:center;padding:10px 16px;text-decoration:none;color:#1e293b;font-size:14px;transition:background .15s}
        .yvl-switcher__item a:hover{background:#f1f5f9}
        .yvl-switcher__item--active a{font-weight:700;color:var(--color-primary,#2563eb);background:rgba(37,99,235,.06)}
        .yvl-switcher__code{font-weight:700;font-size:12px;width:28px;text-align:center;padding:2px 0;border-radius:4px;background:#f1f5f9}
        .yvl-switcher__item--active .yvl-switcher__code{background:var(--color-primary,#2563eb);color:#fff}
        .yvl-switcher--dropdown{position:relative;display:inline-block}
        .yvl-switcher--dropdown .yvl-switcher__list{display:flex;gap:8px;flex-wrap:wrap}
        .yvl-switcher--dropdown .yvl-switcher__item a{padding:6px 12px;border-radius:6px;border:1px solid #e2e8f0}
        .yvl-switcher--dropdown .yvl-switcher__item--active a{border-color:var(--color-primary,#2563eb)}
        .yvl-switcher--dropdown .yvl-switcher__name{display:none}
        @media(max-width:768px){.yvl-switcher--floating{bottom:16px;left:16px}}
        </style>
        <?php
    }
}
