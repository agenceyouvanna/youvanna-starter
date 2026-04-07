<?php
defined('ABSPATH') || exit;

/**
 * Language registry — manages available languages and the current active language.
 */
class YVL_Languages {

    /** @var string|null Current language being viewed on frontend */
    private static ?string $current_lang = null;

    /** @var array<string, array{name:string, native:string, flag:string, locale:string, dir:string}> */
    private static array $catalog = [];

    public static function init(): void {
        self::$catalog = self::build_catalog();
    }

    /**
     * Full catalog of supported languages with metadata.
     */
    private static function build_catalog(): array {
        return [
            'fr' => ['name' => 'French',    'native' => 'Français',    'flag' => '🇫🇷', 'locale' => 'fr_FR', 'dir' => 'ltr'],
            'en' => ['name' => 'English',   'native' => 'English',     'flag' => '🇬🇧', 'locale' => 'en_US', 'dir' => 'ltr'],
            'de' => ['name' => 'German',    'native' => 'Deutsch',     'flag' => '🇩🇪', 'locale' => 'de_DE', 'dir' => 'ltr'],
            'es' => ['name' => 'Spanish',   'native' => 'Español',     'flag' => '🇪🇸', 'locale' => 'es_ES', 'dir' => 'ltr'],
            'it' => ['name' => 'Italian',   'native' => 'Italiano',    'flag' => '🇮🇹', 'locale' => 'it_IT', 'dir' => 'ltr'],
            'pt' => ['name' => 'Portuguese','native' => 'Português',   'flag' => '🇵🇹', 'locale' => 'pt_PT', 'dir' => 'ltr'],
            'nl' => ['name' => 'Dutch',     'native' => 'Nederlands',  'flag' => '🇳🇱', 'locale' => 'nl_NL', 'dir' => 'ltr'],
            'ru' => ['name' => 'Russian',   'native' => 'Русский',     'flag' => '🇷🇺', 'locale' => 'ru_RU', 'dir' => 'ltr'],
            'ar' => ['name' => 'Arabic',    'native' => 'العربية',     'flag' => '🇸🇦', 'locale' => 'ar',    'dir' => 'rtl'],
            'zh' => ['name' => 'Chinese',   'native' => '中文',         'flag' => '🇨🇳', 'locale' => 'zh_CN', 'dir' => 'ltr'],
            'ja' => ['name' => 'Japanese',  'native' => '日本語',       'flag' => '🇯🇵', 'locale' => 'ja',    'dir' => 'ltr'],
            'ko' => ['name' => 'Korean',    'native' => '한국어',       'flag' => '🇰🇷', 'locale' => 'ko_KR', 'dir' => 'ltr'],
            'pl' => ['name' => 'Polish',    'native' => 'Polski',      'flag' => '🇵🇱', 'locale' => 'pl_PL', 'dir' => 'ltr'],
            'tr' => ['name' => 'Turkish',   'native' => 'Türkçe',      'flag' => '🇹🇷', 'locale' => 'tr_TR', 'dir' => 'ltr'],
            'sv' => ['name' => 'Swedish',   'native' => 'Svenska',     'flag' => '🇸🇪', 'locale' => 'sv_SE', 'dir' => 'ltr'],
            'da' => ['name' => 'Danish',    'native' => 'Dansk',       'flag' => '🇩🇰', 'locale' => 'da_DK', 'dir' => 'ltr'],
            'no' => ['name' => 'Norwegian', 'native' => 'Norsk',       'flag' => '🇳🇴', 'locale' => 'nb_NO', 'dir' => 'ltr'],
            'fi' => ['name' => 'Finnish',   'native' => 'Suomi',       'flag' => '🇫🇮', 'locale' => 'fi',    'dir' => 'ltr'],
            'el' => ['name' => 'Greek',     'native' => 'Ελληνικά',    'flag' => '🇬🇷', 'locale' => 'el',    'dir' => 'ltr'],
            'cs' => ['name' => 'Czech',     'native' => 'Čeština',     'flag' => '🇨🇿', 'locale' => 'cs_CZ', 'dir' => 'ltr'],
            'ro' => ['name' => 'Romanian',  'native' => 'Română',      'flag' => '🇷🇴', 'locale' => 'ro_RO', 'dir' => 'ltr'],
            'hu' => ['name' => 'Hungarian', 'native' => 'Magyar',      'flag' => '🇭🇺', 'locale' => 'hu_HU', 'dir' => 'ltr'],
            'uk' => ['name' => 'Ukrainian', 'native' => 'Українська',  'flag' => '🇺🇦', 'locale' => 'uk',    'dir' => 'ltr'],
            'hr' => ['name' => 'Croatian',  'native' => 'Hrvatski',    'flag' => '🇭🇷', 'locale' => 'hr',    'dir' => 'ltr'],
            'bg' => ['name' => 'Bulgarian', 'native' => 'Български',   'flag' => '🇧🇬', 'locale' => 'bg_BG', 'dir' => 'ltr'],
            'lb' => ['name' => 'Luxembourgish','native'=>'Lëtzebuergesch','flag'=>'🇱🇺','locale'=>'lb', 'dir' => 'ltr'],
        ];
    }

    /**
     * Get the full catalog of all available languages.
     */
    public static function catalog(): array {
        return self::$catalog;
    }

    /**
     * Get info for a specific language code.
     */
    public static function get_info(string $code): ?array {
        return self::$catalog[$code] ?? null;
    }

    /**
     * Get the default (source) language.
     */
    public static function default_lang(): string {
        return get_option('yvl_default_lang', 'fr');
    }

    /**
     * Get all enabled languages (array of codes).
     */
    public static function enabled(): array {
        $langs = get_option('yvl_languages', ['fr']);
        return is_array($langs) ? $langs : ['fr'];
    }

    /**
     * Get enabled languages excluding the default.
     */
    public static function secondary(): array {
        $default = self::default_lang();
        return array_values(array_filter(self::enabled(), fn($l) => $l !== $default));
    }

    /**
     * Check if a language code is enabled.
     */
    public static function is_enabled(string $code): bool {
        return in_array($code, self::enabled(), true);
    }

    /**
     * Add a language.
     */
    public static function add(string $code): bool {
        if (!isset(self::$catalog[$code])) return false;
        $langs = self::enabled();
        if (in_array($code, $langs, true)) return false;
        $langs[] = $code;
        update_option('yvl_languages', $langs);
        flush_rewrite_rules();
        return true;
    }

    /**
     * Remove a language (cannot remove default).
     */
    public static function remove(string $code): bool {
        if ($code === self::default_lang()) return false;
        $langs = self::enabled();
        $langs = array_values(array_filter($langs, fn($l) => $l !== $code));
        update_option('yvl_languages', $langs);
        // Optionally delete translations
        YVL_DB::delete_lang($code);
        flush_rewrite_rules();
        return true;
    }

    /**
     * Set the default language.
     */
    public static function set_default(string $code): bool {
        if (!self::is_enabled($code)) return false;
        update_option('yvl_default_lang', $code);
        flush_rewrite_rules();
        return true;
    }

    /**
     * Get/set the current frontend language.
     */
    public static function current(): string {
        return self::$current_lang ?? self::default_lang();
    }

    public static function set_current(string $lang): void {
        self::$current_lang = $lang;
    }

    /**
     * Check if we're viewing a translated (non-default) language.
     */
    public static function is_translated(): bool {
        return self::$current_lang !== null && self::$current_lang !== self::default_lang();
    }
}
