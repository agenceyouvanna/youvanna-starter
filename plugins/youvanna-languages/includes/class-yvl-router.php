<?php
defined('ABSPATH') || exit;

/**
 * URL Router — handles /{lang}/ URL prefix routing.
 *
 * Default language = no prefix. Secondary languages = /{lang}/ prefix.
 */
class YVL_Router {

    /** @var bool Prevent re-entry in home_url filter */
    private static bool $filtering_url = false;

    public static function init(): void {
        add_action('init', [__CLASS__, 'add_rewrite_rules'], 1);
        add_filter('query_vars', [__CLASS__, 'add_query_vars']);
        add_action('parse_request', [__CLASS__, 'detect_language']);
        add_filter('home_url', [__CLASS__, 'filter_home_url'], 20, 4);
        add_filter('redirect_canonical', [__CLASS__, 'prevent_canonical_redirect'], 10, 2);
        add_filter('locale', [__CLASS__, 'filter_locale']);
    }

    /**
     * Register rewrite rules for each secondary language.
     */
    public static function add_rewrite_rules(): void {
        $langs = YVL_Languages::secondary();
        if (empty($langs)) return;

        $lang_pattern = implode('|', array_map('preg_quote', $langs));

        // Homepage: /{lang}/
        add_rewrite_rule(
            '^(' . $lang_pattern . ')/?$',
            'index.php?yvl_lang=$matches[1]',
            'top'
        );
        // Inner pages: /{lang}/anything
        add_rewrite_rule(
            '^(' . $lang_pattern . ')/(.+?)/?$',
            'index.php?yvl_lang=$matches[1]&yvl_path=$matches[2]',
            'top'
        );
    }

    public static function add_query_vars(array $vars): array {
        $vars[] = 'yvl_lang';
        $vars[] = 'yvl_path';
        return $vars;
    }

    /**
     * Detect language from URL and re-route.
     */
    public static function detect_language(\WP $wp): void {
        $lang = $wp->query_vars['yvl_lang'] ?? '';
        if (!$lang || !YVL_Languages::is_enabled($lang) || $lang === YVL_Languages::default_lang()) {
            return;
        }

        YVL_Languages::set_current($lang);
        $path = $wp->query_vars['yvl_path'] ?? '';

        // Clear our custom vars
        unset($wp->query_vars['yvl_lang'], $wp->query_vars['yvl_path']);

        if ($path) {
            // Re-parse the inner path — remove our hook first to prevent recursion
            $wp->request = $path;
            $wp->query_string = '';
            $wp->matched_rule = '';
            $wp->matched_query = '';

            remove_action('parse_request', [__CLASS__, 'detect_language']);
            $wp->parse_request();
            add_action('parse_request', [__CLASS__, 'detect_language']);
        } else {
            // Language root = front page
            $front_id = (int) get_option('page_on_front');
            if ($front_id) {
                $wp->query_vars = ['page_id' => $front_id];
            } else {
                $wp->query_vars = [];
            }
        }
    }

    /**
     * Prefix home_url for translated languages.
     */
    public static function filter_home_url(string $url, string $path, $scheme, $blog_id): string {
        if (self::$filtering_url) return $url;
        if (!YVL_Languages::is_translated()) return $url;
        if (is_admin()) return $url;

        self::$filtering_url = true;
        $home = untrailingslashit(get_option('home'));
        self::$filtering_url = false;

        $lang = YVL_Languages::current();

        if (strpos($url, $home) === 0) {
            $relative = substr($url, strlen($home));
            if (strpos($relative, '/' . $lang . '/') !== 0 && $relative !== '/' . $lang) {
                $url = $home . '/' . $lang . $relative;
            }
        }

        return $url;
    }

    /**
     * Build URL for a specific language (for switcher / hreflang).
     */
    public static function url_for_lang(string $lang): string {
        self::$filtering_url = true;
        $home = untrailingslashit(get_option('home'));
        self::$filtering_url = false;

        // Current page path
        $path = '/';
        if (is_singular()) {
            $permalink = get_permalink();
            if ($permalink) {
                $path = wp_make_link_relative($permalink);
            }
        } elseif (is_front_page() || is_home()) {
            $path = '/';
        } elseif ((is_category() || is_tag() || is_tax()) && get_queried_object()) {
            $link = get_term_link(get_queried_object());
            if (!is_wp_error($link)) {
                $path = wp_make_link_relative($link);
            }
        }

        // Strip any existing language prefix from path
        foreach (YVL_Languages::secondary() as $l) {
            if (strpos($path, '/' . $l . '/') === 0) {
                $path = substr($path, strlen($l) + 1);
                break;
            }
            if ($path === '/' . $l) {
                $path = '/';
                break;
            }
        }

        if ($lang === YVL_Languages::default_lang()) {
            return $home . $path;
        }

        return $home . '/' . $lang . $path;
    }

    /**
     * Prevent WordPress from redirecting translated URLs.
     */
    public static function prevent_canonical_redirect($redirect_url, $requested_url) {
        if (YVL_Languages::is_translated()) return false;
        return $redirect_url;
    }

    /**
     * Switch WordPress locale.
     */
    public static function filter_locale(string $locale): string {
        if (!YVL_Languages::is_translated()) return $locale;
        $info = YVL_Languages::get_info(YVL_Languages::current());
        return $info ? $info['locale'] : $locale;
    }
}
