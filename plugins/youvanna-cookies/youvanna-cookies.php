<?php
/**
 * Plugin Name: Youvanna Cookies
 * Description: Bandeau de consentement RGPD — simple, français, intégré au thème Youvanna.
 * Version: 1.1.0
 * Author: Agence Youvanna
 * Author URI: https://youvanna.com
 * Text Domain: youvanna-cookies
 */
if (!defined('ABSPATH')) exit;

// ============================================
// 1. BANDEAU HTML
// ============================================
add_action('wp_footer', function() {
    $privacy_url = get_option('yv_privacy_url', '');
    if (!$privacy_url) {
        // Fallback 1 : WP privacy policy page (si la page existe et est publiée)
        $privacy_id = (int) get_option('wp_page_for_privacy_policy');
        if ($privacy_id && get_post_status($privacy_id) === 'publish') {
            $privacy_url = get_permalink($privacy_id);
        }
    }
    if (!$privacy_url) {
        // Fallback 2 : page mentions-legales
        $mentions = get_page_by_path('mentions-legales');
        if ($mentions && $mentions->post_status === 'publish') {
            $privacy_url = get_permalink($mentions);
        }
    }
    if (!$privacy_url) {
        // Fallback 3 : URL brute (toujours résolvable si rewrite OK)
        $privacy_url = home_url('/mentions-legales/');
    }
    ?>
    <div id="yv-cb" class="yv-cb" role="dialog" aria-label="Cookies" style="display:none;">
        <div class="yv-cb-inner">
            <div class="yv-cb-text">
                <p class="yv-cb-title">Ce site utilise des cookies</p>
                <p class="yv-cb-desc">Nous utilisons des cookies pour mesurer l'audience et améliorer votre expérience. <a href="<?php echo esc_url($privacy_url); ?>" class="yv-cb-link">En savoir plus</a></p>
            </div>
            <div class="yv-cb-actions">
                <button class="yv-cb-btn yv-cb-btn--reject" id="yv-cb-reject" type="button">Refuser</button>
                <button class="yv-cb-btn yv-cb-btn--accept" id="yv-cb-accept" type="button">Accepter</button>
            </div>
        </div>
    </div>
    <button id="yv-cb-reopen" class="yv-cb-reopen" type="button" aria-label="Gérer les cookies" style="display:none;" title="Gérer les cookies">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="8" cy="10" r="1.5" fill="currentColor"/><circle cx="15" cy="8" r="1" fill="currentColor"/><circle cx="14" cy="14" r="1.5" fill="currentColor"/><circle cx="9" cy="15" r="1" fill="currentColor"/></svg>
    </button>
    <?php
});

// ============================================
// 2. CSS
// ============================================
add_action('wp_head', function() {
    ?>
    <style id="yv-cookies-css">
    .yv-cb{position:fixed;bottom:0;left:0;right:0;z-index:99999;padding:16px;pointer-events:none;font-family:var(--font-body,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif)}
    .yv-cb-inner{max-width:680px;margin:0 auto;background:#fff;border-radius:12px;box-shadow:0 -4px 24px rgba(0,0,0,.12);padding:20px 24px;pointer-events:all;display:flex;align-items:center;gap:20px;animation:yvCbSlide .3s ease}
    @keyframes yvCbSlide{from{transform:translateY(100%);opacity:0}to{transform:translateY(0);opacity:1}}
    .yv-cb-text{flex:1}
    .yv-cb-title{font-size:15px;font-weight:700;color:#0f172a;margin:0 0 4px}
    .yv-cb-desc{font-size:13px;color:#64748b;line-height:1.5;margin:0}
    .yv-cb-link{color:var(--color-primary-dark,#7A2210);text-decoration:underline;font-weight:600}
    .yv-cb-actions{display:flex;gap:8px;flex-shrink:0}
    .yv-cb-btn{padding:10px 20px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;border:2px solid transparent;transition:background-color .15s ease,color .15s ease,border-color .15s ease,opacity .15s ease;white-space:nowrap}
    .yv-cb-btn--accept{background:var(--color-primary,#2563eb);color:#fff;border-color:var(--color-primary,#2563eb)}
    .yv-cb-btn--accept:hover{opacity:.9}
    .yv-cb-btn--reject{background:transparent;color:#475569;border-color:#cbd5e1}
    .yv-cb-btn--reject:hover{background:#f8fafc;border-color:#94a3b8;color:#334155}
    .yv-cb-reopen{position:fixed;bottom:16px;left:16px;z-index:99998;width:40px;height:40px;border-radius:50%;background:#fff;border:1px solid #e2e8f0;box-shadow:0 2px 8px rgba(0,0,0,.08);cursor:pointer;display:flex;align-items:center;justify-content:center;color:#94a3b8;transition:all .2s}
    .yv-cb-reopen:hover{border-color:var(--color-primary,#2563eb);color:var(--color-primary,#2563eb)}
    @media(max-width:640px){
        .yv-cb{padding:10px}
        .yv-cb-inner{flex-direction:column;padding:18px 16px;gap:14px}
        .yv-cb-actions{width:100%;display:grid;grid-template-columns:1fr 1fr;gap:8px}
        .yv-cb-btn{text-align:center}
    }
    </style>
    <?php
}, 99);

// ============================================
// 3. JS — Accepter / Refuser, c'est tout
// ============================================
add_action('wp_footer', function() {
    ?>
    <script id="yv-cookies-js">
    (function(){
        var CK='yv_consent',banner=document.getElementById('yv-cb'),reopen=document.getElementById('yv-cb-reopen');
        function get(){var m=document.cookie.match('(?:^|; )'+CK+'=([^;]*)');return m?m[1]:null;}
        function set(v){var d=new Date();d.setTime(d.getTime()+365*864e5);document.cookie=CK+'='+v+';expires='+d.toUTCString()+';path=/;SameSite=Lax;Secure';}
        function fire(ok){
            document.dispatchEvent(new CustomEvent('yv_consent_update',{detail:{accepted:ok?['analytics']:[]}}));
        }
        function show(){banner.style.display='';reopen.style.display='none';}
        function hide(){banner.style.display='none';reopen.style.display='';}
        var v=get();
        if(v){hide();if(v==='yes')fire(true);}else{show();}
        document.getElementById('yv-cb-accept').onclick=function(){set('yes');hide();fire(true);};
        document.getElementById('yv-cb-reject').onclick=function(){set('no');hide();fire(false);};
        reopen.onclick=function(){show();};
    })();
    </script>
    <?php
}, 99);
