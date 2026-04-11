<?php
/**
 * Youvanna Post-Clone Setup Script
 *
 * Usage: wp eval-file wp-content/themes/youvanna-starter/post-clone-setup.php --allow-root
 *
 * This script configures EVERYTHING after cloning the template to a new domain:
 * 1. Youvanna Languages plugin — copy + activate
 * 2. Redis Object Cache — install + activate + enable
 * 3. WP Super Cache — install + activate + enable
 * 4. Wordfence — install + activate + WAF bootstrap + Plesk auto_prepend_file
 * 5. Wordfence security config — firewall, brute force, scanner, login, XMLRPC
 * 6. Wordfence Central disconnect (for cloned sites)
 *
 * Run this AFTER cloning the template to a new domain.
 * Must be run as root (for Plesk CLI access).
 */

// Safety: only run via WP-CLI
if (!defined('WP_CLI') || !WP_CLI) {
    echo "This script must be run via WP-CLI:\n";
    echo "wp eval-file wp-content/themes/youvanna-starter/post-clone-setup.php --allow-root\n";
    exit(1);
}

WP_CLI::log('');
WP_CLI::log('╔══════════════════════════════════════════════════╗');
WP_CLI::log('║  Youvanna Post-Clone Setup                      ║');
WP_CLI::log('╚══════════════════════════════════════════════════╝');
WP_CLI::log('');

// ============================================
// 1. Youvanna Languages plugin
// ============================================
WP_CLI::log('── 1. Youvanna Languages ──');

$yvl_source = get_stylesheet_directory() . '/plugins/youvanna-languages';
$yvl_dest = WP_PLUGIN_DIR . '/youvanna-languages';

if (is_dir($yvl_source)) {
    if (!is_dir($yvl_dest)) {
        // Copy plugin to wp-content/plugins
        $cmd = 'cp -r ' . escapeshellarg($yvl_source) . ' ' . escapeshellarg($yvl_dest);
        shell_exec($cmd);
        WP_CLI::log('Copied youvanna-languages to plugins/');
    }
    if (!is_plugin_active('youvanna-languages/youvanna-languages.php')) {
        WP_CLI::runcommand('plugin activate youvanna-languages', ['launch' => true]);
    }
    WP_CLI::log('Youvanna Languages: active.');
} else {
    WP_CLI::warning('youvanna-languages not found in theme. Skipping.');
}

// ============================================
// 1b. Youvanna Cookies plugin
// ============================================
WP_CLI::log('');
WP_CLI::log('── 1b. Youvanna Cookies ──');

$yvc_source = get_stylesheet_directory() . '/plugins/youvanna-cookies';
$yvc_dest = WP_PLUGIN_DIR . '/youvanna-cookies';

if (is_dir($yvc_source)) {
    if (!is_dir($yvc_dest)) {
        $cmd = 'cp -r ' . escapeshellarg($yvc_source) . ' ' . escapeshellarg($yvc_dest);
        shell_exec($cmd);
        WP_CLI::log('Copied youvanna-cookies to plugins/');
    }
    if (!is_plugin_active('youvanna-cookies/youvanna-cookies.php')) {
        WP_CLI::runcommand('plugin activate youvanna-cookies', ['launch' => true]);
    }
    WP_CLI::log('Youvanna Cookies: active.');
} else {
    WP_CLI::warning('youvanna-cookies not found in theme. Skipping.');
}

// ============================================
// 2. Redis Object Cache
// ============================================
WP_CLI::log('');
WP_CLI::log('── 2. Redis Object Cache ──');

// Check if Redis server is available
$redis_available = false;
if (class_exists('Redis')) {
    try {
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379, 2);
        $redis->ping();
        $redis_available = true;
        $redis->close();
    } catch (Exception $e) {
        // Redis not running
    }
}

if ($redis_available) {
    // Install redis-cache plugin if not present
    $plugins = get_plugins();
    $rc_installed = false;
    foreach ($plugins as $path => $info) {
        if (strpos($path, 'redis-cache') !== false) {
            $rc_installed = $path;
            break;
        }
    }

    if (!$rc_installed) {
        WP_CLI::runcommand('plugin install redis-cache --activate', ['launch' => true]);
        WP_CLI::log('Redis Object Cache installed and activated.');
    } elseif (!is_plugin_active($rc_installed)) {
        activate_plugin($rc_installed);
        WP_CLI::log('Redis Object Cache activated.');
    } else {
        WP_CLI::log('Redis Object Cache already active.');
    }

    // Enable the object cache drop-in
    WP_CLI::runcommand('redis enable', ['launch' => true, 'exit_error' => false]);
    WP_CLI::log('Redis Object Cache: enabled.');
} else {
    WP_CLI::warning('Redis server not available on this host. Skipping object cache.');
    WP_CLI::warning('Install Redis: dnf install -y redis && systemctl enable --now redis');
}

// ============================================
// 3. WP Super Cache (page cache)
// ============================================
WP_CLI::log('');
WP_CLI::log('── 3. WP Super Cache ──');

$plugins = get_plugins();
$wpsc_installed = false;
foreach ($plugins as $path => $info) {
    if (strpos($path, 'wp-super-cache') !== false) {
        $wpsc_installed = $path;
        break;
    }
}

if (!$wpsc_installed) {
    WP_CLI::runcommand('plugin install wp-super-cache --activate', ['launch' => true]);
    WP_CLI::log('WP Super Cache installed and activated.');
} elseif (!is_plugin_active($wpsc_installed)) {
    activate_plugin($wpsc_installed);
    WP_CLI::log('WP Super Cache activated.');
} else {
    WP_CLI::log('WP Super Cache already active.');
}

// Enable caching in wp-cache-config.php
$cache_config = WP_CONTENT_DIR . '/wp-cache-config.php';
if (file_exists($cache_config)) {
    $contents = file_get_contents($cache_config);
    if (strpos($contents, '$cache_enabled = false') !== false) {
        $contents = str_replace('$cache_enabled = false', '$cache_enabled = true', $contents);
        file_put_contents($cache_config, $contents);
        WP_CLI::log('WP Super Cache: enabled in config.');
    } else {
        WP_CLI::log('WP Super Cache: already enabled.');
    }
} else {
    WP_CLI::warning('wp-cache-config.php not found. Visit Settings → WP Super Cache to initialize.');
}

// Ensure cache directory exists with correct ownership
$cache_dir = WP_CONTENT_DIR . '/cache/supercache';
if (!is_dir($cache_dir)) {
    wp_mkdir_p($cache_dir);
    WP_CLI::log('Created cache directory.');
}

// Fix file ownership (Plesk uses per-domain system users)
$site_url = get_option('siteurl');
$domain = parse_url($site_url, PHP_URL_HOST);
if ($domain) {
    // Plesk system user format: domain_randomstring
    $stat = stat(ABSPATH . 'wp-config.php');
    if ($stat) {
        $uid = $stat['uid'];
        $gid = $stat['gid'];
        $files_to_fix = [
            WP_CONTENT_DIR . '/advanced-cache.php',
            $cache_config,
        ];
        foreach ($files_to_fix as $file) {
            if (file_exists($file)) {
                chown($file, $uid);
                chgrp($file, $gid);
            }
        }
        // Recursively fix cache dir
        shell_exec('chown -R ' . $uid . ':' . $gid . ' ' . escapeshellarg(WP_CONTENT_DIR . '/cache/'));
    }
}
WP_CLI::log('WP Super Cache: ready.');

// ============================================
// 4. Wordfence — Install + WAF Bootstrap
// ============================================
WP_CLI::log('');
WP_CLI::log('── 4. Wordfence Security ──');

$plugins = get_plugins();
$wf_installed = false;
foreach ($plugins as $path => $info) {
    if (strpos($path, 'wordfence') !== false) {
        $wf_installed = $path;
        break;
    }
}

if (!$wf_installed) {
    WP_CLI::log('Installing Wordfence...');
    WP_CLI::runcommand('plugin install wordfence --activate', ['launch' => true]);
    WP_CLI::log('Wordfence installed and activated.');
} else {
    if (!is_plugin_active($wf_installed)) {
        activate_plugin($wf_installed);
        WP_CLI::log('Wordfence activated.');
    } else {
        WP_CLI::log('Wordfence already active.');
    }
}

// Reload Wordfence classes
if (!class_exists('wfConfig')) {
    $wf_main = WP_PLUGIN_DIR . '/wordfence/wordfence.php';
    if (file_exists($wf_main)) {
        include_once $wf_main;
    }
}

if (!class_exists('wfConfig')) {
    WP_CLI::error('Wordfence classes not available. Activate Wordfence manually, then re-run.');
    exit(1);
}

// Create WAF bootstrap
$waf_bootstrap = ABSPATH . 'wordfence-waf.php';
if (!file_exists($waf_bootstrap)) {
    $waf_content = <<<'PHP'
<?php
/**
 * Wordfence WAF bootstrap — loaded via auto_prepend_file before WordPress.
 */
if (file_exists(__DIR__ . '/wp-content/plugins/wordfence/waf/bootstrap.php')) {
    define('WFWAF_LOG_PATH', __DIR__ . '/wp-content/wflogs/');
    include_once __DIR__ . '/wp-content/plugins/wordfence/waf/bootstrap.php';
}
PHP;
    file_put_contents($waf_bootstrap, $waf_content);
    WP_CLI::log('Created wordfence-waf.php at web root.');
} else {
    WP_CLI::log('wordfence-waf.php already exists.');
}

// Fix ownership
if (isset($uid, $gid)) {
    chown($waf_bootstrap, $uid);
    chgrp($waf_bootstrap, $gid);
}

// Configure Plesk auto_prepend_file
$waf_path = realpath($waf_bootstrap);
if ($domain && $waf_path) {
    $tmp = tempnam('/tmp', 'waf_');
    file_put_contents($tmp, 'auto_prepend_file = ' . $waf_path);

    $cmd = 'plesk bin site --update-php-settings ' . escapeshellarg($domain) . ' -additional-settings ' . escapeshellarg($tmp) . ' 2>&1';
    $output = shell_exec($cmd);
    unlink($tmp);

    if ($output && (strpos($output, 'Error') !== false || strpos($output, 'error') !== false)) {
        WP_CLI::warning('Plesk auto_prepend_file failed: ' . trim($output));
    } else {
        WP_CLI::log('Plesk auto_prepend_file configured for ' . $domain);
    }
} else {
    WP_CLI::warning('Could not detect domain. Set auto_prepend_file manually.');
}

// ============================================
// 5. Wordfence Central disconnect (cloned sites)
// ============================================
WP_CLI::log('Disconnecting from previous Wordfence Central...');
wfConfig::set('wordfenceCentralConnected', 0);
wfConfig::set('wordfenceCentralSiteID', '');
wfConfig::set('wordfenceCentralPK', '');
wfConfig::set('wordfenceCentralSecretKey', '');
wfConfig::set('wordfenceCentralCurrentSiteUrl', '');

// ============================================
// 6. Wordfence — Firewall
// ============================================
WP_CLI::log('Configuring firewall...');
wfConfig::set('firewallEnabled', 1);
wfConfig::set('wafStatus', 'learning-mode');
wfConfig::set('learningModeGracePeriodEnabled', 1);
wfConfig::set('learningModeGracePeriod', time() + (7 * 24 * 3600));
wfConfig::set('disableWAFBlacklistBlocking', 0);
wfConfig::set('wafAlertOnAttacks', 1);
wfConfig::set('maxRequestsCrawlers', 'DISABLED');
wfConfig::set('maxRequestsHumans', 'DISABLED');
wfConfig::set('max404Crawlers', 'DISABLED');
wfConfig::set('max404Humans', 'DISABLED');
wfConfig::set('maxScanHits', 'DISABLED');

// ============================================
// 7. Wordfence — Brute Force
// ============================================
WP_CLI::log('Configuring brute force protection...');
wfConfig::set('loginSecurityEnabled', 1);
wfConfig::set('loginSec_lockInvalidUsers', 1);
wfConfig::set('loginSec_maxFailures', 5);
wfConfig::set('loginSec_maxForgotPasswd', 3);
wfConfig::set('loginSec_countFailMins', 5);
wfConfig::set('loginSec_lockoutMins', 30);
wfConfig::set('loginSec_strongPasswds_enabled', 1);
wfConfig::set('loginSec_breachPasswds_enabled', 1);
wfConfig::set('loginSec_maskLoginErrors', 1);
wfConfig::set('loginSec_blockAdminReg', 1);
wfConfig::set('loginSec_disableAuthorScan', 1);

// ============================================
// 8. Wordfence — Scanner
// ============================================
WP_CLI::log('Configuring scanner...');
wfConfig::set('scansEnabled_core', 1);
wfConfig::set('scansEnabled_themes', 1);
wfConfig::set('scansEnabled_plugins', 1);
wfConfig::set('scansEnabled_malware', 1);
wfConfig::set('scansEnabled_fileContents', 1);
wfConfig::set('scansEnabled_fileContentsGSB', 1);
wfConfig::set('scansEnabled_checkReadableConfig', 1);
wfConfig::set('scansEnabled_suspectedFiles', 1);
wfConfig::set('scansEnabled_passwds', 1);
wfConfig::set('scansEnabled_diskSpace', 1);
wfConfig::set('scheduledScansEnabled', 1);
wfConfig::set('lowResourceScansEnabled', 0);

// ============================================
// 9. Wordfence — General
// ============================================
WP_CLI::log('Configuring general settings...');
wfConfig::set('hideWPVersion', 1);
$admin_email = get_option('admin_email');
wfConfig::set('alertEmails', $admin_email);
wfConfig::set('alertOn_critical', 1);
wfConfig::set('alertOn_update', 0);
wfConfig::set('alertOn_wafDeactivated', 1);
wfConfig::set('alertOn_block', 0);
wfConfig::set('alertOn_loginLockout', 1);
wfConfig::set('alertOn_lostPasswdForm', 0);
wfConfig::set('alertOn_adminLogin', 0);
wfConfig::set('alertOn_nonAdminLogin', 0);
wfConfig::set('blockFakeBots', 1);
wfConfig::set('liveTrafficEnabled', 0);
wfConfig::set('autoUpdate', 1);
wfConfig::set('disableXMLRPC', 'loginOnly');

// ============================================
// 10. Flush rewrite rules
// ============================================
WP_CLI::log('');
WP_CLI::log('Flushing rewrite rules...');
flush_rewrite_rules();
delete_transient('wordfence_dashboard_activity');

// ============================================
// DONE
// ============================================
WP_CLI::log('');
WP_CLI::success('Post-clone setup complete!');
WP_CLI::log('');
WP_CLI::log('  ✓ Youvanna Languages    — active');
WP_CLI::log('  ✓ Youvanna Cookies      — active');
WP_CLI::log('  ' . ($redis_available ? '✓' : '✗') . ' Redis Object Cache     — ' . ($redis_available ? 'active' : 'skipped (install Redis first)'));
WP_CLI::log('  ✓ WP Super Cache        — active');
WP_CLI::log('  ✓ Wordfence             — active + WAF optimized');
WP_CLI::log('  ✓ Wordfence Central     — disconnected (ready for fresh link)');
WP_CLI::log('  ✓ Alerts                — ' . $admin_email);
WP_CLI::log('');
WP_CLI::log('Firewall is in learning mode for 7 days, then auto-enables.');
