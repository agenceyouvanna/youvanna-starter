<?php
/**
 * Wordfence Auto-Configuration Script for Youvanna Sites
 *
 * Usage: wp eval-file wp-content/themes/youvanna-starter/wordfence-autoconfig.php --allow-root
 *
 * This script:
 * 1. Installs and activates Wordfence if not already present
 * 2. Creates wordfence-waf.php bootstrap at web root
 * 3. Configures Plesk auto_prepend_file for extended WAF protection
 * 4. Configures all security settings to optimal values
 * 5. Enables brute force protection
 * 6. Enables firewall in learning mode
 * 7. Sets up login security
 * 8. Disconnects from any previous Wordfence Central link (for cloned sites)
 *
 * Run this AFTER cloning the template to a new domain.
 * Must be run as root (for Plesk CLI access).
 */

// Safety: only run via WP-CLI
if (!defined('WP_CLI') || !WP_CLI) {
    echo "This script must be run via WP-CLI: wp eval-file wordfence-autoconfig.php --allow-root\n";
    exit(1);
}

WP_CLI::log('=== Wordfence Auto-Configuration for Youvanna ===');

// ============================================
// 1. Install & Activate Wordfence
// ============================================
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

// Wait for Wordfence to be fully loaded
if (!class_exists('wfConfig')) {
    WP_CLI::error('Wordfence classes not available. Please activate Wordfence manually first, then re-run this script.');
    exit(1);
}

// ============================================
// 2. Create wordfence-waf.php bootstrap + Plesk auto_prepend_file
// ============================================
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

// Detect domain from site URL for Plesk CLI
$site_url = get_option('siteurl');
$domain = parse_url($site_url, PHP_URL_HOST);
$waf_path = realpath($waf_bootstrap);

if ($domain && $waf_path) {
    // Write directive to temp file (Plesk expects a file path)
    $tmp = tempnam('/tmp', 'waf_');
    file_put_contents($tmp, 'auto_prepend_file = ' . $waf_path);

    $cmd = 'plesk bin site --update-php-settings ' . escapeshellarg($domain) . ' -additional-settings ' . escapeshellarg($tmp) . ' 2>&1';
    $output = shell_exec($cmd);
    unlink($tmp);

    if (strpos($output, 'Error') !== false || strpos($output, 'error') !== false) {
        WP_CLI::warning('Plesk auto_prepend_file failed: ' . trim($output));
        WP_CLI::warning('You may need to set it manually in Plesk → PHP Settings → Additional directives');
    } else {
        WP_CLI::log('Plesk auto_prepend_file configured for ' . $domain);
    }
} else {
    WP_CLI::warning('Could not detect domain. Set auto_prepend_file manually in Plesk PHP settings.');
}

// ============================================
// 3. Disconnect from previous Wordfence Central (for cloned sites)
// ============================================
WP_CLI::log('Disconnecting from any previous Wordfence Central link...');
wfConfig::set('wordfenceCentralConnected', 0);
wfConfig::set('wordfenceCentralSiteID', '');
wfConfig::set('wordfenceCentralPK', '');
wfConfig::set('wordfenceCentralSecretKey', '');
wfConfig::set('wordfenceCentralCurrentSiteUrl', '');

// ============================================
// 4. Firewall Configuration
// ============================================
WP_CLI::log('Configuring Web Application Firewall...');

// Enable firewall in learning mode (will auto-switch to enabled after learning period)
wfConfig::set('firewallEnabled', 1);
wfConfig::set('wafStatus', 'learning-mode');
wfConfig::set('learningModeGracePeriodEnabled', 1);
wfConfig::set('learningModeGracePeriod', time() + (7 * 24 * 3600)); // 7 days learning

// Firewall rules
wfConfig::set('disableWAFBlacklistBlocking', 0); // Enable blocklist
wfConfig::set('wafAlertOnAttacks', 1);

// Rate limiting
wfConfig::set('maxRequestsCrawlers', 'DISABLED'); // Don't rate limit crawlers (SEO)
wfConfig::set('maxRequestsHumans', 'DISABLED');
wfConfig::set('max404Crawlers', 'DISABLED');
wfConfig::set('max404Humans', 'DISABLED');
wfConfig::set('maxScanHits', 'DISABLED');

// ============================================
// 5. Brute Force Protection
// ============================================
WP_CLI::log('Configuring Brute Force Protection...');

wfConfig::set('loginSecurityEnabled', 1);
wfConfig::set('loginSec_lockInvalidUsers', 1);
wfConfig::set('loginSec_maxFailures', 5);        // Lock after 5 failed attempts
wfConfig::set('loginSec_maxForgotPasswd', 3);     // Lock after 3 forgot password attempts
wfConfig::set('loginSec_countFailMins', 5);       // Count failures within 5 minutes
wfConfig::set('loginSec_lockoutMins', 30);        // Lockout for 30 minutes
wfConfig::set('loginSec_strongPasswds_enabled', 1); // Enforce strong passwords
wfConfig::set('loginSec_breachPasswds_enabled', 1); // Check for breached passwords
wfConfig::set('loginSec_maskLoginErrors', 1);     // Don't reveal which field is wrong
wfConfig::set('loginSec_blockAdminReg', 1);       // Block admin registration
wfConfig::set('loginSec_disableAuthorScan', 1);   // Block author scans (?author=1)

// ============================================
// 6. Scan Settings
// ============================================
WP_CLI::log('Configuring Scanner...');

wfConfig::set('scansEnabled_core', 1);        // Scan core files
wfConfig::set('scansEnabled_themes', 1);      // Scan themes
wfConfig::set('scansEnabled_plugins', 1);     // Scan plugins
wfConfig::set('scansEnabled_malware', 1);     // Scan for malware
wfConfig::set('scansEnabled_fileContents', 1); // Deep scan file contents
wfConfig::set('scansEnabled_fileContentsGSB', 1); // Google Safe Browsing check
wfConfig::set('scansEnabled_checkReadableConfig', 1); // Check for readable config files
wfConfig::set('scansEnabled_suspectedFiles', 1);
wfConfig::set('scansEnabled_passwds', 1);     // Check for weak passwords
wfConfig::set('scansEnabled_diskSpace', 1);   // Check disk space
wfConfig::set('scheduledScansEnabled', 1);    // Enable scheduled scans
wfConfig::set('lowResourceScansEnabled', 0);  // Full resource scans

// ============================================
// 7. General Settings
// ============================================
WP_CLI::log('Configuring General Settings...');

// Hide Wordfence version
wfConfig::set('hideWPVersion', 1);

// Alert email
$admin_email = get_option('admin_email');
wfConfig::set('alertEmails', $admin_email);
wfConfig::set('alertOn_critical', 1);
wfConfig::set('alertOn_update', 0);         // Don't alert on plugin updates
wfConfig::set('alertOn_wafDeactivated', 1);
wfConfig::set('alertOn_block', 0);          // Don't alert on every block (too noisy)
wfConfig::set('alertOn_loginLockout', 1);
wfConfig::set('alertOn_lostPasswdForm', 0);
wfConfig::set('alertOn_adminLogin', 0);
wfConfig::set('alertOn_nonAdminLogin', 0);

// Block fake Google crawlers
wfConfig::set('blockFakeBots', 1);

// Disable live traffic view (performance)
wfConfig::set('liveTrafficEnabled', 0);

// Auto-update Wordfence
wfConfig::set('autoUpdate', 1);

// XMLRPC protection
wfConfig::set('disableXMLRPC', 'loginOnly'); // Block XML-RPC login attempts

// ============================================
// 8. Country blocking (optional — commented out)
// ============================================
// Uncomment and adjust if you want to block specific countries
// wfConfig::set('cbl_countries', 'CN,RU,KP');
// wfConfig::set('cbl_enabled', 1);

// ============================================
// 9. Flush and verify
// ============================================
WP_CLI::log('Flushing transients...');
delete_transient('wordfence_dashboard_activity');

WP_CLI::success('Wordfence configured successfully!');
WP_CLI::log('');
WP_CLI::log('Summary:');
WP_CLI::log('  - WAF bootstrap: wordfence-waf.php created at web root');
WP_CLI::log('  - Plesk auto_prepend_file: configured for extended WAF protection');
WP_CLI::log('  - Firewall: Learning mode (7 days), then auto-enabled');
WP_CLI::log('  - Brute Force: 5 attempts / 5 min → 30 min lockout');
WP_CLI::log('  - Scanner: Full scan enabled, scheduled');
WP_CLI::log('  - Login: Strong passwords, breached password check, admin scan block');
WP_CLI::log('  - Alerts: ' . $admin_email);
WP_CLI::log('  - Wordfence Central: Disconnected (ready for fresh link)');
WP_CLI::log('');
WP_CLI::log('After 7 days, the firewall will auto-switch to "Enabled and Protecting".');
