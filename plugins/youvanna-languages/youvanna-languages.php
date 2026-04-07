<?php
/**
 * Plugin Name: Youvanna Languages
 * Plugin URI:  https://youvanna.com
 * Description: Multilingue enterprise-grade pour les sites Youvanna. Traduisez 100% du contenu : pages, SCF, metas, menus, options. URLs /fr/ /en/ /de/ + hreflang SEO.
 * Version:     1.0.0
 * Author:      Agence Youvanna
 * Author URI:  https://youvanna.com
 * License:     GPL-2.0-or-later
 * Text Domain: youvanna-languages
 * Requires PHP: 7.4
 */

defined('ABSPATH') || exit;

define('YVL_VERSION', '1.0.0');
define('YVL_FILE', __FILE__);
define('YVL_DIR', plugin_dir_path(__FILE__));
define('YVL_URL', plugin_dir_url(__FILE__));

// ============================================
// AUTOLOAD
// ============================================
require_once YVL_DIR . 'includes/class-yvl-db.php';
require_once YVL_DIR . 'includes/class-yvl-languages.php';
require_once YVL_DIR . 'includes/class-yvl-translator.php';
require_once YVL_DIR . 'includes/class-yvl-router.php';
require_once YVL_DIR . 'includes/class-yvl-seo.php';
require_once YVL_DIR . 'includes/class-yvl-switcher.php';

if (is_admin()) {
    require_once YVL_DIR . 'admin/class-yvl-admin.php';
    require_once YVL_DIR . 'admin/class-yvl-admin-translate.php';
}

// ============================================
// ACTIVATION / DEACTIVATION
// ============================================
register_activation_hook(YVL_FILE, ['YVL_DB', 'activate']);
register_deactivation_hook(YVL_FILE, ['YVL_DB', 'deactivate']);

// ============================================
// INIT
// ============================================
add_action('plugins_loaded', function () {
    YVL_Languages::init();
    YVL_Translator::init();
    YVL_Router::init();
    YVL_SEO::init();
    YVL_Switcher::init();

    if (is_admin()) {
        YVL_Admin::init();
        YVL_Admin_Translate::init();
    }
});
