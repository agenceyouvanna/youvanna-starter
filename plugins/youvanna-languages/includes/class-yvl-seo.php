<?php
defined('ABSPATH') || exit;

/**
 * SEO integration — hreflang, canonical, sitemap, html lang.
 */
class YVL_SEO {

    public static function init(): void {
        add_action('wp_head', [__CLASS__, 'output_hreflang'], 1);
        add_filter('language_attributes', [__CLASS__, 'html_lang_attr']);
        add_action('init', [__CLASS__, 'register_sitemap_rewrites']);

        // Fix canonical for translated pages
        add_action('template_redirect', [__CLASS__, 'fix_canonical_hooks']);

        // Yoast sitemap integration
        add_filter('wpseo_sitemap_index', [__CLASS__, 'yoast_sitemap_index']);

        // Yoast canonical override
        add_filter('wpseo_canonical', [__CLASS__, 'filter_yoast_canonical']);
    }

    /**
     * Output hreflang tags.
     */
    public static function output_hreflang(): void {
        $langs = YVL_Languages::enabled();
        if (count($langs) < 2) return;

        echo "\n<!-- Youvanna Languages: hreflang -->\n";

        foreach ($langs as $lang) {
            $url = YVL_Router::url_for_lang($lang);
            printf(
                '<link rel="alternate" hreflang="%s" href="%s" />' . "\n",
                esc_attr($lang),
                esc_url($url)
            );
        }

        // x-default
        printf(
            '<link rel="alternate" hreflang="x-default" href="%s" />' . "\n",
            esc_url(YVL_Router::url_for_lang(YVL_Languages::default_lang()))
        );

        echo "<!-- /Youvanna Languages -->\n";
    }

    /**
     * Fix canonical URL — remove WP's built-in canonical on translated pages,
     * and let Yoast handle it if present (via wpseo_canonical filter).
     */
    public static function fix_canonical_hooks(): void {
        if (!YVL_Languages::is_translated()) return;

        // Remove WP's default canonical to avoid duplicate
        remove_action('wp_head', 'rel_canonical');

        // If Yoast is NOT active, output our own canonical
        if (!defined('WPSEO_VERSION')) {
            add_action('wp_head', [__CLASS__, 'output_canonical'], 2);
        }
    }

    /**
     * Output canonical for translated pages (when Yoast is absent).
     */
    public static function output_canonical(): void {
        $url = YVL_Router::url_for_lang(YVL_Languages::current());
        echo '<link rel="canonical" href="' . esc_url($url) . '" />' . "\n";
    }

    /**
     * Override Yoast canonical for translated pages.
     */
    public static function filter_yoast_canonical(string $canonical): string {
        if (!YVL_Languages::is_translated()) return $canonical;
        return YVL_Router::url_for_lang(YVL_Languages::current());
    }

    /**
     * Filter html lang attribute.
     */
    public static function html_lang_attr(string $output): string {
        if (!YVL_Languages::is_translated()) return $output;

        $info = YVL_Languages::get_info(YVL_Languages::current());
        if (!$info) return $output;

        $lang_attr = str_replace('_', '-', $info['locale']);
        $dir = $info['dir'] ?? 'ltr';

        return 'lang="' . esc_attr($lang_attr) . '" dir="' . esc_attr($dir) . '"';
    }

    /**
     * Register sitemap rewrite for /sitemap-{lang}.xml.
     */
    public static function register_sitemap_rewrites(): void {
        $langs = YVL_Languages::secondary();
        foreach ($langs as $lang) {
            add_rewrite_rule(
                '^sitemap-' . preg_quote($lang) . '\.xml$',
                'index.php?yvl_sitemap=' . $lang,
                'top'
            );
        }
        add_filter('query_vars', function ($vars) {
            $vars[] = 'yvl_sitemap';
            return $vars;
        });
        add_action('template_redirect', [__CLASS__, 'serve_sitemap']);
    }

    /**
     * Serve language-specific sitemap XML.
     */
    public static function serve_sitemap(): void {
        $lang = get_query_var('yvl_sitemap');
        if (!$lang || !YVL_Languages::is_enabled($lang)) return;

        header('Content-Type: application/xml; charset=UTF-8');
        header('X-Robots-Tag: noindex, follow');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        echo '        xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

        $home = untrailingslashit(get_option('home'));

        $posts = get_posts([
            'post_type'   => ['page', 'post'],
            'post_status' => 'publish',
            'numberposts' => -1,
        ]);

        foreach ($posts as $post) {
            $translations = YVL_DB::get_all($lang, 'post', $post->ID);
            if (empty($translations)) continue;

            $path = wp_make_link_relative(get_permalink($post));
            $url  = $home . '/' . $lang . $path;
            $mod  = get_post_modified_time('c', true, $post);

            echo "  <url>\n";
            echo "    <loc>" . esc_url($url) . "</loc>\n";
            echo "    <lastmod>" . esc_html($mod) . "</lastmod>\n";

            foreach (YVL_Languages::enabled() as $alt_lang) {
                $alt_url = ($alt_lang === YVL_Languages::default_lang())
                    ? $home . $path
                    : $home . '/' . $alt_lang . $path;
                echo '    <xhtml:link rel="alternate" hreflang="' . esc_attr($alt_lang) . '" href="' . esc_url($alt_url) . '" />' . "\n";
            }

            echo "  </url>\n";
        }

        echo "</urlset>\n";
        exit;
    }

    /**
     * Add language sitemaps to Yoast sitemap index.
     */
    public static function yoast_sitemap_index(string $sitemap_index): string {
        $langs = YVL_Languages::secondary();
        $home = untrailingslashit(get_option('home'));

        foreach ($langs as $lang) {
            $count = YVL_DB::count_lang($lang);
            if ($count > 0) {
                $sitemap_index .= sprintf(
                    '<sitemap><loc>%s/sitemap-%s.xml</loc><lastmod>%s</lastmod></sitemap>' . "\n",
                    esc_url($home),
                    esc_attr($lang),
                    esc_html(current_time('c'))
                );
            }
        }

        return $sitemap_index;
    }
}
