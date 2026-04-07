<?php
/**
 * Youvanna Starter — functions.php v2.0
 * Thème Youvanna avec ACF pour sites vitrines
 * Architecture propre, 0 duplication, full automatisable
 */
if (!defined('ABSPATH')) exit;

// ============================================
// 1. ASSETS — CSS & JS
// ============================================
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', [], '6.5.1');
    // Make FA non-render-blocking
    add_filter('style_loader_tag', function($html, $handle) {
        if ($handle === 'font-awesome') {
            return str_replace("rel='stylesheet'", "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", $html) . '<noscript>' . $html . '</noscript>';
        }
        return $html;
    }, 10, 2);
    wp_enqueue_style('youvanna-main', get_stylesheet_directory_uri() . '/assets/css/main.css', [], filemtime(get_stylesheet_directory() . '/assets/css/main.css'));
    wp_enqueue_script('youvanna-main', get_stylesheet_directory_uri() . '/assets/js/main.js', [], filemtime(get_stylesheet_directory() . '/assets/js/main.js'), true);
});

// ============================================
// 2. THEME SUPPORT
// ============================================
add_action('after_setup_theme', function() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('menus');
    add_theme_support('html5', ['search-form', 'comment-form', 'gallery', 'caption']);
    register_nav_menus([
        'primary' => 'Menu Principal',
        'footer'  => 'Menu Footer',
    ]);
    add_image_size('hero', 1920, 1080, true);
    add_image_size('card', 600, 400, true);
    add_image_size('thumb-sq', 400, 400, true);
    add_theme_support('automatic-feed-links');
});

// Excerpt length for blog cards

// ============================================
// 3. SCF — Save JSON locally for version control
// ============================================
add_filter('acf/settings/save_json', function() {
    return get_stylesheet_directory() . '/acf-json';
});
add_filter('acf/settings/load_json', function($paths) {
    $paths[] = get_stylesheet_directory() . '/acf-json';
    return $paths;
});

// ============================================
// 4. PERFORMANCE — Remove bloat
// ============================================
add_action('wp_enqueue_scripts', function() {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('classic-theme-styles');
    wp_dequeue_style('global-styles');
    wp_dequeue_script('wp-embed');
}, 100);

remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('wp_head', 'rest_output_link_wp_head', 10);
remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('wp_head', 'wp_oembed_add_host_js');
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

add_action('wp_default_scripts', function($scripts) {
    if (!is_admin() && isset($scripts->registered['jquery'])) {
        $scripts->registered['jquery']->deps = array_diff(
            $scripts->registered['jquery']->deps, ['jquery-migrate']
        );
    }
});

// ============================================
// 4b. ADMIN UX — Interface propre pour le client
// ============================================

// Masquer l'éditeur WP sur les pages (tout est dans ACF)
add_action('admin_init', function() {
    remove_post_type_support('page', 'editor');
});

// Nettoyer le menu admin — cacher ce qui ne sert pas aux clients
add_action('admin_menu', function() {
    remove_menu_page('edit.php?post_type=acf-field-group'); // ACF admin (pas pour le client)
}, 999);

// CSS admin custom pour rendre ACF plus beau
add_action('admin_head', function() {
    $screen = get_current_screen();
    if (!$screen || !in_array($screen->base, ['post', 'toplevel_page_site-settings'])) return;
    ?>
    <style>
    /* Cacher le titre WP sur la homepage (il est fixe) */
    body.post-type-page #titlediv { margin-bottom: 8px; }

    /* ACF Tabs — plus gros, plus clairs */
    .acf-tab-group li a {
        font-size: 14px !important;
        padding: 10px 18px !important;
        font-weight: 600 !important;
    }
    .acf-tab-group li.active a {
        color: #2563eb !important;
        border-bottom-color: #2563eb !important;
    }

    /* ACF Fields — plus aérés */
    .acf-field { padding: 16px 12px !important; }
    .acf-label label { font-size: 14px !important; font-weight: 600 !important; color: #1e293b !important; }
    .acf-field .description { font-size: 13px !important; color: #64748b !important; margin-top: 4px !important; }

    /* ACF Field Groups — style carte */
    .acf-postbox { border: 1px solid #e2e8f0 !important; border-radius: 8px !important; margin-bottom: 20px !important; overflow: hidden; }
    .acf-postbox .postbox-header { background: #f8fafc !important; border-bottom: 1px solid #e2e8f0 !important; }
    .acf-postbox .postbox-header h2 { font-size: 15px !important; font-weight: 700 !important; color: #0f172a !important; padding: 12px 16px !important; }

    /* ACF Repeater — plus lisible */
    .acf-repeater .acf-row { border-color: #e2e8f0 !important; }
    .acf-repeater .acf-row:hover { background: #f8fafc !important; }

    /* ACF Flexible Content — plus clair */
    .acf-flexible-content .layout { border: 1px solid #e2e8f0 !important; border-radius: 8px !important; margin-bottom: 12px !important; }
    .acf-flexible-content .layout .acf-fc-layout-handle { background: #f8fafc !important; font-weight: 600 !important; }

    /* Options page — plus propre */
    .toplevel_page_site-settings .acf-postbox { max-width: 800px; }

    /* Image fields — preview arrondi */
    .acf-image-uploader img { border-radius: 8px !important; }

    /* Messages ACF optionnels dans les onglets */
    .acf-field-message .acf-input p {
        background: linear-gradient(135deg, #fefce8 0%, #fef9c3 100%);
        border: 1px solid #fde68a;
        border-radius: 8px;
        padding: 10px 16px;
        font-size: 13px;
        color: #92400e;
        margin: 0;
    }
    .acf-field-message .acf-label { display: none !important; }

    /* Message d'aide en haut */
    .yv-admin-help {
        background: linear-gradient(135deg, #eff6ff 0%, #f8fafc 100%);
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        padding: 16px 20px;
        margin-bottom: 20px;
        font-size: 14px;
        color: #1e40af;
        line-height: 1.6;
    }
    .yv-admin-help strong { color: #1e3a5f; }
    </style>
    <?php
});

// Message d'aide contextuel sur les pages
add_action('edit_form_after_title', function($post) {
    if ($post->post_type !== 'page') return;

    $is_front = (int)get_option('page_on_front') === $post->ID;
    $template = get_page_template_slug($post->ID);

    if ($is_front) {
        echo '<div class="yv-admin-help">';
        echo '<strong>Page d\'accueil</strong> — Remplissez les sections ci-dessous. ';
        echo 'Seules les sections avec du contenu s\'afficheront sur le site. ';
        echo 'Les sections vides sont automatiquement masquées.';
        echo '</div>';
    } elseif ($template === 'page-contact.php') {
        echo '<div class="yv-admin-help">';
        echo '<strong>Page Contact</strong> — Le formulaire et les coordonnées s\'affichent automatiquement. ';
        echo 'Les coordonnées viennent des <a href="' . esc_url(admin_url('admin.php?page=site-settings')) . '">Réglages du site</a>.';
        echo '</div>';
    } else {
        echo '<div class="yv-admin-help">';
        echo '<strong>Page intérieure</strong> — Ajoutez une image et un titre pour l\'en-tête, ';
        echo 'puis utilisez les "Sections de la page" pour construire votre contenu avec des blocs.';
        echo '</div>';
    }
});

// ============================================
// 5. HELPERS — Fonctions utilitaires (0 duplication)
// ============================================

/**
 * Récupérer un champ ACF avec fallback
 * Supporte 'option' comme 3e argument pour les options globales
 */
function yv_field($name, $fallback = '', $post_id = false) {
    if (!function_exists('get_field')) return $fallback;
    $val = get_field($name, $post_id);
    if ($val === null || $val === '' || $val === false) return $fallback;
    return $val;
}

/**
 * Récupérer l'URL d'une image ACF
 */
function yv_image($name, $size = 'large', $post_id = false) {
    if (!function_exists('get_field')) return '';
    $img = get_field($name, $post_id);
    if (!$img) return '';
    if (is_array($img)) return $img['sizes'][$size] ?? $img['url'];
    return wp_get_attachment_image_url($img, $size) ?: '';
}

/**
 * Raccourci pour options globales (wp_options, préfixe yv_)
 */
function yv_option($name, $fallback = '') {
    $val = get_option('yv_' . $name, '');
    return $val !== '' ? $val : $fallback;
}

/**
 * Render un hero réutilisable (homepage full / page court)
 */
function yv_render_hero($args = []) {
    $a = wp_parse_args($args, [
        'image' => '',
        'title' => get_the_title(),
        'subtitle' => '',
        'buttons' => [],
        'class' => 'page-hero',
    ]);
    ?>
    <section class="<?php echo esc_attr($a['class']); ?>" <?php if ($a['image']): ?>style="background-image: url('<?php echo esc_url($a['image']); ?>')"<?php endif; ?>>
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1><?php echo esc_html($a['title']); ?></h1>
            <?php if ($a['subtitle']): ?>
                <p class="hero-subtitle"><?php echo esc_html($a['subtitle']); ?></p>
            <?php endif; ?>
            <?php if (!empty($a['buttons'])): ?>
                <div class="hero-buttons">
                    <?php foreach ($a['buttons'] as $btn): ?>
                        <a href="<?php echo esc_url($btn['url']); ?>" class="btn <?php echo esc_attr($btn['class'] ?? 'btn-primary'); ?>"><?php echo esc_html($btn['text']); ?></a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php
}

/**
 * Render section title + subtitle (réutilisé partout)
 */
function yv_section_header($title, $subtitle = '', $badge = '') {
    if (!$title) return;
    if ($badge) {
        echo '<span class="section-badge">' . esc_html($badge) . '</span>';
    }
    // Allow <mark> tag in title for color highlight
    echo '<h2 class="section-title">' . wp_kses($title, ['mark' => []]) . '</h2>';
    if ($subtitle) {
        echo '<p class="section-subtitle">' . esc_html($subtitle) . '</p>';
    }
}

/**
 * Render une carte (réutilisée dans services, cards flex, blog)
 */
function yv_render_card($args = []) {
    $a = wp_parse_args($args, [
        'image' => '',
        'image_id' => 0,
        'icon' => '',
        'title' => '',
        'text' => '',
        'link' => null,
    ]);
    ?>
    <div class="card">
        <?php if ($a['image_id']): ?>
            <?php echo wp_get_attachment_image($a['image_id'], 'card', false, ['loading' => 'lazy', 'alt' => esc_attr($a['title'])]); ?>
        <?php elseif ($a['image']): ?>
            <img src="<?php echo esc_url($a['image']); ?>" alt="<?php echo esc_attr($a['title']); ?>" loading="lazy">
        <?php elseif ($a['icon']): ?>
            <div class="card-icon"><?php echo wp_kses_post($a['icon']); ?></div>
        <?php endif; ?>
        <div class="card-body">
            <h3><?php echo esc_html($a['title']); ?></h3>
            <p><?php echo esc_html($a['text']); ?></p>
            <?php if ($a['link'] && is_array($a['link'])): ?>
                <a href="<?php echo esc_url($a['link']['url']); ?>" class="card-link"><?php echo esc_html($a['link']['title'] ?? 'En savoir plus'); ?> &rarr;</a>
            <?php endif; ?>
        </div>
    </div>
    <?php
}



/**
 * Render une grille de stats (réutilisé dans front-page about + section-numbers)
 */
function yv_render_stats($rows, $class = 'stats-grid') {
    if (!$rows || !is_array($rows)) return;
    ?>
    <div class="<?php echo esc_attr($class); ?>">
        <?php foreach ($rows as $stat): ?>
            <div class="stat">
                <span class="stat-number"><?php echo esc_html($stat['number']); ?></span>
                <span class="stat-label"><?php echo esc_html($stat['label']); ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

// ============================================
// 6. PAGE RÉGLAGES DU SITE (WP natif, sans ACF PRO)
// ============================================
add_action('admin_menu', function() {
    add_menu_page(
        'Réglages du site',
        'Réglages du site',
        'manage_options',
        'site-settings',
        'yv_render_settings_page',
        'dashicons-admin-site',
        2
    );
});

add_action('admin_init', function() {
    $sanitizers = [
        'text' => 'sanitize_text_field',
        'email' => 'sanitize_email',
        'url' => 'esc_url_raw',
        'textarea' => 'sanitize_textarea_field',
    ];
    $fields = yv_settings_fields();
    foreach ($fields as $section => $group) {
        foreach ($group['fields'] as $f) {
            register_setting('yv_settings', 'yv_' . $f['name'], [
                'sanitize_callback' => $sanitizers[$f['type']] ?? 'sanitize_text_field',
            ]);
        }
    }
});

function yv_settings_fields() {
    return [
        'coords' => [
            'title' => 'Coordonnées',
            'fields' => [
                ['name' => 'phone', 'label' => 'Téléphone', 'type' => 'text'],
                ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
                ['name' => 'address', 'label' => 'Adresse', 'type' => 'textarea'],
                ['name' => 'opening_hours', 'label' => "Horaires d'ouverture", 'type' => 'textarea'],
                ['name' => 'maps_embed_url', 'label' => 'URL Google Maps Embed', 'type' => 'url', 'desc' => "Copier l'URL src de l'iframe Google Maps"],
                ['name' => 'business_type', 'label' => 'Type d\'activite (schema.org)', 'type' => 'text', 'desc' => 'Ex: Dentist, Restaurant, LegalService, HomeAndConstructionBusiness. Defaut: LocalBusiness'],
            ],
        ],
        'brand' => [
            'title' => 'Boutons & textes',
            'fields' => [
                ['name' => 'footer_description', 'label' => 'Description footer', 'type' => 'textarea'],
                ['name' => 'cta_text', 'label' => 'Texte bouton CTA header', 'type' => 'text', 'default' => 'Nous contacter'],
                ['name' => 'cta_link', 'label' => 'Lien bouton CTA header', 'type' => 'text', 'default' => '/contact'],
            ],
        ],
        'social' => [
            'title' => 'Réseaux sociaux',
            'fields' => [
                ['name' => 'social_facebook', 'label' => 'Facebook URL', 'type' => 'url'],
                ['name' => 'social_instagram', 'label' => 'Instagram URL', 'type' => 'url'],
                ['name' => 'social_linkedin', 'label' => 'LinkedIn URL', 'type' => 'url'],
                ['name' => 'social_youtube', 'label' => 'YouTube URL', 'type' => 'url'],
                ['name' => 'social_tiktok', 'label' => 'TikTok URL', 'type' => 'url'],
            ],
        ],
        'analytics' => [
            'title' => 'Analytics & Tracking',
            'fields' => [
                ['name' => 'gtm_id', 'label' => 'Google Tag Manager ID', 'type' => 'text', 'desc' => 'Ex: GTM-XXXXXXX — Prioritaire sur GA4'],
                ['name' => 'ga_id', 'label' => 'Google Analytics ID', 'type' => 'text', 'desc' => 'Ex: G-XXXXXXXXXX — Utilisé uniquement si pas de GTM'],
            ],
        ],
    ];
}

function yv_render_settings_page() {
    if (!current_user_can('manage_options')) return;
    ?>
    <div class="wrap">
        <h1>Réglages du site</h1>
        <p class="description">Ces informations sont utilisées partout sur le site : header, footer, page contact, schema.org.</p>
        <form method="post" action="options.php">
            <?php settings_fields('yv_settings'); ?>
            <?php foreach (yv_settings_fields() as $section => $group): ?>
            <div class="yv-settings-card">
                <h2><?php echo esc_html($group['title']); ?></h2>
                <table class="form-table">
                    <?php foreach ($group['fields'] as $f):
                        $key = 'yv_' . $f['name'];
                        $val = get_option($key, $f['default'] ?? '');
                    ?>
                    <tr>
                        <th><label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($f['label']); ?></label></th>
                        <td>
                            <?php if ($f['type'] === 'textarea'): ?>
                                <textarea name="<?php echo esc_attr($key); ?>" id="<?php echo esc_attr($key); ?>" rows="3" class="large-text"><?php echo esc_textarea($val); ?></textarea>
                            <?php else: ?>
                                <input type="<?php echo esc_attr($f['type']); ?>" name="<?php echo esc_attr($key); ?>" id="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($val); ?>" class="regular-text">
                            <?php endif; ?>
                            <?php if (!empty($f['desc'])): ?>
                                <p class="description"><?php echo esc_html($f['desc']); ?></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <?php endforeach; ?>
            <?php submit_button('Enregistrer'); ?>
        </form>
    </div>
    <style>
    .yv-settings-card{background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:20px 24px;margin-bottom:20px;max-width:800px}
    .yv-settings-card h2{font-size:16px;font-weight:700;color:#0f172a;margin:0 0 12px;padding:0}
    .yv-settings-card .form-table th{font-weight:600;color:#1e293b;width:200px}
    .yv-settings-card .form-table td input,.yv-settings-card .form-table td textarea{width:100%;max-width:500px}
    </style>
    <?php
}

// ============================================
// 7. ACF FIELD GROUPS — All via PHP (no GUI)
// ============================================
add_action('acf/include_fields', function() {
    if (!function_exists('acf_add_local_field_group')) return;

    // --- HOMEPAGE ---
    acf_add_local_field_group([
        'key' => 'yv_homepage',
        'title' => 'Homepage — Sections',
        'fields' => [
            ['key' => 'yv_h_tab1', 'label' => 'Bandeau principal', 'type' => 'tab'],
            ['key' => 'yv_hero_img', 'label' => 'Image de fond', 'name' => 'hero_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium', 'instructions' => 'Grande image en arrière-plan. Taille idéale : 1920x1080px.'],
            ['key' => 'yv_hero_title', 'label' => 'Titre principal', 'name' => 'hero_title', 'type' => 'text', 'instructions' => 'Le gros titre affiché sur la bannière.'],
            ['key' => 'yv_hero_sub', 'label' => 'Sous-titre', 'name' => 'hero_subtitle', 'type' => 'textarea', 'rows' => 2, 'instructions' => 'Texte court sous le titre (1-2 lignes).'],
            ['key' => 'yv_hero_cta1t', 'label' => 'Bouton principal — texte', 'name' => 'hero_cta1_text', 'type' => 'text', 'instructions' => 'Ex: "Découvrir nos services". Laisser vide pour masquer.'],
            ['key' => 'yv_hero_cta1l', 'label' => 'Bouton principal — lien', 'name' => 'hero_cta1_link', 'type' => 'text', 'instructions' => 'Ex: /nos-services'],
            ['key' => 'yv_hero_cta2t', 'label' => 'Bouton secondaire — texte', 'name' => 'hero_cta2_text', 'type' => 'text', 'instructions' => 'Optionnel. Ex: "Nous contacter"'],
            ['key' => 'yv_hero_cta2l', 'label' => 'Bouton secondaire — lien', 'name' => 'hero_cta2_link', 'type' => 'text'],
            ['key' => 'yv_h_tab2', 'label' => 'Nos services', 'type' => 'tab'],
            ['key' => 'yv_h_msg2', 'type' => 'message', 'message' => '💡 <strong>Section optionnelle</strong> — Si vous ne remplissez rien ici, cette section ne s\'affichera pas sur le site.', 'new_lines' => ''],
            ['key' => 'yv_srv_title', 'label' => 'Titre de la section', 'name' => 'services_title', 'type' => 'text', 'instructions' => 'Ex: "Nos Services". Laisser vide pour masquer toute la section.'],
            ['key' => 'yv_srv_sub', 'label' => 'Sous-titre', 'name' => 'services_subtitle', 'type' => 'textarea', 'rows' => 2, 'instructions' => 'Optionnel. Texte court sous le titre.'],
            ['key' => 'yv_srv', 'label' => 'Services', 'name' => 'services', 'type' => 'repeater', 'layout' => 'block', 'sub_fields' => [
                ['key' => 'yv_srv_icon', 'label' => 'Icône (emoji ou SVG)', 'name' => 'icon', 'type' => 'text'],
                ['key' => 'yv_srv_img', 'label' => 'Image', 'name' => 'image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'thumbnail'],
                ['key' => 'yv_srv_t', 'label' => 'Titre', 'name' => 'title', 'type' => 'text'],
                ['key' => 'yv_srv_d', 'label' => 'Description', 'name' => 'description', 'type' => 'textarea', 'rows' => 3],
                ['key' => 'yv_srv_l', 'label' => 'Lien', 'name' => 'link', 'type' => 'link'],
            ]],
            ['key' => 'yv_h_tab3', 'label' => 'Présentation', 'type' => 'tab'],
            ['key' => 'yv_h_msg3', 'type' => 'message', 'message' => '💡 <strong>Section optionnelle</strong> — Si vous ne remplissez rien ici, cette section ne s\'affichera pas sur le site.', 'new_lines' => ''],
            ['key' => 'yv_ab_img', 'label' => 'Image', 'name' => 'about_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium'],
            ['key' => 'yv_ab_title', 'label' => 'Titre', 'name' => 'about_title', 'type' => 'text'],
            ['key' => 'yv_ab_text', 'label' => 'Texte', 'name' => 'about_text', 'type' => 'wysiwyg', 'media_upload' => 0],
            ['key' => 'yv_ab_btn', 'label' => 'Bouton', 'name' => 'about_button', 'type' => 'link'],
            ['key' => 'yv_stats', 'label' => 'Statistiques', 'name' => 'stats', 'type' => 'repeater', 'layout' => 'table', 'sub_fields' => [
                ['key' => 'yv_stat_n', 'label' => 'Chiffre', 'name' => 'number', 'type' => 'text'],
                ['key' => 'yv_stat_l', 'label' => 'Label', 'name' => 'label', 'type' => 'text'],
            ]],
            ['key' => 'yv_h_tab5', 'label' => 'Avis clients', 'type' => 'tab'],
            ['key' => 'yv_h_msg5', 'type' => 'message', 'message' => '💡 <strong>Section optionnelle</strong> — Si vous n\'ajoutez aucun avis, cette section ne s\'affichera pas sur le site.', 'new_lines' => ''],
            ['key' => 'yv_testi_title', 'label' => 'Titre de la section', 'name' => 'testimonials_title', 'type' => 'text', 'default_value' => 'Ce que disent nos clients', 'instructions' => 'Optionnel. Si aucun avis n\'est ajouté, cette section ne s\'affiche pas.'],
            ['key' => 'yv_testi', 'label' => 'Avis', 'name' => 'testimonials', 'type' => 'repeater', 'layout' => 'block', 'sub_fields' => [
                ['key' => 'yv_testi_txt', 'label' => 'Texte', 'name' => 'text', 'type' => 'textarea', 'rows' => 3],
                ['key' => 'yv_testi_name', 'label' => 'Nom', 'name' => 'name', 'type' => 'text'],
                ['key' => 'yv_testi_role', 'label' => 'Rôle / Entreprise', 'name' => 'role', 'type' => 'text'],
                ['key' => 'yv_testi_photo', 'label' => 'Photo', 'name' => 'photo', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'thumbnail'],
                ['key' => 'yv_testi_stars', 'label' => 'Note (1-5)', 'name' => 'rating', 'type' => 'number', 'min' => 1, 'max' => 5, 'default_value' => 5],
            ]],
            ['key' => 'yv_h_tab4', 'label' => 'Bandeau d\'appel', 'type' => 'tab'],
            ['key' => 'yv_h_msg4', 'type' => 'message', 'message' => '💡 <strong>Section optionnelle</strong> — Si vous ne remplissez pas le titre, ce bandeau ne s\'affichera pas sur le site.', 'new_lines' => ''],
            ['key' => 'yv_cta_bg', 'label' => 'Image fond', 'name' => 'cta_background', 'type' => 'image', 'return_format' => 'array'],
            ['key' => 'yv_cta_t', 'label' => 'Titre', 'name' => 'cta_title', 'type' => 'text'],
            ['key' => 'yv_cta_txt', 'label' => 'Texte', 'name' => 'cta_text_home', 'type' => 'textarea', 'rows' => 2],
            ['key' => 'yv_cta_btn_t', 'label' => 'Bouton texte', 'name' => 'cta_button_text', 'type' => 'text'],
            ['key' => 'yv_cta_btn_l', 'label' => 'Bouton lien', 'name' => 'cta_button_link', 'type' => 'text'],
        ],
        'location' => [[['param' => 'page_type', 'operator' => '==', 'value' => 'front_page']]],
    ]);

    // --- PAGE HERO (pages intérieures seulement, PAS la homepage) ---
    acf_add_local_field_group([
        'key' => 'yv_page',
        'title' => "Bandeau de la page",
        'fields' => [
            ['key' => 'yv_p_hero_img', 'label' => 'Image de fond', 'name' => 'page_hero_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium', 'instructions' => 'Image en arrière-plan du bandeau. Taille idéale : 1920x600px.'],
            ['key' => 'yv_p_hero_t', 'label' => 'Titre (vide = titre de la page)', 'name' => 'page_hero_title', 'type' => 'text'],
            ['key' => 'yv_p_hero_sub', 'label' => 'Sous-titre', 'name' => 'page_hero_subtitle', 'type' => 'textarea', 'rows' => 2],
        ],
        'location' => [[
            ['param' => 'post_type', 'operator' => '==', 'value' => 'page'],
            ['param' => 'page_type', 'operator' => '!=', 'value' => 'front_page'],
        ]],
    ]);

    // --- FLEXIBLE CONTENT (inner pages) ---
    acf_add_local_field_group([
        'key' => 'yv_flex',
        'title' => 'Sections de la page',
        'fields' => [
            ['key' => 'yv_sections', 'label' => 'Sections', 'name' => 'sections', 'type' => 'flexible_content', 'layouts' => [
                ['key' => 'yv_fl_ti', 'name' => 'text_image', 'label' => 'Bloc texte + image', 'sub_fields' => [
                    ['key' => 'yv_fl_ti_t', 'label' => 'Titre', 'name' => 'title', 'type' => 'text'],
                    ['key' => 'yv_fl_ti_txt', 'label' => 'Texte', 'name' => 'text', 'type' => 'wysiwyg', 'media_upload' => 0],
                    ['key' => 'yv_fl_ti_img', 'label' => 'Image', 'name' => 'image', 'type' => 'image', 'return_format' => 'array'],
                    ['key' => 'yv_fl_ti_pos', 'label' => 'Position image', 'name' => 'image_position', 'type' => 'select', 'choices' => ['right' => 'Droite', 'left' => 'Gauche']],
                    ['key' => 'yv_fl_ti_lnk', 'label' => 'Bouton', 'name' => 'link', 'type' => 'link'],
                ]],
                ['key' => 'yv_fl_cards', 'name' => 'cards', 'label' => 'Grille de cartes', 'sub_fields' => [
                    ['key' => 'yv_fl_c_t', 'label' => 'Titre section', 'name' => 'title', 'type' => 'text'],
                    ['key' => 'yv_fl_c_sub', 'label' => 'Sous-titre', 'name' => 'subtitle', 'type' => 'textarea', 'rows' => 2],
                    ['key' => 'yv_fl_c_cols', 'label' => 'Colonnes', 'name' => 'columns', 'type' => 'select', 'choices' => ['2' => '2', '3' => '3', '4' => '4'], 'default_value' => '3'],
                    ['key' => 'yv_fl_c_rpt', 'label' => 'Cartes', 'name' => 'cards', 'type' => 'repeater', 'layout' => 'block', 'sub_fields' => [
                        ['key' => 'yv_fl_c_img', 'label' => 'Image', 'name' => 'image', 'type' => 'image', 'return_format' => 'array'],
                        ['key' => 'yv_fl_c_ct', 'label' => 'Titre', 'name' => 'title', 'type' => 'text'],
                        ['key' => 'yv_fl_c_cd', 'label' => 'Description', 'name' => 'description', 'type' => 'textarea', 'rows' => 3],
                        ['key' => 'yv_fl_c_cl', 'label' => 'Lien', 'name' => 'link', 'type' => 'link'],
                    ]],
                ]],
                ['key' => 'yv_fl_cta', 'name' => 'cta', 'label' => 'Bandeau d\'appel à l\'action', 'sub_fields' => [
                    ['key' => 'yv_fl_cta_bg', 'label' => 'Image fond', 'name' => 'background', 'type' => 'image', 'return_format' => 'array'],
                    ['key' => 'yv_fl_cta_t', 'label' => 'Titre', 'name' => 'title', 'type' => 'text'],
                    ['key' => 'yv_fl_cta_txt', 'label' => 'Texte', 'name' => 'text', 'type' => 'textarea', 'rows' => 2],
                    ['key' => 'yv_fl_cta_btn', 'label' => 'Bouton', 'name' => 'button', 'type' => 'link'],
                ]],
                ['key' => 'yv_fl_testi', 'name' => 'testimonials', 'label' => 'Avis clients', 'sub_fields' => [
                    ['key' => 'yv_fl_testi_t', 'label' => 'Titre', 'name' => 'title', 'type' => 'text', 'default_value' => 'Témoignages'],
                    ['key' => 'yv_fl_testi_rpt', 'label' => 'Témoignages', 'name' => 'items', 'type' => 'repeater', 'layout' => 'block', 'sub_fields' => [
                        ['key' => 'yv_fl_testi_txt', 'label' => 'Texte', 'name' => 'text', 'type' => 'textarea', 'rows' => 3],
                        ['key' => 'yv_fl_testi_name', 'label' => 'Nom', 'name' => 'name', 'type' => 'text'],
                        ['key' => 'yv_fl_testi_role', 'label' => 'Rôle', 'name' => 'role', 'type' => 'text'],
                        ['key' => 'yv_fl_testi_photo', 'label' => 'Photo', 'name' => 'photo', 'type' => 'image', 'return_format' => 'array'],
                        ['key' => 'yv_fl_testi_stars', 'label' => 'Note (1-5)', 'name' => 'rating', 'type' => 'number', 'min' => 1, 'max' => 5, 'default_value' => 5],
                    ]],
                ]],
                ['key' => 'yv_fl_faq', 'name' => 'faq', 'label' => 'Questions fréquentes', 'sub_fields' => [
                    ['key' => 'yv_fl_faq_t', 'label' => 'Titre', 'name' => 'title', 'type' => 'text', 'default_value' => 'Questions fréquentes'],
                    ['key' => 'yv_fl_faq_rpt', 'label' => 'Questions', 'name' => 'items', 'type' => 'repeater', 'layout' => 'block', 'sub_fields' => [
                        ['key' => 'yv_fl_faq_q', 'label' => 'Question', 'name' => 'question', 'type' => 'text'],
                        ['key' => 'yv_fl_faq_a', 'label' => 'Réponse', 'name' => 'answer', 'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic'],
                    ]],
                ]],
                ['key' => 'yv_fl_gallery', 'name' => 'gallery', 'label' => 'Galerie photos', 'sub_fields' => [
                    ['key' => 'yv_fl_gal_t', 'label' => 'Titre', 'name' => 'title', 'type' => 'text'],
                    ['key' => 'yv_fl_gal_cols', 'label' => 'Colonnes', 'name' => 'columns', 'type' => 'select', 'choices' => ['2' => '2', '3' => '3', '4' => '4'], 'default_value' => '3'],
                    ['key' => 'yv_fl_gal_imgs', 'label' => 'Images', 'name' => 'images', 'type' => 'gallery', 'return_format' => 'array', 'preview_size' => 'thumbnail'],
                ]],
                ['key' => 'yv_fl_map', 'name' => 'map', 'label' => 'Google Maps', 'sub_fields' => [
                    ['key' => 'yv_fl_map_t', 'label' => 'Titre (optionnel)', 'name' => 'title', 'type' => 'text'],
                    ['key' => 'yv_fl_map_url', 'label' => 'URL iframe Google Maps', 'name' => 'map_url', 'type' => 'url'],
                    ['key' => 'yv_fl_map_h', 'label' => 'Hauteur (px)', 'name' => 'height', 'type' => 'number', 'default_value' => 450],
                ]],
 ['key' => 'yv_fl_text', 'name' => 'text', 'label' => 'Bloc de texte', 'sub_fields' => [
                    ['key' => 'yv_fl_txt_t', 'label' => 'Titre (optionnel)', 'name' => 'title', 'type' => 'text'],
                    ['key' => 'yv_fl_txt_content', 'label' => 'Contenu', 'name' => 'content', 'type' => 'wysiwyg', 'media_upload' => 1, 'toolbar' => 'full'],
                    ['key' => 'yv_fl_txt_narrow', 'label' => 'Largeur reduite', 'name' => 'narrow', 'type' => 'true_false', 'default_value' => 1, 'ui' => 1],
                ]],
                ['key' => 'yv_fl_video', 'name' => 'video', 'label' => 'Video', 'sub_fields' => [
                    ['key' => 'yv_fl_vid_t', 'label' => 'Titre (optionnel)', 'name' => 'title', 'type' => 'text'],
                    ['key' => 'yv_fl_vid_url', 'label' => 'URL YouTube ou Vimeo', 'name' => 'video_url', 'type' => 'url'],
                ]],
                ['key' => 'yv_fl_team', 'name' => 'team', 'label' => 'Equipe', 'sub_fields' => [
                    ['key' => 'yv_fl_team_t', 'label' => 'Titre', 'name' => 'title', 'type' => 'text'],
                    ['key' => 'yv_fl_team_sub', 'label' => 'Sous-titre', 'name' => 'subtitle', 'type' => 'textarea', 'rows' => 2],
                    ['key' => 'yv_fl_team_cols', 'label' => 'Colonnes', 'name' => 'columns', 'type' => 'select', 'choices' => ['3' => '3', '4' => '4'], 'default_value' => '3'],
                    ['key' => 'yv_fl_team_rpt', 'label' => 'Membres', 'name' => 'members', 'type' => 'repeater', 'layout' => 'block', 'sub_fields' => [
                        ['key' => 'yv_fl_team_photo', 'label' => 'Photo', 'name' => 'photo', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'thumbnail'],
                        ['key' => 'yv_fl_team_name', 'label' => 'Nom', 'name' => 'name', 'type' => 'text'],
                        ['key' => 'yv_fl_team_role', 'label' => 'Poste', 'name' => 'role', 'type' => 'text'],
                        ['key' => 'yv_fl_team_bio', 'label' => 'Bio courte', 'name' => 'bio', 'type' => 'textarea', 'rows' => 2],
                    ]],
                ]],
               ['key' => 'yv_fl_numbers', 'name' => 'numbers', 'label' => 'Chiffres clés', 'sub_fields' => [
                    ['key' => 'yv_fl_num_t', 'label' => 'Titre', 'name' => 'title', 'type' => 'text'],
                    ['key' => 'yv_fl_num_bg', 'label' => 'Couleur de fond', 'name' => 'bg_color', 'type' => 'select', 'choices' => ['light' => 'Clair', 'primary' => 'Couleur principale', 'dark' => 'Sombre'], 'default_value' => 'light'],
                    ['key' => 'yv_fl_num_rpt', 'label' => 'Chiffres', 'name' => 'items', 'type' => 'repeater', 'layout' => 'table', 'sub_fields' => [
                        ['key' => 'yv_fl_num_n', 'label' => 'Chiffre', 'name' => 'number', 'type' => 'text'],
                        ['key' => 'yv_fl_num_l', 'label' => 'Label', 'name' => 'label', 'type' => 'text'],
                    ]],
                ]],
            ]],
        ],
        'location' => [[
            ['param' => 'post_type', 'operator' => '==', 'value' => 'page'],
            ['param' => 'page_type', 'operator' => '!=', 'value' => 'front_page'],
        ]],
    ]);

    // --- CONTACT PAGE ---
    acf_add_local_field_group([
        'key' => 'yv_contact',
        'title' => 'Page Contact',
        'fields' => [
            ['key' => 'yv_ct_form_t', 'label' => 'Titre formulaire', 'name' => 'contact_form_title', 'type' => 'text', 'default_value' => 'Envoyez-nous un message'],
            ['key' => 'yv_ct_form_id', 'label' => 'ID formulaire CF7', 'name' => 'contact_form_id', 'type' => 'text', 'instructions' => 'Numéro du formulaire Contact Form 7'],
            ['key' => 'yv_ct_show_map', 'label' => 'Afficher Google Maps', 'name' => 'show_map', 'type' => 'true_false', 'default_value' => 1, 'ui' => 1],
        ],
        'location' => [[['param' => 'page_template', 'operator' => '==', 'value' => 'page-contact.php']]],
    ]);
});

// ============================================
// 8. GTM/GA — Chargé uniquement après consentement cookies
// ============================================
add_action('wp_head', function() {
    $gtm = yv_option('gtm_id');
    if ($gtm): ?>
    <script>window.dataLayer=window.dataLayer||[];
    function loadGTM(){if(window._gtmLoaded)return;window._gtmLoaded=true;
    dataLayer.push({'gtm.start':new Date().getTime(),event:'gtm.js'});
    var j=document.createElement('script');j.async=true;
    j.src='https://www.googletagmanager.com/gtm.js?id=<?php echo esc_js($gtm); ?>';
    document.head.appendChild(j);}
    document.addEventListener('yv_consent_update',function(e){if(e.detail.accepted&&e.detail.accepted.length)loadGTM();});
    if(document.cookie.indexOf('yv_consent=yes')!==-1)loadGTM();
    </script>
    <?php endif;

    // Fallback GA4 direct si pas de GTM
    $ga = yv_option('ga_id');
    if ($ga && !$gtm): ?>
    <script>
    function loadGA(){if(window._gaLoaded)return;window._gaLoaded=true;
    var j=document.createElement('script');j.async=true;
    j.src='https://www.googletagmanager.com/gtag/js?id=<?php echo esc_js($ga); ?>';
    document.head.appendChild(j);
    j.onload=function(){window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?php echo esc_js($ga); ?>');};}
    document.addEventListener('yv_consent_update',function(e){if(e.detail.accepted&&e.detail.accepted.length)loadGA();});
    if(document.cookie.indexOf('yv_consent=yes')!==-1)loadGA();
    </script>
    <?php endif;
}, 1);

// ============================================
// 9. SCHEMA.ORG JSON-LD — Full SEO optimized
// ============================================

// WebSite schema
add_action('wp_head', function() {
    if (!is_front_page()) return;
    echo '<script type="application/ld+json">' . wp_json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => get_bloginfo('name'),
        'url' => home_url('/'),
        'description' => get_bloginfo('description'),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</script>\n";
}, 4);

// BreadcrumbList on all pages except homepage
add_action('wp_head', function() {
    if (is_front_page()) return;
    $items = [['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => home_url('/')]];
    if (is_page()) {
        $items[] = ['@type' => 'ListItem', 'position' => 2, 'name' => get_the_title(), 'item' => get_permalink()];
    } elseif (is_single()) {
        $cats = get_the_category();
        if ($cats) {
            $items[] = ['@type' => 'ListItem', 'position' => 2, 'name' => $cats[0]->name, 'item' => get_category_link($cats[0]->term_id)];
            $items[] = ['@type' => 'ListItem', 'position' => 3, 'name' => get_the_title(), 'item' => get_permalink()];
        } else {
            $items[] = ['@type' => 'ListItem', 'position' => 2, 'name' => get_the_title(), 'item' => get_permalink()];
        }
    } elseif (is_category()) {
        $items[] = ['@type' => 'ListItem', 'position' => 2, 'name' => single_cat_title('', false), 'item' => get_category_link(get_queried_object_id())];
    }
    echo '<script type="application/ld+json">' . wp_json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $items,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</script>\n";
}, 4);

// LocalBusiness schema (homepage)
add_action('wp_head', function() {
    if (!is_front_page()) return;
    $biz_type = yv_option('business_type', 'LocalBusiness');
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => $biz_type,
        '@id' => home_url('/#organization'),
        'name' => get_bloginfo('name'),
        'description' => get_bloginfo('description'),
        'url' => home_url('/'),
    ];
    $phone = yv_option('phone');
    if ($phone) $schema['telephone'] = $phone;
    $email = yv_option('email');
    if ($email) $schema['email'] = $email;
    $address = yv_option('address');
    if ($address) $schema['address'] = ['@type' => 'PostalAddress', 'streetAddress' => $address];
    $logo_id = get_theme_mod('custom_logo');
    $logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : false;
    if ($logo_url) {
        $schema['logo'] = $logo_url;
        $schema['image'] = $logo_url;
    }
    // Opening hours
    $hours = yv_option('opening_hours');
    if ($hours) $schema['openingHours'] = array_filter(array_map('trim', explode("\n", $hours)));
    // Social profiles (sameAs)
    $same_as = [];
    foreach (['social_facebook','social_instagram','social_linkedin','social_youtube','social_tiktok'] as $key) {
        $url = yv_option($key);
        if ($url) $same_as[] = $url;
    }
    if ($same_as) $schema['sameAs'] = $same_as;
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</script>\n";
}, 5);

// Schema.org — BlogPosting pour les articles
add_action('wp_head', function() {
    if (!is_single()) return;
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => get_the_title(),
        'url' => get_permalink(),
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => get_permalink()],
        'datePublished' => get_the_date('c'),
        'dateModified' => get_the_modified_date('c'),
        'author' => ['@type' => 'Person', 'name' => get_the_author()],
        'publisher' => ['@type' => 'Organization', 'name' => get_bloginfo('name')],
        'description' => get_the_excerpt(),
    ];
    $thumb = get_the_post_thumbnail_url(null, 'large');
    $schema['image'] = $thumb ?: home_url('/wp-includes/images/blank.gif');
    $logo_id = get_theme_mod('custom_logo');
    if ($logo_id) $schema['publisher']['logo'] = ['@type' => 'ImageObject', 'url' => wp_get_attachment_image_url($logo_id, 'full')];
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</script>\n";
}, 5);

// Schema.org — FAQPage pour les pages avec section FAQ
add_action('wp_head', function() {
    if (!is_page() || !function_exists('get_field')) return;
    $sections = get_field('sections');
    if (!$sections || !is_array($sections)) return;
    $faq_items = [];
    foreach ($sections as $section) {
        if (($section['acf_fc_layout'] ?? '') !== 'faq') continue;
        if (empty($section['items'])) continue;
        foreach ($section['items'] as $item) {
            if (empty($item['question'])) continue;
            $faq_items[] = [
                '@type' => 'Question',
                'name' => $item['question'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => wp_strip_all_tags($item['answer'] ?? '')],
            ];
        }
    }
    if (empty($faq_items)) return;
    echo '<script type="application/ld+json">' . wp_json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $faq_items,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</script>\n";
}, 5);

// Preload hero image for LCP performance
add_action('wp_head', function() {
    if (is_front_page()) {
        $hero_img = yv_image('hero_image', 'hero');
    } elseif (is_singular('post')) {
        $hero_img = get_the_post_thumbnail_url(null, 'hero');
    } elseif (is_home()) {
        $blog_id = get_option('page_for_posts');
        $hero_img = $blog_id ? yv_image('page_hero_image', 'hero', $blog_id) : '';
    } elseif (is_page()) {
        $hero_img = yv_image('page_hero_image', 'hero');
    } else {
        $hero_img = '';
    }
    if ($hero_img) {
        echo '<link rel="preload" as="image" href="' . esc_url($hero_img) . '" fetchpriority="high">' . "\n";
    }
}, 1);

// Noscript fallback: show content if JS disabled
add_action('wp_head', function() {
    echo '<noscript><style>.reveal{opacity:1!important;transform:none!important}.reveal .card,.reveal .faq-item,.reveal .stat,.reveal .testimonial-card{opacity:1!important;transform:none!important}</style></noscript>' . "\n";
}, 2);

// Preconnect CDN + GTM/GA
add_action('wp_head', function() {
    echo '<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>' . "\n";
    if (yv_option('gtm_id') || yv_option('ga_id')) {
        echo '<link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>' . "\n";
        echo '<link rel="dns-prefetch" href="https://www.googletagmanager.com">' . "\n";
    }
}, 1);

// ============================================
// 10. BLOG — Excerpt
// ============================================
add_filter('excerpt_length', function() { return 25; });
add_filter('excerpt_more', function() { return '&hellip;'; });

// ============================================
// 11. TYPOGRAPHIE — Remplacer les caractères spéciaux
// ============================================
// Em-dash, en-dash, smart quotes → caractères normaux
function yv_clean_typography($content) {
    if (!is_string($content)) return $content;
    $search  = ["\xe2\x80\x94", "\xe2\x80\x93", "\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\xa6"];
    $replace = ['-',             '-',             "'",             "'",             '"',             '"',             '...'];
    return str_replace($search, $replace, $content);
}
add_filter('the_content', 'yv_clean_typography');
add_filter('the_title', 'yv_clean_typography');
add_filter('the_excerpt', 'yv_clean_typography');
add_filter('acf/format_value', 'yv_clean_typography', 20);
