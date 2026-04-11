# Youvanna Starter v2.7.0 - Guide Claude Code

Ce fichier est lu automatiquement par Claude Code. Il contient toutes les règles, conventions, feedbacks et contexte pour travailler sur les sites Youvanna.

---

## Règles absolues

1. **JAMAIS de couleur hardcodée** - Toute couleur passe par les CSS variables `:root` dans `main.css`. NULLE PART ailleurs.
2. **JAMAIS de code dupliqué** - Utilise les helpers PHP existants (yv_field, yv_option, yv_image, yv_img, yv_render_card, etc.)
3. **JAMAIS hardcoder une info client** - Tout passe par `wp option update yv_xxx` ou `update_field()`
4. **TOUJOURS les accents français** - é, è, ê, ë, à, â, ù, û, ô, ç, î, œ, æ. JAMAIS "equipe" -> toujours "équipe", JAMAIS "Pret" -> toujours "Prêt". Si SSH pose des problèmes d'encodage, utiliser `scp` + `wp eval-file` (préserve l'UTF-8).
5. **JAMAIS inventer ce qu'on ne sait pas** - Pas de faux avis, pas de faux chiffres, pas de fausses données. Lorem Ipsum pour les témoignages si pas de vrais avis. Pas de section numbers si pas de chiffres vérifiés. Les descriptions de services doivent être basées sur des infos réelles (site existant, brief client). Règle numéro 1 de l'utilisateur.
6. **JAMAIS de tirets spéciaux** - Utiliser `-` uniquement. Pas de em-dash `—`, en-dash `–`, smart quotes. Ce sont des tells IA que Google détecte. Filet de sécurité : `mu-plugins/yv-no-emdash.php`.
7. **TOUJOURS des images Gemini sur les cartes** - Pas d'icônes quand on peut mettre une image. Icônes FA en fallback uniquement si 6+ cartes.
8. **JAMAIS de texte sur les images Gemini** - Toujours inclure "no text, no words, no letters, no logos, no watermarks" dans les prompts.
9. **Ratio d'image = réfléchir au contenu** - Le ratio dépend de la quantité de texte à côté. Rédiger le contenu AVANT de générer les images. Texte court -> 4:3, texte moyen -> 1:1 ou 4:5, texte long -> 3:4 portrait. Heroes toujours 16:9.
10. **Schema.org = automatique** - LocalBusiness, WebSite (avec SearchAction), BreadcrumbList, FAQPage, BlogPosting. JAMAIS écrire de schema manuellement.
11. **Animations = automatiques** - Counter (skip prefers-reduced-motion), stagger (cards, faq, stats, testimonials, team), parallax, marquee (will-change: transform), FAQ smooth, back-to-top. Ne rien ajouter.
12. **TOUJOURS des FAQ pour le SEO** - Ajouter une section FAQ sur chaque page pertinente.
13. **JAMAIS de `<mark>` dans les titres** - Pas de surligné/highlight. Les titres sont en texte brut. Les badges pills sont acceptés via le 3e argument de `yv_section_header()`.
14. **TOUJOURS sync --color-primary-rgb** - Quand on change `--color-primary`, mettre à jour `--color-primary-rgb` avec les valeurs R, G, B.
15. **OG Meta = automatique** - Open Graph + Twitter Card générés automatiquement si Yoast/RankMath n'est pas actif.
16. **TOUJOURS utiliser wp_get_attachment_image()** - Pour les images SCF, utiliser `yv_img()` ou `wp_get_attachment_image($img['ID'])` au lieu de `<img src>` brut. Donne width/height/srcset/sizes automatiquement.
17. **SCF, pas ACF** - On utilise SCF (Secure Custom Fields), le plugin officiel WordPress. Fork gratuit d'ACF avec Repeater + Flex Content intégrés. L'API garde le préfixe `acf_` pour compatibilité, mais dans les commentaires et la doc on écrit SCF.
18. **TOUJOURS font-display: swap sur TOUTES les fonts** - Le thème override FA CDN (`font-display: block`) avec un inline `font-display: swap` dans `wp_head`. Google Fonts DOIT toujours inclure `&display=swap`. JAMAIS charger une font externe sans `font-display: swap` - ça bloque le FCP et casse les scores PageSpeed.
19. **JAMAIS de `<style>` ou `<script>` dans `post_content`** - `wp_kses_post` strippe les tags quand on écrit via CLI/PHP (pas de capability `unfiltered_html`) ET `wptexturize` bousille les `var(--color-*)` en `var(&#8211;color-*)`. Le CSS va TOUJOURS dans `assets/css/main.css`, le JS dans `assets/js/main.js`. Le post_content doit être du HTML pur.
20. **JAMAIS `esc_html()` sur du contenu riche** - Si un champ SCF peut contenir du HTML (wysiwyg, textarea, description, contenu flexible), utiliser `wp_kses_post()`. `esc_html()` rend `<p>` et `<strong>` en texte littéral visible. Réserver `esc_html()` aux titres, labels, attributs, URLs.
21. **Header full-width** - La `.nav-container` doit utiliser `--max-width-wide` (1440px) pas `--max-width` (1320px).
22. **Hover card = signal de clickability** - `.card:hover { transform }` est INTERDIT. L'animation hover doit UNIQUEMENT cibler `a.card-clickable:hover`. Les cards non-cliquables ne bougent pas.
23. **Cards doivent pointer vers les VRAIS permalinks** - Ne jamais écrire `/adhesion` en espérant que Yoast redirige. Toujours le chemin hiérarchique complet `/la-societe/adhesion/`. Utiliser `get_permalink($id)` à l'import.
24. **Footer : DEUX menus OBLIGATOIRES** - Le skill Agent 4 doit créer `Menu Principal` ET `Menu Footer`. Vérifier : `wp menu location list` doit montrer primary + footer avec un menu assigné. Le footer.php a un fallback (pages top-level) mais ce n'est qu'un filet de sécurité.
25. **JAMAIS wrapper `the_custom_logo()` dans un `<a>`** - Cette fonction génère déjà son propre `<a class="custom-logo-link">`. L'imbriquer dans un `<a>` crée du HTML invalide. Toujours utiliser `wp_get_attachment_image(get_theme_mod('custom_logo'), 'full', false, [...])` pour extraire juste l'image.
26. **JAMAIS `aspect-ratio` + `min-height` sans media query** - Sur mobile, le navigateur calcule la largeur depuis la hauteur (`width = min-height * aspect-ratio`), ce qui déborde le viewport. Toujours scoper dans `@media (min-width: 769px)`.
27. **TOUJOURS `overflow-x: hidden` sur `html` et `body`** - Safety net contre tout élément qui dépasserait. Combiné avec `max-width: 100vw` sur le body.
28. **TOUJOURS tester mobile AVANT de dire que c'est fini** - Capture headless à 375px avec `node /tmp/screenshot.js <url> <out>`. Chromium installé sur le serveur.
29. **TOUJOURS php -l après chaque modification PHP** - Vérifier la syntaxe avant de considérer le travail fini.
30. **TOUJOURS flush le cache après modif front** - WP Super Cache + Redis object cache. Commande : `wp cache flush --allow-root && find wp-content/cache -type f -delete`. Puis vérifier via `curl -s https://SITE/?nocache=$(date +%s)`.
31. **Modèle image OBLIGATOIRE : gemini-3-pro-image-preview** - JAMAIS imagen-3, imagen-3.0-generate-002, imagen-4, ou tout autre modèle Imagen. Règle absolue.
32. **JAMAIS `transition: all`** - Toujours lister les propriétés composited (`transform`, `opacity`, `background-color`, `border-color`, `color`, `box-shadow`). `transition: all` applique les transitions à TOUTES les propriétés héritées et casse les animations GPU-composited (Lighthouse "avoid non-composited animations"). Exception : aucune.
33. **JAMAIS animer `box-shadow` ou `filter` en @keyframes** - Paint-heavy sur chaque frame. Utiliser un pseudo-element `::before` ou `::after` avec `box-shadow` fixe + animer son `opacity`. Pattern dans `main.css` : `.image-stat-badge::after { box-shadow: ...; opacity: 0; animation: badgePulseOpacity 4s... }`.
34. **TOUJOURS critical CSS inline + main.css non-blocking** - `functions.php` inline `assets/css/critical.css` (4KB, above-the-fold) via `wp_head` priorité 1, ET utilise le filter `style_loader_tag` pour transformer `youvanna-fonts`, `fa-*` et `youvanna-main` en `rel="preload" as="style" onload="this.rel='stylesheet'"`. Ne JAMAIS enlever ce filter — c'est ce qui passe Lighthouse mobile de 85 à 100.
35. **TOUJOURS réserver l'espace des icônes FA** - `.btn i, .btn svg { width: 1em; height: 1em; flex-shrink: 0 }` dans `critical.css` pour éviter le CLS quand la font FA charge. Sinon les boutons shiftent et le CLS dépasse 0.1.
36. **TOUJOURS `fetchpriority="high"` sur le hero LCP** - Le filter `wp_preload_resources` ajoute automatiquement `fetchpriority=high` aux images préchargées. Ne pas toucher. Le hero atteint un LCP < 2.0s grâce à ça.
37. **Header box-shadow via `::before` composited** - `#site-header::before { box-shadow: var(--shadow); opacity: 0; transition: opacity }`. JAMAIS mettre `transition: all` ou transitionner directement `box-shadow` sur le header — cause un repaint full-width à chaque scroll.
38. **WCAG AAA sur les couleurs** - `--color-primary-dark: #7A2210` (9.18:1 sur bg), `--color-text-light: #595959`. Les `.btn-outline` utilisent `--color-primary-dark`, PAS `--color-primary` (qui est trop clair pour AAA). Cookies plugin idem : `.yv-cb-link { color: var(--color-primary-dark); font-weight: 600 }`.
39. **Cookies plugin bundlé dans le thème** - `plugins/youvanna-cookies/` est copié automatiquement dans `wp-content/plugins/` par `post-clone-setup.php`. JAMAIS modifier la version serveur sans répercuter dans le thème.

---

## Feedbacks utilisateur (mémoire persistante)

### Accents français
TOUJOURS les accents. L'utilisateur a déjà corrigé des textes sans accents ("Pret" au lieu de "Prêt", "equipe" au lieu de "équipe"). Texte sans accents = site amateur = inacceptable. Si problème d'encodage SSH, utiliser scp + wp eval-file.

### Ne jamais inventer
JAMAIS de faux avis, faux chiffres ("500+ clients", "98% satisfaction"), fausses données. C'est du mensonge, pas professionnel et potentiellement illégal.
- **Témoignages** : Si pas de vrais avis -> Lorem Ipsum avec noms placeholder évidents.
- **Chiffres/stats** : Si pas de données vérifiées -> NE PAS mettre de section numbers.
- **Contenu texte** : Baser sur infos réelles. Si rien fourni, contenu générique mais honnête sans faits inventés.

### Images Gemini
- API Gemini (gemini-3-pro-image-preview) pour toutes les images placeholder
- Toujours 2K, toujours "no text, no words, no letters, no logos, no watermarks"
- Le ratio dépend du texte à côté, PAS du type de section
- Méthode : curl en bash, PAS un helper PHP côté serveur

### SCF pas ACF
L'utilisateur insiste : on utilise SCF (Secure Custom Fields), pas ACF. Toujours écrire SCF dans les commentaires, la doc, et les descriptions. L'API utilise le préfixe `acf_` pour compatibilité mais c'est bien SCF.

### Memory dans CLAUDE.md
L'utilisateur veut que TOUTES les règles et feedbacks soient aussi dans ce fichier CLAUDE.md (pas seulement dans la mémoire locale de Claude Code), pour que toute session future les retrouve automatiquement.

### GitHub sync
TOUJOURS push les modifs du starter vers GitHub après changement. Le webhook auto-pull met à jour demo.youvanna.com instantanément.

### Toujours continuer
TOUJOURS enchaîner les phases automatiquement. Ne jamais s'arrêter entre les phases. Si bloqué, demander explicitement.

### Telegram updates
TOUJOURS envoyer des updates Telegram quand une tâche est terminée. L'utilisateur ne voit PAS le transcript. AUCUNE EXCEPTION.

---

## Architecture

```
youvanna-starter/
|-- functions.php          # Helpers, SCF fields, admin UX, GTM/GA, Schema.org, OG meta
|-- header.php / footer.php
|-- front-page.php         # Homepage (hero, services, about, testimonials, CTA)
|-- page.php               # Pages intérieures (hero + flexible content)
|-- page-contact.php       # Contact (hero + CF7 + coordonnées)
|-- single.php / home.php / archive.php / search.php  # Blog + recherche
|-- index.php              # Fallback WP requis
|-- 404.php
|-- CLAUDE.md              # Ce fichier
|-- assets/css/main.css    # Design system - variables CSS, animations
|-- assets/js/main.js      # Menu, counter, stagger, parallax, FAQ smooth, back-to-top
|-- template-parts/        # 11 layouts flex content
    |-- section-text_image.php
    |-- section-cards.php
    |-- section-cta.php
    |-- section-testimonials.php (marquee horizontal)
    |-- section-faq.php
    |-- section-gallery.php
    |-- section-map.php
    |-- section-numbers.php (counter animation)
    |-- section-text.php (WYSIWYG pleine largeur - mentions légales, CGV, etc.)
    |-- section-video.php (YouTube/Vimeo/MP4/oEmbed responsive)
    |-- section-team.php (grille équipe avec photo/nom/rôle/bio)
```

## Helpers PHP

```php
yv_option($name, $fallback)              // wp_options avec préfixe yv_
yv_field($name, $fallback, $post_id)     // Champ SCF (gère 0 et valeurs falsy correctement)
yv_image($name, $size, $post_id)         // URL image SCF (retourne string URL)
yv_img($name, $size, $post_id, $attrs)   // Image SCF avec wp_get_attachment_image (width/height/srcset auto)
yv_image_id($name, $post_id)              // Attachment ID from SCF image field (int)
yv_render_hero($args)                     // Bandeau hero (<img fetchpriority="high"> + overlay)
yv_section_header($title, $sub, $badge)  // H2 + subtitle + badge pill. Pas de <mark>.
yv_render_card($args)                     // Carte (image_id > image > icon). link vérifie is_array()
yv_render_stats($rows, $class)           // Grille de chiffres (counter animation auto)
```

## Options globales (yv_)

| Clé | Usage |
|-----|-------|
| `yv_phone` | Header, footer, contact, schema.org |
| `yv_email` | Footer, contact, schema.org |
| `yv_address` | Footer, contact, schema.org |
| `yv_opening_hours` | Contact, schema.org (openingHours) |
| `yv_maps_embed_url` | Contact (iframe Google Maps) |
| `yv_city` | Schema.org addressLocality |
| `yv_postal_code` | Schema.org postalCode |
| `yv_latitude` | Schema.org GeoCoordinates latitude |
| `yv_longitude` | Schema.org GeoCoordinates longitude |
| `yv_business_type` | Schema.org @type (Dentist, Restaurant, LegalService... Défaut: LocalBusiness) |
| `yv_footer_description` | Footer |
| `yv_cta_text` / `yv_cta_link` | Bouton CTA header |
| `yv_social_facebook/instagram/linkedin/youtube/tiktok` | Footer icônes FA + schema.org sameAs |
| `yv_gtm_id` / `yv_ga_id` | Analytics (charge après consentement cookies) |

## Champs SCF par page

**Homepage** : hero_title, hero_subtitle, hero_cta1_text/link, hero_cta2_text/link, hero_image, services_title, services_subtitle, services (repeater: icon, title, description, image, link), about_title, about_text, about_image, about_button (type link), stats (repeater: number, label), testimonials_title, testimonials (repeater: text, name, role, rating, photo), cta_title, cta_text_home, cta_background, cta_button_text/link

**Pages intérieures** : page_hero_title, page_hero_subtitle, page_hero_image, sections (flexible content - 11 layouts)

**Page contact** : contact_form_title, contact_form_id, show_map

## Flexible Content layouts (11)

Dispatching : `page.php` fait `get_template_part('template-parts/section', get_row_layout())` - le layout `cards` charge `template-parts/section-cards.php`.

Tous les layouts (sauf cta, text_image, map, text, video) ont un champ `badge` optionnel.

### Sub-fields par layout

| Layout | Sub-fields | Types |
|--------|-----------|-------|
| `text_image` | title (text), text (wysiwyg), image (image), image_position (select: right/left), link (link) | |
| `cards` | title (text), subtitle (textarea), badge (text), columns (select: 2/3/4), cards (repeater) | cards[]: image (image), title (text), description (textarea), link (link) |
| `cta` | background (image), title (text), text (textarea), button (link) | |
| `testimonials` | title (text), badge (text), items (repeater) | items[]: text (textarea), name (text), role (text), photo (image), rating (number 1-5) |
| `faq` | title (text), badge (text), items (repeater) | items[]: question (text), answer (wysiwyg) |
| `gallery` | title (text), badge (text), columns (select: 2/3/4), images (gallery) | |
| `map` | title (text), map_url (url), height (number, défaut 450) | |
| `numbers` | title (text), badge (text), bg_color (select: light/primary/dark), items (repeater) | items[]: number (text), label (text) |
| `text` | title (text), content (wysiwyg full+media), narrow (true_false, défaut 1) | |
| `video` | title (text), video_url (url) | |
| `team` | title (text), subtitle (textarea), badge (text), columns (select: 3/4), members (repeater) | members[]: photo (image), name (text), role (text), bio (textarea) |

### Field-type cheat sheet pour update_field()

| Type SCF | Format update_field() | Exemple |
|----------|----------------------|---------|
| `text` | string | `'Mon titre'` |
| `textarea` | string | `'Description...'` |
| `wysiwyg` | string HTML | `'<p>Contenu</p>'` |
| `number` | int | `5` |
| `url` | string URL | `'https://...'` |
| `image` (return_format: array) | int (attachment ID) | `42` |
| `gallery` (return_format: array) | array of int (IDs) | `[42, 43, 44]` |
| `link` | array | `['url' => '/contact', 'title' => 'Contactez-nous', 'target' => '']` |
| `select` | string (value key) | `'3'` ou `'primary'` |
| `true_false` | int | `1` ou `0` |
| `repeater` | array of arrays | voir exemples ci-dessous |
| `flexible_content` | array of arrays avec `acf_fc_layout` | voir exemples ci-dessous |

### Link fields : text pair vs SCF link array

| Champ | Pattern | Format update_field() |
|-------|---------|----------------------|
| `hero_cta1_text` + `hero_cta1_link` | text pair | 2 appels : `update_field('hero_cta1_text', 'Texte')` + `update_field('hero_cta1_link', '/url')` |
| `cta_button_text` + `cta_button_link` | text pair | idem |
| `about_button` | SCF link | `update_field('about_button', ['url' => '/a-propos', 'title' => 'En savoir plus', 'target' => ''])` |
| services[].link | SCF link | dans le repeater |
| flex cards[].cards[].link | SCF link | dans le repeater imbriqué |
| flex cta.button | SCF link | `['url' => '...', 'title' => '...', 'target' => '']` |
| flex text_image.link | SCF link | idem |

### Exemples update_field()

```php
// Homepage hero
update_field('hero_title', 'Bienvenue chez Nom Client', $front_id);
update_field('hero_subtitle', 'Votre artisan à Lorient', $front_id);
update_field('hero_cta1_text', 'Nos services', $front_id);
update_field('hero_cta1_link', '/nos-services', $front_id);
update_field('hero_image', $hero_attachment_id, $front_id); // ID, pas URL !

// Homepage services (repeater)
update_field('services', [
    ['icon' => '', 'title' => 'Service 1', 'description' => 'Texte...', 'image' => $img_id, 'link' => ['url' => '/service-1', 'title' => 'En savoir plus', 'target' => '']],
    ['icon' => '', 'title' => 'Service 2', 'description' => 'Texte...', 'image' => $img_id2, 'link' => ['url' => '/service-2', 'title' => 'En savoir plus', 'target' => '']],
], $front_id);

// Page intérieure - flexible content
update_field('sections', [
    ['acf_fc_layout' => 'text_image', 'title' => 'Notre histoire', 'text' => '<p>Texte HTML</p>', 'image' => $img_id, 'image_position' => 'right', 'link' => ['url' => '/contact', 'title' => 'Contactez-nous', 'target' => '']],
    ['acf_fc_layout' => 'cards', 'title' => 'Nos services', 'subtitle' => '', 'badge' => 'Services', 'columns' => '3', 'cards' => [
        ['title' => 'Carte 1', 'description' => 'Texte', 'image' => $img_id, 'link' => ['url' => '/s1', 'title' => 'Voir', 'target' => '']],
    ]],
    ['acf_fc_layout' => 'faq', 'title' => 'Questions fréquentes', 'badge' => 'FAQ', 'items' => [
        ['question' => 'Question 1 ?', 'answer' => '<p>Réponse</p>'],
    ]],
    ['acf_fc_layout' => 'gallery', 'title' => 'Galerie', 'badge' => '', 'columns' => '3', 'images' => [$img_id1, $img_id2, $img_id3]],
    ['acf_fc_layout' => 'numbers', 'title' => 'En chiffres', 'badge' => '', 'bg_color' => 'primary', 'items' => [
        ['number' => '15+', 'label' => 'Années d\'expérience'],
    ]],
    ['acf_fc_layout' => 'testimonials', 'title' => 'Avis clients', 'badge' => '', 'items' => [
        ['text' => 'Super service', 'name' => 'Jean Dupont', 'role' => 'Client', 'photo' => $photo_id, 'rating' => 5],
    ]],
    ['acf_fc_layout' => 'team', 'title' => 'Notre équipe', 'subtitle' => '', 'badge' => 'Équipe', 'columns' => '3', 'members' => [
        ['photo' => $photo_id, 'name' => 'Jean', 'role' => 'Gérant', 'bio' => 'Bio courte'],
    ]],
    ['acf_fc_layout' => 'video', 'title' => 'Présentation', 'video_url' => 'https://www.youtube.com/watch?v=xxx'],
    ['acf_fc_layout' => 'text', 'title' => 'Mentions légales', 'content' => '<p>Contenu HTML...</p>', 'narrow' => 1],
    ['acf_fc_layout' => 'map', 'title' => 'Nous trouver', 'map_url' => 'https://www.google.com/maps/embed?pb=...', 'height' => 450],
    ['acf_fc_layout' => 'cta', 'title' => 'Prêt à démarrer ?', 'text' => 'Contactez-nous', 'background' => $bg_id, 'button' => ['url' => '/contact', 'title' => 'Contact', 'target' => '']],
], $page_id);
```

### CF7 form ID discovery

Après clonage du template, trouver l'ID du formulaire Contact Form 7 :
```bash
wp post list --post_type=wpcf7_contact_form --fields=ID,post_title --allow-root
```
Puis mettre à jour : `update_field('contact_form_id', $form_id, $contact_page_id);`

## CSS Variables (dans :root)

```css
--color-primary: #hex;        /* Couleur principale */
--color-primary-rgb: R, G, B; /* MÊME couleur en RGB - TOUJOURS sync avec primary ! */
--color-primary-dark: #hex;   /* Hover */
--color-accent: #hex;         /* Accent secondaire */
--color-secondary: #hex;      /* Footer, hero fallback */
--font-heading / --font-body  /* System font stack par défaut, custom via @font-face */
```

## Dépendances

- Font Awesome 6.5 subsets (fontawesome + solid + brands, CDN async via preload+onload, ~50KB vs 300KB)
- SCF (Secure Custom Fields - plugin officiel WordPress, fork gratuit d'ACF avec Repeater + Flex Content intégrés)
- Contact Form 7
- Yoast SEO (gère OG meta quand actif, sinon fallback auto dans functions.php)
- Youvanna Languages (plugin inclus dans plugins/youvanna-languages/ - multilingue i18n)
- Wordfence (sécurité - auto-config via `post-clone-setup.php`)
- Redis Object Cache (cache objet persistant)
- WP Super Cache (cache de page statique)
- Plugin youvanna-cookies (bandeau RGPD auto)

## Performance

- Cache busting automatique via `filemtime()`
- Font Awesome subsets async (fontawesome + solid + brands, preload + onload swap, ~50KB) + inline `font-display: swap` override
- Google Fonts TOUJOURS avec `&display=swap` dans l'URL
- WebP conversion automatique à l'upload (image_editor_output_format filter)
- Compression images 82% (wp_editor_set_quality filter)
- Big images cappées à 2560px (big_image_size_threshold filter)
- Cache headers statiques : configurés via Nginx (1 an, immutable)
- System font stack par défaut (0 requête font). Custom via @font-face + variables CSS
- Hero LCP : vrai `<img class="hero-bg-img" fetchpriority="high">` avec `wp_get_attachment_image()` (srcset/sizes auto, pas de background-image)
- CTA banners : même pattern `<img class="hero-bg-img">` (lazy loaded)
- Preconnect cdnjs + GTM/GA
- `wp_get_attachment_image()` partout (width/height/srcset/sizes automatiques, 0 CLS)
- Lazy loading sur toutes les images below-fold + photos témoignages
- `<noscript>` fallback pour animations `.reveal` (cards, faq, stats, testimonials, team)
- `prefers-reduced-motion` respecté : parallax, counter, marquee, stagger
- Bloat WP supprimé (block CSS, emoji, oEmbed, REST link, jquery-migrate)
- `scroll-padding-top` pour offset header fixe sur ancres
- Nav link underline animation CSS
- Media queries consolidés (1 bloc 960px, 1 bloc 768px)
- Scroll handlers consolidés (1 seul rAF pour header, parallax, back-to-top)
- Body scroll lock sur mobile menu (overflow-hidden)
- Mobile nav max-height + overflow-y auto
- Focus-visible WCAG 2.1 AA sur tous les éléments interactifs
- Gallery hover overlay avec icône FA search

## SEO

- `<meta name="description">` + `<link rel="canonical">` automatiques (skip si Yoast/RankMath)
- `<meta name="robots" content="noindex, follow">` sur 404 et search
- Open Graph + Twitter Card + `og:locale` automatiques (fallback si Yoast/RankMath absent)
- OG tags gère correctement archives, catégories, search (pas seulement singular)
- WebSite schema avec SearchAction
- LocalBusiness schema complet (address + addressLocality + postalCode + geo GeoCoordinates + openingHours, sameAs, image, logo)
- BlogPosting avec wordCount, mainEntityOfPage, image fallback logo
- FAQPage schema automatique sur pages avec section FAQ
- BreadcrumbList sur toutes les pages sauf homepage
- Gallery items avec aria-label
- aria-current="page" sur les liens nav actifs
- Testimonial stars avec aria-label + role="img"

## Multilingue (Youvanna Languages)

Plugin inclus dans `plugins/youvanna-languages/`. Après activation :
- Admin : Langues -> Gérer les langues (ajouter/supprimer)
- Admin : Langues -> Traduire (page complète avec tous les champs par langue)
- Admin : Langues -> Exporter/Importer (JSON)
- URLs : langue par défaut = pas de préfixe, secondaires = `/en/`, `/de/`, etc.
- hreflang automatique, sitemap par langue, Yoast compatible
- Traduit : titres, contenus, extraits, slugs, champs SCF, menus, options yv_*, Yoast meta
- Sélecteur de langue flottant ou shortcode `[yvl_switcher]`
- JSON export/import pour traduction via IA (skill)

### Installation sur un nouveau site :
```bash
cp -r wp-content/themes/youvanna-starter/plugins/youvanna-languages wp-content/plugins/
wp plugin activate youvanna-languages --allow-root
wp rewrite flush --allow-root
```

## Post-clone setup (sécurité + cache + plugins)

Script unique qui configure TOUT après le clonage : `post-clone-setup.php`
```bash
wp eval-file wp-content/themes/youvanna-starter/post-clone-setup.php --allow-root
```
Configure automatiquement :
- Youvanna Languages : copie + activation
- Redis Object Cache : install + activation + enable (si Redis dispo sur le serveur)
- WP Super Cache : install + activation + enable
- Wordfence : install + WAF bootstrap + Plesk auto_prepend_file + firewall + brute force + scanner + login security + XMLRPC
- Wordfence Central : déconnexion (sites clonés)
100% automatique, aucune étape manuelle.

## Infos serveur

- **SSH** : root@82.29.173.183 (credentials dans la mémoire locale Claude Code)
- **Démo** : /var/www/vhosts/demo.youvanna.com/httpdocs
- **PHP** : export PATH=/opt/plesk/php/8.3/bin:$PATH
- **WP-CLI** : wp --allow-root
- **GitHub** : agenceyouvanna/youvanna-starter
- **Gemini** : credentials dans la mémoire locale Claude Code
- **Note** : Les credentials (PAT, API keys, mots de passe) ne sont JAMAIS dans le git. Ils sont dans les fichiers mémoire de Claude Code uniquement.

## Backup .claude/

Le répertoire `.claude/` dans ce repo contient :
- `skills/youvanna-new-site/SKILL.md` - Pipeline 9 agents pour créer un nouveau site client
- `memory/` - Fichiers mémoire non-sensibles (feedbacks, règles, contexte projet)
- Les fichiers contenant des credentials (API keys, PAT, mots de passe) sont exclus du repo et ne sont que sur le serveur dans `/root/.claude/projects/`.
