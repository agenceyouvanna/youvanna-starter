<?php
defined('ABSPATH') || exit;

/**
 * Database schema and lifecycle.
 *
 * Table: {prefix}yvl_translations
 *   - id            BIGINT AUTO_INCREMENT
 *   - lang          VARCHAR(10)     e.g. 'en', 'de', 'es'
 *   - object_type   VARCHAR(30)     'post', 'term', 'option', 'menu', 'yoast'
 *   - object_id     BIGINT          post ID, term ID, 0 for options
 *   - field_key     VARCHAR(255)    field name (post_title, post_content, meta:hero_title, option:yv_phone, etc.)
 *   - field_value   LONGTEXT        translated value
 *   - updated_at    DATETIME
 */
class YVL_DB {

    const TABLE = 'yvl_translations';
    const DB_VERSION = '1.0.0';

    /**
     * Get the full table name with prefix.
     */
    public static function table(): string {
        global $wpdb;
        return $wpdb->prefix . self::TABLE;
    }

    /**
     * Plugin activation — create table + default options.
     */
    public static function activate(): void {
        global $wpdb;
        $table   = self::table();
        $charset = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            lang VARCHAR(10) NOT NULL,
            object_type VARCHAR(30) NOT NULL DEFAULT 'post',
            object_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
            field_key VARCHAR(255) NOT NULL,
            field_value LONGTEXT,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY lang_object_field (lang, object_type, object_id, field_key(191)),
            KEY object_lookup (object_type, object_id),
            KEY lang_idx (lang)
        ) {$charset};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        // Default options
        if (!get_option('yvl_default_lang')) {
            update_option('yvl_default_lang', 'fr', true);
        }
        if (!get_option('yvl_languages')) {
            update_option('yvl_languages', ['fr'], true);
        }

        update_option('yvl_db_version', self::DB_VERSION, true);

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation — flush rewrites only (keep data).
     */
    public static function deactivate(): void {
        flush_rewrite_rules();
    }

    // ============================================
    // CRUD helpers
    // ============================================

    /**
     * Get a translated value.
     */
    public static function get(string $lang, string $object_type, int $object_id, string $field_key): ?string {
        global $wpdb;
        $table = self::table();

        return $wpdb->get_var($wpdb->prepare(
            "SELECT field_value FROM {$table} WHERE lang = %s AND object_type = %s AND object_id = %d AND field_key = %s",
            $lang, $object_type, $object_id, $field_key
        ));
    }

    /**
     * Get all translations for an object in a language.
     */
    public static function get_all(string $lang, string $object_type, int $object_id): array {
        global $wpdb;
        $table = self::table();

        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT field_key, field_value FROM {$table} WHERE lang = %s AND object_type = %s AND object_id = %d",
            $lang, $object_type, $object_id
        ), ARRAY_A);

        $out = [];
        foreach ($rows as $row) {
            $out[$row['field_key']] = $row['field_value'];
        }
        return $out;
    }

    /**
     * Get all translations for a language (all objects of a type).
     */
    public static function get_by_lang(string $lang, string $object_type = 'post'): array {
        global $wpdb;
        $table = self::table();

        return $wpdb->get_results($wpdb->prepare(
            "SELECT object_id, field_key, field_value FROM {$table} WHERE lang = %s AND object_type = %s",
            $lang, $object_type
        ), ARRAY_A);
    }

    /**
     * Set (upsert) a translation.
     */
    public static function set(string $lang, string $object_type, int $object_id, string $field_key, ?string $field_value): bool {
        global $wpdb;
        $table = self::table();

        if ($field_value === null || $field_value === '') {
            // Delete empty translations
            $wpdb->delete($table, [
                'lang'        => $lang,
                'object_type' => $object_type,
                'object_id'   => $object_id,
                'field_key'   => $field_key,
            ]);
            return true;
        }

        // REPLACE INTO for upsert (uses UNIQUE key)
        $result = $wpdb->replace($table, [
            'lang'        => $lang,
            'object_type' => $object_type,
            'object_id'   => $object_id,
            'field_key'   => $field_key,
            'field_value' => $field_value,
            'updated_at'  => current_time('mysql'),
        ], ['%s', '%s', '%d', '%s', '%s', '%s']);

        return $result !== false;
    }

    /**
     * Bulk set translations for an object.
     */
    public static function set_bulk(string $lang, string $object_type, int $object_id, array $fields): void {
        foreach ($fields as $key => $value) {
            self::set($lang, $object_type, $object_id, $key, $value);
        }
    }

    /**
     * Delete all translations for an object.
     */
    public static function delete_object(string $object_type, int $object_id): int {
        global $wpdb;
        return (int) $wpdb->delete(self::table(), [
            'object_type' => $object_type,
            'object_id'   => $object_id,
        ]);
    }

    /**
     * Delete all translations for a language.
     */
    public static function delete_lang(string $lang): int {
        global $wpdb;
        return (int) $wpdb->delete(self::table(), ['lang' => $lang]);
    }

    /**
     * Count translations for a language.
     */
    public static function count_lang(string $lang): int {
        global $wpdb;
        $table = self::table();
        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE lang = %s AND field_value != ''",
            $lang
        ));
    }

    /**
     * Export all translations for a language as array.
     */
    public static function export_lang(string $lang): array {
        global $wpdb;
        $table = self::table();

        return $wpdb->get_results($wpdb->prepare(
            "SELECT object_type, object_id, field_key, field_value FROM {$table} WHERE lang = %s ORDER BY object_type, object_id, field_key",
            $lang
        ), ARRAY_A);
    }

    /**
     * Import translations from array.
     */
    public static function import_lang(string $lang, array $rows): int {
        $count = 0;
        foreach ($rows as $row) {
            if (!is_array($row)) continue;
            if (!isset($row['object_type'], $row['object_id'], $row['field_key'], $row['field_value'])) continue;
            if (self::set($lang, $row['object_type'], (int) $row['object_id'], $row['field_key'], $row['field_value'])) {
                $count++;
            }
        }
        return $count;
    }
}
