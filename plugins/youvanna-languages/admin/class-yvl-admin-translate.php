<?php
defined('ABSPATH') || exit;

/**
 * Admin translation page — one long page with ALL translatable content.
 *
 * Sections:
 * 1. Site settings (blogname, blogdescription)
 * 2. Theme options (yv_* options)
 * 3. Each page (title, content, SCF fields, Yoast meta)
 * 4. Each post (title, content, excerpt, Yoast meta)
 * 5. Menu items
 */
class YVL_Admin_Translate {

    public static function init(): void {
        add_action('admin_init', [__CLASS__, 'handle_save']);
    }

    /**
     * Handle form submission — save all translations.
     */
    public static function handle_save(): void {
        if (!isset($_POST['yvl_save_translations'])) return;
        if (!current_user_can('manage_options')) return;
        if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'yvl_save_translations')) return;

        $lang = sanitize_text_field($_POST['yvl_lang'] ?? '');
        if (!$lang || !YVL_Languages::is_enabled($lang)) return;

        $translations = $_POST['yvl'] ?? [];
        $count = 0;

        foreach ($translations as $object_type => $objects) {
            foreach ($objects as $object_id => $fields) {
                $object_id = (int) $object_id;
                foreach ($fields as $field_key => $value) {
                    $field_key = sanitize_text_field($field_key);
                    // Allow HTML for content fields, sanitize others
                    if (in_array($field_key, ['post_content', 'post_excerpt'], true) || strpos($field_key, 'scf:') === 0) {
                        $value = wp_kses_post($value);
                    } else {
                        $value = sanitize_text_field($value);
                    }
                    YVL_DB::set($lang, $object_type, $object_id, $field_key, $value);
                    if ($value !== '') $count++;
                }
            }
        }

        add_settings_error('yvl', 'saved', $count . ' traductions enregistrées.', 'success');
    }

    /**
     * Main translation page.
     */
    public static function page_translate(): void {
        $lang = sanitize_text_field($_GET['lang'] ?? '');
        $secondary = YVL_Languages::secondary();

        // If no lang selected, show picker
        if (!$lang || !YVL_Languages::is_enabled($lang)) {
            self::render_lang_picker($secondary);
            return;
        }

        $lang_info = YVL_Languages::get_info($lang);
        ?>
        <div class="wrap yvl-wrap">
            <h1>
                Traduire en <?php echo esc_html($lang_info['native'] ?? strtoupper($lang)); ?>
                <span class="yvl-badge yvl-badge--primary"><?php echo esc_html(strtoupper($lang)); ?></span>
            </h1>

            <p class="description">
                Remplissez les traductions ci-dessous. Les champs vides utiliseront le contenu original (<?php echo esc_html(strtoupper(YVL_Languages::default_lang())); ?>).
                <br>Le champ de gauche montre le contenu original, le champ de droite est la traduction.
            </p>

            <?php settings_errors('yvl'); ?>

            <form method="post" id="yvl-translate-form">
                <?php wp_nonce_field('yvl_save_translations'); ?>
                <input type="hidden" name="yvl_lang" value="<?php echo esc_attr($lang); ?>">
                <input type="hidden" name="yvl_save_translations" value="1">

                <div class="yvl-sticky-bar">
                    <button type="submit" class="button button-primary button-hero">
                        Enregistrer toutes les traductions
                    </button>
                    <span class="yvl-progress" id="yvl-progress"></span>
                </div>

                <!-- TOC -->
                <div class="yvl-toc">
                    <strong>Navigation rapide :</strong>
                    <a href="#yvl-section-site">Site</a> |
                    <a href="#yvl-section-options">Options thème</a> |
                    <a href="#yvl-section-pages">Pages</a> |
                    <a href="#yvl-section-posts">Articles</a> |
                    <a href="#yvl-section-menus">Menus</a>
                </div>

                <?php
                self::render_site_settings($lang);
                self::render_theme_options($lang);
                self::render_pages($lang);
                self::render_posts($lang);
                self::render_menus($lang);
                ?>

                <div style="margin-top:24px">
                    <button type="submit" class="button button-primary button-hero">
                        Enregistrer toutes les traductions
                    </button>
                </div>
            </form>
        </div>
        <?php
    }

    /**
     * Language picker when no lang is selected.
     */
    private static function render_lang_picker(array $secondary): void {
        ?>
        <div class="wrap yvl-wrap">
            <h1>Traduire</h1>
            <?php if (empty($secondary)): ?>
                <div class="notice notice-warning"><p>
                    Ajoutez d'abord une langue secondaire dans
                    <a href="<?php echo esc_url(admin_url('admin.php?page=yvl-languages')); ?>">Langues → Gérer les langues</a>.
                </p></div>
            <?php else: ?>
                <p>Choisissez une langue à traduire :</p>
                <div class="yvl-lang-cards">
                    <?php foreach ($secondary as $code):
                        $info = YVL_Languages::get_info($code);
                        $count = YVL_DB::count_lang($code);
                    ?>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=yvl-translate&lang=' . $code)); ?>" class="yvl-lang-card">
                            <strong><?php echo esc_html($info['native'] ?? $code); ?></strong>
                            <span><?php echo esc_html(strtoupper($code)); ?></span>
                            <small><?php echo (int) $count; ?> traductions</small>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    // ============================================
    // SECTION RENDERERS
    // ============================================

    /**
     * Section: Site settings (blogname, blogdescription).
     */
    private static function render_site_settings(string $lang): void {
        $fields = [
            'blogname'        => 'Nom du site',
            'blogdescription' => 'Slogan / Description',
        ];
        ?>
        <div class="yvl-section" id="yvl-section-site">
            <h2 class="yvl-section-title">Paramètres du site</h2>
            <table class="form-table yvl-table">
                <?php foreach ($fields as $key => $label):
                    $original = get_option($key, '');
                    $translated = YVL_DB::get($lang, 'option', 0, $key) ?? '';
                ?>
                <tr>
                    <th><?php echo esc_html($label); ?></th>
                    <td>
                        <div class="yvl-side-by-side">
                            <div class="yvl-original">
                                <label>Original</label>
                                <input type="text" value="<?php echo esc_attr($original); ?>" readonly disabled>
                            </div>
                            <div class="yvl-translated">
                                <label>Traduction</label>
                                <input type="text" name="yvl[option][0][<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($translated); ?>" placeholder="<?php echo esc_attr($original); ?>">
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php
    }

    /**
     * Section: Theme options (yv_*).
     */
    private static function render_theme_options(string $lang): void {
        $options = [
            'yv_phone'              => 'Téléphone',
            'yv_email'              => 'Email',
            'yv_address'            => 'Adresse',
            'yv_city'               => 'Ville',
            'yv_opening_hours'      => 'Horaires (texte)',
            'yv_footer_description' => 'Description footer',
            'yv_cta_text'           => 'Texte bouton CTA header',
        ];
        ?>
        <div class="yvl-section" id="yvl-section-options">
            <h2 class="yvl-section-title">Options du thème</h2>
            <table class="form-table yvl-table">
                <?php foreach ($options as $key => $label):
                    $original = get_option($key, '');
                    $translated = YVL_DB::get($lang, 'option', 0, $key) ?? '';
                ?>
                <tr>
                    <th><?php echo esc_html($label); ?></th>
                    <td>
                        <div class="yvl-side-by-side">
                            <div class="yvl-original">
                                <label>Original</label>
                                <input type="text" value="<?php echo esc_attr($original); ?>" readonly disabled>
                            </div>
                            <div class="yvl-translated">
                                <label>Traduction</label>
                                <input type="text" name="yvl[option][0][<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($translated); ?>" placeholder="<?php echo esc_attr($original); ?>">
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php
    }

    /**
     * Section: Pages (title, content, SCF fields, Yoast).
     */
    private static function render_pages(string $lang): void {
        $pages = get_posts([
            'post_type'   => 'page',
            'post_status' => 'publish',
            'numberposts' => -1,
            'orderby'     => 'menu_order',
            'order'       => 'ASC',
        ]);
        ?>
        <div class="yvl-section" id="yvl-section-pages">
            <h2 class="yvl-section-title">Pages (<?php echo count($pages); ?>)</h2>
            <?php foreach ($pages as $page): ?>
                <?php self::render_post_block($lang, $page); ?>
            <?php endforeach; ?>
        </div>
        <?php
    }

    /**
     * Section: Posts.
     */
    private static function render_posts(string $lang): void {
        $posts = get_posts([
            'post_type'   => 'post',
            'post_status' => 'publish',
            'numberposts' => -1,
            'orderby'     => 'date',
            'order'       => 'DESC',
        ]);
        ?>
        <div class="yvl-section" id="yvl-section-posts">
            <h2 class="yvl-section-title">Articles (<?php echo count($posts); ?>)</h2>
            <?php foreach ($posts as $post): ?>
                <?php self::render_post_block($lang, $post); ?>
            <?php endforeach; ?>
            <?php if (empty($posts)): ?>
                <p class="description">Aucun article publié.</p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Render a translatable block for a single post/page.
     */
    private static function render_post_block(string $lang, \WP_Post $post): void {
        $id = $post->ID;
        $existing = YVL_DB::get_all($lang, 'post', $id);
        $yoast_existing = YVL_DB::get_all($lang, 'yoast', $id);

        // Standard fields
        $standard_fields = [
            'post_title'   => ['label' => 'Titre', 'type' => 'text', 'original' => $post->post_title],
            'post_slug'    => ['label' => 'Slug URL', 'type' => 'text', 'original' => $post->post_name],
            'post_content' => ['label' => 'Contenu', 'type' => 'textarea', 'original' => $post->post_content],
            'post_excerpt' => ['label' => 'Extrait', 'type' => 'text', 'original' => $post->post_excerpt],
        ];

        // Yoast SEO fields
        $yoast_title = get_post_meta($id, '_yoast_wpseo_title', true);
        $yoast_desc  = get_post_meta($id, '_yoast_wpseo_metadesc', true);

        // SCF fields
        $scf_fields = self::get_scf_fields($id);

        $filled = 0;
        $total = count($standard_fields) + count($scf_fields) + 2; // +2 for yoast
        foreach ($existing as $val) { if ($val !== '') $filled++; }
        foreach ($yoast_existing as $val) { if ($val !== '') $filled++; }
        $pct = $total > 0 ? round(($filled / $total) * 100) : 0;
        ?>
        <div class="yvl-post-block" data-post-id="<?php echo (int) $id; ?>">
            <h3 class="yvl-post-block__header">
                <button type="button" class="yvl-toggle" aria-expanded="false">
                    <span class="yvl-post-block__title"><?php echo esc_html($post->post_title); ?></span>
                    <span class="yvl-badge yvl-badge--<?php echo $pct === 100 ? 'success' : ($pct > 0 ? 'warning' : 'default'); ?>">
                        <?php echo $pct; ?>%
                    </span>
                    <span class="dashicons dashicons-arrow-down-alt2"></span>
                </button>
            </h3>
            <div class="yvl-post-block__body" style="display:none">
                <table class="form-table yvl-table">
                    <?php // Standard fields
                    foreach ($standard_fields as $key => $field):
                        $translated = $existing[$key] ?? '';
                    ?>
                    <tr>
                        <th><?php echo esc_html($field['label']); ?></th>
                        <td>
                            <div class="yvl-side-by-side">
                                <div class="yvl-original">
                                    <label>Original</label>
                                    <?php if ($field['type'] === 'textarea'): ?>
                                        <textarea readonly disabled rows="4"><?php echo esc_textarea($field['original']); ?></textarea>
                                    <?php else: ?>
                                        <input type="text" value="<?php echo esc_attr($field['original']); ?>" readonly disabled>
                                    <?php endif; ?>
                                </div>
                                <div class="yvl-translated">
                                    <label>Traduction</label>
                                    <?php if ($field['type'] === 'textarea'): ?>
                                        <textarea name="yvl[post][<?php echo (int) $id; ?>][<?php echo esc_attr($key); ?>]" rows="4" placeholder="<?php echo esc_attr(mb_substr($field['original'], 0, 80) . '...'); ?>"><?php echo esc_textarea($translated); ?></textarea>
                                    <?php else: ?>
                                        <input type="text" name="yvl[post][<?php echo (int) $id; ?>][<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($translated); ?>" placeholder="<?php echo esc_attr($field['original']); ?>">
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <!-- Yoast SEO -->
                    <tr>
                        <th>SEO Titre <small>(Yoast)</small></th>
                        <td>
                            <div class="yvl-side-by-side">
                                <div class="yvl-original">
                                    <label>Original</label>
                                    <input type="text" value="<?php echo esc_attr($yoast_title); ?>" readonly disabled>
                                </div>
                                <div class="yvl-translated">
                                    <label>Traduction</label>
                                    <input type="text" name="yvl[yoast][<?php echo (int) $id; ?>][title]" value="<?php echo esc_attr($yoast_existing['title'] ?? ''); ?>" placeholder="<?php echo esc_attr($yoast_title); ?>">
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>SEO Description <small>(Yoast)</small></th>
                        <td>
                            <div class="yvl-side-by-side">
                                <div class="yvl-original">
                                    <label>Original</label>
                                    <input type="text" value="<?php echo esc_attr($yoast_desc); ?>" readonly disabled>
                                </div>
                                <div class="yvl-translated">
                                    <label>Traduction</label>
                                    <input type="text" name="yvl[yoast][<?php echo (int) $id; ?>][description]" value="<?php echo esc_attr($yoast_existing['description'] ?? ''); ?>" placeholder="<?php echo esc_attr($yoast_desc); ?>">
                                </div>
                            </div>
                        </td>
                    </tr>

                    <?php // SCF fields
                    foreach ($scf_fields as $scf_key => $scf):
                        $full_key = 'scf:' . $scf_key;
                        $translated = $existing[$full_key] ?? '';
                    ?>
                    <tr>
                        <th><?php echo esc_html($scf['label']); ?> <small>(SCF)</small></th>
                        <td>
                            <div class="yvl-side-by-side">
                                <div class="yvl-original">
                                    <label>Original</label>
                                    <?php if ($scf['type'] === 'textarea'): ?>
                                        <textarea readonly disabled rows="3"><?php echo esc_textarea($scf['value']); ?></textarea>
                                    <?php else: ?>
                                        <input type="text" value="<?php echo esc_attr($scf['value']); ?>" readonly disabled>
                                    <?php endif; ?>
                                </div>
                                <div class="yvl-translated">
                                    <label>Traduction</label>
                                    <?php if ($scf['type'] === 'textarea'): ?>
                                        <textarea name="yvl[post][<?php echo (int) $id; ?>][<?php echo esc_attr($full_key); ?>]" rows="3" placeholder="<?php echo esc_attr(mb_substr($scf['value'], 0, 80)); ?>"><?php echo esc_textarea($translated); ?></textarea>
                                    <?php else: ?>
                                        <input type="text" name="yvl[post][<?php echo (int) $id; ?>][<?php echo esc_attr($full_key); ?>]" value="<?php echo esc_attr($translated); ?>" placeholder="<?php echo esc_attr($scf['value']); ?>">
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
        <?php
    }

    /**
     * Get all text-based SCF fields for a post.
     */
    private static function get_scf_fields(int $post_id): array {
        if (!function_exists('get_field_objects')) return [];

        $field_objects = get_field_objects($post_id);
        if (!$field_objects || !is_array($field_objects)) return [];

        $text_types = ['text', 'textarea', 'wysiwyg', 'url', 'email'];
        $fields = [];

        foreach ($field_objects as $name => $obj) {
            if (!isset($obj['type'])) continue;

            if (in_array($obj['type'], $text_types, true)) {
                $value = $obj['value'] ?? '';
                if (is_string($value) && $value !== '') {
                    $fields[$name] = [
                        'label' => $obj['label'] ?? $name,
                        'type'  => in_array($obj['type'], ['textarea', 'wysiwyg'], true) ? 'textarea' : 'text',
                        'value' => $value,
                    ];
                }
            } elseif ($obj['type'] === 'repeater' && is_array($obj['value'] ?? null)) {
                // Flatten repeater text fields
                self::flatten_repeater($fields, $name, $obj, $post_id);
            } elseif ($obj['type'] === 'flexible_content' && is_array($obj['value'] ?? null)) {
                // Flatten flexible content text fields
                self::flatten_flexible($fields, $name, $obj, $post_id);
            }
        }

        return $fields;
    }

    /**
     * Flatten repeater fields into translatable text fields.
     */
    private static function flatten_repeater(array &$fields, string $prefix, array $obj, int $post_id): void {
        $text_types = ['text', 'textarea', 'wysiwyg', 'url', 'email'];
        $rows = $obj['value'] ?? [];
        if (!is_array($rows)) return;

        $sub_fields = $obj['sub_fields'] ?? [];

        foreach ($rows as $i => $row) {
            if (!is_array($row)) continue;
            foreach ($sub_fields as $sub) {
                if (!in_array($sub['type'] ?? '', $text_types, true)) continue;
                $sub_name = $sub['name'] ?? '';
                $value = $row[$sub_name] ?? '';
                if (is_string($value) && $value !== '') {
                    $key = $prefix . '.' . $i . '.' . $sub_name;
                    $fields[$key] = [
                        'label' => ($obj['label'] ?? $prefix) . ' [' . ($i + 1) . '] → ' . ($sub['label'] ?? $sub_name),
                        'type'  => in_array($sub['type'], ['textarea', 'wysiwyg'], true) ? 'textarea' : 'text',
                        'value' => $value,
                    ];
                }
            }
        }
    }

    /**
     * Flatten flexible content fields.
     */
    private static function flatten_flexible(array &$fields, string $prefix, array $obj, int $post_id): void {
        $text_types = ['text', 'textarea', 'wysiwyg', 'url', 'email'];
        $layouts = $obj['value'] ?? [];
        if (!is_array($layouts)) return;

        // Get layout definitions from sub_fields/layouts
        $layout_defs = $obj['layouts'] ?? [];
        $layout_map = [];
        foreach ($layout_defs as $layout) {
            $layout_map[$layout['name'] ?? ''] = $layout;
        }

        foreach ($layouts as $i => $layout) {
            if (!is_array($layout)) continue;
            $layout_name = $layout['acf_fc_layout'] ?? '';
            $layout_def = $layout_map[$layout_name] ?? null;
            $sub_fields = $layout_def['sub_fields'] ?? [];

            foreach ($sub_fields as $sub) {
                $sub_type = $sub['type'] ?? '';
                $sub_name = $sub['name'] ?? '';

                if (in_array($sub_type, $text_types, true)) {
                    $value = $layout[$sub_name] ?? '';
                    if (is_string($value) && $value !== '') {
                        $key = $prefix . '.' . $i . '.' . $sub_name;
                        $fields[$key] = [
                            'label' => ucfirst($layout_name) . ' [' . ($i + 1) . '] → ' . ($sub['label'] ?? $sub_name),
                            'type'  => in_array($sub_type, ['textarea', 'wysiwyg'], true) ? 'textarea' : 'text',
                            'value' => $value,
                        ];
                    }
                } elseif ($sub_type === 'repeater' && is_array($layout[$sub_name] ?? null)) {
                    // Nested repeater inside flex content
                    $nested_rows = $layout[$sub_name];
                    $nested_sub_fields = $sub['sub_fields'] ?? [];
                    foreach ($nested_rows as $j => $nested_row) {
                        if (!is_array($nested_row)) continue;
                        foreach ($nested_sub_fields as $nsub) {
                            if (!in_array($nsub['type'] ?? '', $text_types, true)) continue;
                            $nsub_name = $nsub['name'] ?? '';
                            $value = $nested_row[$nsub_name] ?? '';
                            if (is_string($value) && $value !== '') {
                                $key = $prefix . '.' . $i . '.' . $sub_name . '.' . $j . '.' . $nsub_name;
                                $fields[$key] = [
                                    'label' => ucfirst($layout_name) . ' [' . ($i + 1) . '] → ' . ($sub['label'] ?? $sub_name) . ' [' . ($j + 1) . '] → ' . ($nsub['label'] ?? $nsub_name),
                                    'type'  => in_array($nsub['type'], ['textarea', 'wysiwyg'], true) ? 'textarea' : 'text',
                                    'value' => $value,
                                ];
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Section: Nav menus.
     */
    private static function render_menus(string $lang): void {
        $locations = get_nav_menu_locations();
        ?>
        <div class="yvl-section" id="yvl-section-menus">
            <h2 class="yvl-section-title">Menus</h2>
            <?php foreach ($locations as $location => $menu_id):
                if (!$menu_id) continue;
                $menu = wp_get_nav_menu_object($menu_id);
                if (!$menu) continue;
                $items = wp_get_nav_menu_items($menu_id);
                if (!$items) continue;
            ?>
                <h3><?php echo esc_html($menu->name); ?> <small>(<?php echo esc_html($location); ?>)</small></h3>
                <table class="form-table yvl-table">
                    <?php foreach ($items as $item):
                        $translated = YVL_DB::get($lang, 'menu', $item->ID, 'title') ?? '';
                    ?>
                    <tr>
                        <th><?php echo esc_html($item->title); ?></th>
                        <td>
                            <div class="yvl-side-by-side">
                                <div class="yvl-original">
                                    <label>Original</label>
                                    <input type="text" value="<?php echo esc_attr($item->title); ?>" readonly disabled>
                                </div>
                                <div class="yvl-translated">
                                    <label>Traduction</label>
                                    <input type="text" name="yvl[menu][<?php echo $item->ID; ?>][title]" value="<?php echo esc_attr($translated); ?>" placeholder="<?php echo esc_attr($item->title); ?>">
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endforeach; ?>
            <?php if (empty($locations) || !array_filter($locations)): ?>
                <p class="description">Aucun menu assigné.</p>
            <?php endif; ?>
        </div>
        <?php
    }
}
