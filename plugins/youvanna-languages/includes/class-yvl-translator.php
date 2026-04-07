<?php
defined('ABSPATH') || exit;

/**
 * Content translator — hooks into WordPress to swap content with translations.
 */
class YVL_Translator {

    /** @var array Cache to avoid repeated DB lookups per request */
    private static array $cache = [];

    /** @var array yv_* option keys to translate */
    private static array $option_keys = [
        'yv_phone', 'yv_email', 'yv_address', 'yv_city', 'yv_opening_hours',
        'yv_footer_description', 'yv_cta_text', 'yv_cta_link', 'yv_postal_code',
    ];

    public static function init(): void {
        if (is_admin()) return;

        // Post content filters
        add_filter('the_title',   [__CLASS__, 'filter_title'], 20, 2);
        add_filter('the_content', [__CLASS__, 'filter_content'], 20);
        add_filter('the_excerpt', [__CLASS__, 'filter_excerpt'], 20);
        add_filter('single_post_title', [__CLASS__, 'filter_single_post_title'], 20, 2);

        // Document title
        add_filter('document_title_parts', [__CLASS__, 'filter_document_title'], 20);

        // Bloginfo
        add_filter('option_blogname',      [__CLASS__, 'filter_blogname'], 20);
        add_filter('option_blogdescription',[__CLASS__, 'filter_blogdescription'], 20);

        // SCF / ACF fields — hook both format and load for maximum compat
        add_filter('acf/format_value', [__CLASS__, 'filter_acf_value'], 20, 3);

        // wp_options — individual pre_option_{$key} hooks for each yv_* option
        foreach (self::$option_keys as $opt) {
            add_filter('pre_option_' . $opt, function ($pre) use ($opt) {
                if (!YVL_Languages::is_translated()) return $pre;
                $t = YVL_DB::get(YVL_Languages::current(), 'option', 0, $opt);
                return $t !== null ? $t : $pre;
            });
        }

        // Nav menus
        add_filter('nav_menu_item_title', [__CLASS__, 'filter_menu_item_title'], 20, 4);

        // Yoast SEO
        add_filter('wpseo_title',           [__CLASS__, 'filter_yoast_title'], 20);
        add_filter('wpseo_metadesc',        [__CLASS__, 'filter_yoast_desc'], 20);
        add_filter('wpseo_opengraph_title', [__CLASS__, 'filter_yoast_title'], 20);
        add_filter('wpseo_opengraph_desc',  [__CLASS__, 'filter_yoast_desc'], 20);

        // Post slug translation for display
        add_filter('post_link',      [__CLASS__, 'filter_post_link'], 20, 2);
        add_filter('page_link',      [__CLASS__, 'filter_page_link'], 20, 2);
        add_filter('post_type_link', [__CLASS__, 'filter_post_link'], 20, 2);
    }

    // ============================================
    // HELPERS
    // ============================================

    private static function translated(string $object_type, int $object_id, string $field_key): ?string {
        if (!YVL_Languages::is_translated()) return null;

        $lang = YVL_Languages::current();
        $cache_key = "{$lang}:{$object_type}:{$object_id}:{$field_key}";

        if (array_key_exists($cache_key, self::$cache)) {
            return self::$cache[$cache_key];
        }

        $value = YVL_DB::get($lang, $object_type, $object_id, $field_key);
        self::$cache[$cache_key] = $value;
        return $value;
    }

    private static function current_post_id(): int {
        $id = get_the_ID();
        return $id ? (int) $id : 0;
    }

    // ============================================
    // POST CONTENT FILTERS
    // ============================================

    public static function filter_title(string $title, $post_id = 0): string {
        if (!YVL_Languages::is_translated()) return $title;
        $id = is_numeric($post_id) ? (int) $post_id : 0;
        if (!$id) return $title;
        $t = self::translated('post', $id, 'post_title');
        return $t !== null ? $t : $title;
    }

    public static function filter_content(string $content): string {
        if (!YVL_Languages::is_translated()) return $content;
        $id = self::current_post_id();
        if (!$id) return $content;
        $t = self::translated('post', $id, 'post_content');
        return $t !== null ? wp_kses_post($t) : $content;
    }

    public static function filter_excerpt(string $excerpt): string {
        if (!YVL_Languages::is_translated()) return $excerpt;
        $id = self::current_post_id();
        if (!$id) return $excerpt;
        $t = self::translated('post', $id, 'post_excerpt');
        return $t !== null ? $t : $excerpt;
    }

    public static function filter_single_post_title(string $title, $post): string {
        if (!YVL_Languages::is_translated() || !$post) return $title;
        $t = self::translated('post', $post->ID, 'post_title');
        return $t !== null ? $t : $title;
    }

    // ============================================
    // DOCUMENT TITLE
    // ============================================

    public static function filter_document_title(array $parts): array {
        if (!YVL_Languages::is_translated()) return $parts;

        $id = self::current_post_id();
        if ($id) {
            $t = self::translated('post', $id, 'post_title');
            if ($t !== null) $parts['title'] = $t;
        }

        $lang = YVL_Languages::current();
        $site = YVL_DB::get($lang, 'option', 0, 'blogname');
        if ($site) $parts['site'] = $site;

        return $parts;
    }

    // ============================================
    // BLOGINFO
    // ============================================

    public static function filter_blogname($value) {
        if (!YVL_Languages::is_translated()) return $value;
        $t = YVL_DB::get(YVL_Languages::current(), 'option', 0, 'blogname');
        return $t ?: $value;
    }

    public static function filter_blogdescription($value) {
        if (!YVL_Languages::is_translated()) return $value;
        $t = YVL_DB::get(YVL_Languages::current(), 'option', 0, 'blogdescription');
        return $t ?: $value;
    }

    // ============================================
    // SCF / ACF FIELDS — $post_id can be string ("user_1", "option", etc.)
    // ============================================

    public static function filter_acf_value($value, $post_id, $field) {
        if (!YVL_Languages::is_translated()) return $value;
        if (!is_numeric($post_id) || !$post_id) return $value;
        if (!is_array($field) || !isset($field['name'])) return $value;

        // Only translate text-based fields
        $text_types = ['text', 'textarea', 'wysiwyg', 'url', 'email', 'number'];
        if (!in_array($field['type'] ?? '', $text_types, true)) return $value;

        $field_key = 'scf:' . $field['name'];
        $t = self::translated('post', (int) $post_id, $field_key);
        return $t !== null ? $t : $value;
    }

    // ============================================
    // NAV MENUS
    // ============================================

    public static function filter_menu_item_title(string $title, $item, $args, $depth): string {
        if (!YVL_Languages::is_translated() || !$item) return $title;
        $t = self::translated('menu', (int) $item->ID, 'title');
        return $t !== null ? $t : $title;
    }

    // ============================================
    // YOAST SEO
    // ============================================

    public static function filter_yoast_title(string $title): string {
        if (!YVL_Languages::is_translated()) return $title;
        $id = self::current_post_id();
        if (!$id) return $title;
        $t = self::translated('yoast', $id, 'title');
        return $t !== null ? $t : $title;
    }

    public static function filter_yoast_desc(string $desc): string {
        if (!YVL_Languages::is_translated()) return $desc;
        $id = self::current_post_id();
        if (!$id) return $desc;
        $t = self::translated('yoast', $id, 'description');
        return $t !== null ? $t : $desc;
    }

    // ============================================
    // SLUG / PERMALINK TRANSLATION
    // ============================================

    public static function filter_post_link(string $url, $post): string {
        if (!YVL_Languages::is_translated()) return $url;
        if (!$post || !isset($post->ID)) return $url;
        $slug = self::translated('post', $post->ID, 'post_slug');
        if ($slug) {
            $url = preg_replace('/' . preg_quote($post->post_name, '/') . '/', $slug, $url, 1);
        }
        return $url;
    }

    public static function filter_page_link(string $url, $post_id): string {
        if (!YVL_Languages::is_translated()) return $url;
        $post_id = (int) $post_id;
        if (!$post_id) return $url;
        $post = get_post($post_id);
        if (!$post) return $url;
        $slug = self::translated('post', $post_id, 'post_slug');
        if ($slug) {
            $url = preg_replace('/' . preg_quote($post->post_name, '/') . '/', $slug, $url, 1);
        }
        return $url;
    }

    // ============================================
    // CACHE CONTROL
    // ============================================

    public static function flush_cache(): void {
        self::$cache = [];
    }
}
