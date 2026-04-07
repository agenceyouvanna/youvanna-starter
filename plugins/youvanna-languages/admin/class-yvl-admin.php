<?php
defined('ABSPATH') || exit;

/**
 * Admin — Language management page.
 *
 * Menu: Langues → Langues (manage enabled languages)
 *       Langues → Traduire (translate content per language)
 *       Langues → Exporter/Importer
 */
class YVL_Admin {

    public static function init(): void {
        add_action('admin_menu', [__CLASS__, 'register_menus']);
        add_action('admin_init', [__CLASS__, 'handle_actions']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
    }

    /**
     * Register admin menu pages.
     */
    public static function register_menus(): void {
        // Main menu
        add_menu_page(
            'Langues',
            'Langues',
            'manage_options',
            'yvl-languages',
            [__CLASS__, 'page_languages'],
            'dashicons-translation',
            30
        );

        // Submenu: Manage languages
        add_submenu_page(
            'yvl-languages',
            'Gérer les langues',
            'Gérer les langues',
            'manage_options',
            'yvl-languages',
            [__CLASS__, 'page_languages']
        );

        // Submenu: Translate
        add_submenu_page(
            'yvl-languages',
            'Traduire',
            'Traduire',
            'manage_options',
            'yvl-translate',
            ['YVL_Admin_Translate', 'page_translate']
        );

        // Submenu: Export/Import
        add_submenu_page(
            'yvl-languages',
            'Exporter / Importer',
            'Exporter / Importer',
            'manage_options',
            'yvl-export',
            [__CLASS__, 'page_export']
        );
    }

    /**
     * Enqueue admin CSS/JS.
     */
    public static function enqueue_assets(string $hook): void {
        if (strpos($hook, 'yvl-') === false && strpos($hook, 'yvl_') === false) return;

        wp_enqueue_style('yvl-admin', YVL_URL . 'assets/css/admin.css', [], YVL_VERSION);
        wp_enqueue_script('yvl-admin', YVL_URL . 'assets/js/admin.js', [], YVL_VERSION, true);
    }

    /**
     * Handle add/remove/default language actions.
     */
    public static function handle_actions(): void {
        if (!current_user_can('manage_options')) return;

        // Add language
        if (isset($_POST['yvl_add_lang']) && wp_verify_nonce($_POST['_wpnonce'] ?? '', 'yvl_manage_langs')) {
            $code = sanitize_text_field($_POST['yvl_add_lang']);
            if (YVL_Languages::add($code)) {
                add_settings_error('yvl', 'added', 'Langue ajoutée : ' . strtoupper($code), 'success');
            }
        }

        // Remove language
        if (isset($_GET['yvl_remove_lang']) && wp_verify_nonce($_GET['_wpnonce'] ?? '', 'yvl_remove_lang')) {
            $code = sanitize_text_field($_GET['yvl_remove_lang']);
            if (YVL_Languages::remove($code)) {
                add_settings_error('yvl', 'removed', 'Langue supprimée : ' . strtoupper($code), 'success');
            }
        }

        // Set default
        if (isset($_POST['yvl_set_default']) && wp_verify_nonce($_POST['_wpnonce'] ?? '', 'yvl_manage_langs')) {
            $code = sanitize_text_field($_POST['yvl_set_default']);
            if (YVL_Languages::set_default($code)) {
                add_settings_error('yvl', 'default', 'Langue par défaut : ' . strtoupper($code), 'success');
            }
        }

        // Switcher display option
        if (isset($_POST['yvl_switcher_display']) && wp_verify_nonce($_POST['_wpnonce'] ?? '', 'yvl_manage_langs')) {
            update_option('yvl_show_switcher', sanitize_text_field($_POST['yvl_switcher_display']));
            add_settings_error('yvl', 'switcher', 'Affichage du sélecteur mis à jour.', 'success');
        }

        // Import
        if (isset($_FILES['yvl_import_file']) && wp_verify_nonce($_POST['_wpnonce'] ?? '', 'yvl_import')) {
            self::handle_import();
        }
    }

    /**
     * Page: Manage languages.
     */
    public static function page_languages(): void {
        $enabled = YVL_Languages::enabled();
        $default = YVL_Languages::default_lang();
        $catalog = YVL_Languages::catalog();
        $available = array_diff_key($catalog, array_flip($enabled));
        $switcher = get_option('yvl_show_switcher', 'floating');
        ?>
        <div class="wrap yvl-wrap">
            <h1>Langues</h1>
            <?php settings_errors('yvl'); ?>

            <div class="yvl-grid">
                <!-- Enabled languages -->
                <div class="yvl-card">
                    <h2>Langues actives</h2>
                    <table class="wp-list-table widefat striped">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Langue</th>
                                <th>Traductions</th>
                                <th>Défaut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($enabled as $code):
                            $info = $catalog[$code] ?? null;
                            if (!$info) continue;
                            $count = YVL_DB::count_lang($code);
                            $is_default = ($code === $default);
                        ?>
                            <tr>
                                <td><strong><?php echo esc_html(strtoupper($code)); ?></strong></td>
                                <td><?php echo esc_html($info['native']); ?> (<?php echo esc_html($info['name']); ?>)</td>
                                <td><?php echo (int) $count; ?> champs</td>
                                <td>
                                    <?php if ($is_default): ?>
                                        <span class="yvl-badge yvl-badge--primary">Par défaut</span>
                                    <?php else: ?>
                                        <form method="post" style="display:inline">
                                            <?php wp_nonce_field('yvl_manage_langs'); ?>
                                            <input type="hidden" name="yvl_set_default" value="<?php echo esc_attr($code); ?>">
                                            <button type="submit" class="button button-small">Définir par défaut</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!$is_default): ?>
                                        <a href="<?php echo esc_url(admin_url('admin.php?page=yvl-translate&lang=' . $code)); ?>" class="button button-small">Traduire</a>
                                        <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=yvl-languages&yvl_remove_lang=' . $code), 'yvl_remove_lang')); ?>"
                                           class="button button-small button-link-delete"
                                           onclick="return confirm('Supprimer <?php echo esc_js(strtoupper($code)); ?> et toutes ses traductions ?')">
                                            Supprimer
                                        </a>
                                    <?php else: ?>
                                        <span class="description">Langue source</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Add language -->
                <div class="yvl-card">
                    <h2>Ajouter une langue</h2>
                    <form method="post">
                        <?php wp_nonce_field('yvl_manage_langs'); ?>
                        <select name="yvl_add_lang" class="yvl-select">
                            <option value="">— Choisir une langue —</option>
                            <?php foreach ($available as $code => $info): ?>
                                <option value="<?php echo esc_attr($code); ?>">
                                    <?php echo esc_html($info['native'] . ' (' . strtoupper($code) . ') — ' . $info['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="button button-primary">Ajouter</button>
                    </form>

                    <h3 style="margin-top:24px">Affichage du sélecteur de langue</h3>
                    <form method="post">
                        <?php wp_nonce_field('yvl_manage_langs'); ?>
                        <label><input type="radio" name="yvl_switcher_display" value="floating" <?php checked($switcher, 'floating'); ?>> Bouton flottant (bas gauche)</label><br>
                        <label><input type="radio" name="yvl_switcher_display" value="shortcode" <?php checked($switcher, 'shortcode'); ?>> Shortcode uniquement <code>[yvl_switcher]</code></label><br>
                        <label><input type="radio" name="yvl_switcher_display" value="none" <?php checked($switcher, 'none'); ?>> Désactivé</label><br>
                        <button type="submit" class="button" style="margin-top:8px">Enregistrer</button>
                    </form>

                    <h3 style="margin-top:24px">URLs</h3>
                    <p class="description">
                        La langue par défaut (<?php echo esc_html(strtoupper($default)); ?>) n'a pas de préfixe URL.<br>
                        Les autres langues utilisent <code>/<?php echo esc_html(YVL_Languages::secondary()[0] ?? 'en'); ?>/page</code>.
                    </p>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Page: Export/Import.
     */
    public static function page_export(): void {
        $enabled = YVL_Languages::enabled();
        $default = YVL_Languages::default_lang();
        $secondary = YVL_Languages::secondary();
        ?>
        <div class="wrap yvl-wrap">
            <h1>Exporter / Importer les traductions</h1>
            <?php settings_errors('yvl'); ?>

            <div class="yvl-grid">
                <div class="yvl-card">
                    <h2>Exporter (JSON)</h2>
                    <p>Téléchargez toutes les traductions d'une langue au format JSON.</p>
                    <?php foreach ($secondary as $lang): ?>
                        <a href="<?php echo esc_url(admin_url('admin-ajax.php?action=yvl_export&lang=' . $lang . '&_wpnonce=' . wp_create_nonce('yvl_export'))); ?>"
                           class="button" download>
                            Exporter <?php echo esc_html(strtoupper($lang)); ?>
                        </a>
                    <?php endforeach; ?>
                    <?php if (empty($secondary)): ?>
                        <p class="description">Ajoutez d'abord une langue secondaire.</p>
                    <?php endif; ?>
                </div>

                <div class="yvl-card">
                    <h2>Importer (JSON)</h2>
                    <form method="post" enctype="multipart/form-data">
                        <?php wp_nonce_field('yvl_import'); ?>
                        <p>
                            <label>Langue cible :
                                <select name="yvl_import_lang">
                                    <?php foreach ($secondary as $lang): ?>
                                        <option value="<?php echo esc_attr($lang); ?>"><?php echo esc_html(strtoupper($lang)); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                        </p>
                        <p><input type="file" name="yvl_import_file" accept=".json"></p>
                        <button type="submit" class="button button-primary">Importer</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Handle JSON import.
     */
    private static function handle_import(): void {
        if (empty($_FILES['yvl_import_file']['tmp_name'])) return;

        $lang = sanitize_text_field($_POST['yvl_import_lang'] ?? '');
        if (!$lang || !YVL_Languages::is_enabled($lang)) {
            add_settings_error('yvl', 'import_err', 'Langue invalide.', 'error');
            return;
        }

        $json = file_get_contents($_FILES['yvl_import_file']['tmp_name']);
        $data = json_decode($json, true);

        if (!is_array($data)) {
            add_settings_error('yvl', 'import_err', 'Fichier JSON invalide.', 'error');
            return;
        }

        $count = YVL_DB::import_lang($lang, $data);
        add_settings_error('yvl', 'imported', $count . ' traductions importées pour ' . strtoupper($lang) . '.', 'success');
    }
}

// AJAX export handler
add_action('wp_ajax_yvl_export', function () {
    if (!current_user_can('manage_options') || !wp_verify_nonce($_GET['_wpnonce'] ?? '', 'yvl_export')) {
        wp_die('Unauthorized');
    }

    $lang = sanitize_text_field($_GET['lang'] ?? '');
    if (!$lang || !YVL_Languages::is_enabled($lang)) {
        wp_die('Invalid language');
    }

    $data = YVL_DB::export_lang($lang);

    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="translations-' . $lang . '.json"');
    echo wp_json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
});
