# Youvanna Starter v2.6.0 - Guide Claude Code

Ce fichier est lu automatiquement par Claude Code. Il contient toutes les regles, conventions, feedbacks et contexte pour travailler sur les sites Youvanna.

---

## Regles absolues

1. **JAMAIS de couleur hardcodee** - Toute couleur passe par les CSS variables `:root` dans `main.css`. NULLE PART ailleurs.
2. **JAMAIS de code duplique** - Utilise les helpers PHP existants (yv_field, yv_option, yv_image, yv_img, yv_render_card, etc.)
3. **JAMAIS hardcoder une info client** - Tout passe par `wp option update yv_xxx` ou `update_field()`
4. **TOUJOURS les accents francais** - e, e, e, e, a, a, u, u, o, c, i. JAMAIS "equipe" -> "equipe", JAMAIS "Pret" -> "Pret". Si SSH pose des problemes d'encodage, utiliser `scp` + `wp eval-file` (preserve l'UTF-8).
5. **JAMAIS inventer ce qu'on ne sait pas** - Pas de faux avis, pas de faux chiffres, pas de fausses donnees. Lorem Ipsum pour les temoignages si pas de vrais avis. Pas de section numbers si pas de chiffres verifies. Les descriptions de services doivent etre basees sur des infos reelles (site existant, brief client). Regle numero 1 de l'utilisateur.
6. **JAMAIS de tirets speciaux** - Utiliser `-` uniquement. Pas de em-dash, en-dash, smart quotes.
7. **TOUJOURS des images Gemini sur les cartes** - Pas d'icones quand on peut mettre une image. Icones FA en fallback uniquement si 6+ cartes.
8. **JAMAIS de texte sur les images Gemini** - Toujours inclure "no text, no words, no letters, no logos, no watermarks" dans les prompts.
9. **Ratio d'image = reflechir au contenu** - Le ratio depend de la quantite de texte a cote. Rediger le contenu AVANT de generer les images. Texte court -> 4:3, texte moyen -> 1:1 ou 4:5, texte long -> 3:4 portrait. Heroes toujours 16:9.
10. **Schema.org = automatique** - LocalBusiness, WebSite (avec SearchAction), BreadcrumbList, FAQPage, BlogPosting. JAMAIS ecrire de schema manuellement.
11. **Animations = automatiques** - Counter (skip prefers-reduced-motion), stagger (cards, faq, stats, testimonials, team), parallax, marquee (will-change: transform), FAQ smooth, back-to-top. Ne rien ajouter.
12. **TOUJOURS des FAQ pour le SEO** - Ajouter une section FAQ sur chaque page pertinente.
13. **Section headers style Framer** - `<mark>` pour highlight, badge pill en 3e argument de `yv_section_header()`. Badge disponible sur tous les layouts flex.
14. **TOUJOURS sync --color-primary-rgb** - Quand on change `--color-primary`, mettre a jour `--color-primary-rgb` avec les valeurs R, G, B.
15. **OG Meta = automatique** - Open Graph + Twitter Card generes automatiquement si Yoast/RankMath n'est pas actif.
16. **TOUJOURS utiliser wp_get_attachment_image()** - Pour les images SCF, utiliser `yv_img()` ou `wp_get_attachment_image($img['ID'])` au lieu de `<img src>` brut. Donne width/height/srcset/sizes automatiquement.
17. **SCF, pas ACF** - On utilise SCF (Secure Custom Fields), le plugin officiel WordPress. C'est un fork gratuit d'ACF avec Repeater + Flex Content integres. L'API garde le prefixe `acf_` pour compatibilite, mais dans les commentaires et la doc on ecrit SCF.
18. **TOUJOURS font-display: swap sur TOUTES les fonts** - Le theme override FA CDN (`font-display: block`) avec un inline `font-display: swap` dans `wp_head`. Google Fonts DOIT toujours inclure `&display=swap` dans l'URL. JAMAIS charger une font externe sans `font-display: swap` — ca bloque le FCP et casse les scores PageSpeed. Si un agent ou un dev ajoute une font sans swap, c'est un bug critique a corriger immediatement.
19. **ACCENTS FRANÇAIS = NON NEGOCIABLE** - TOUS les textes visibles (titres, descriptions, meta titles Yoast, meta descriptions, alt texts, noms de categories, FAQ, boutons, labels menu) DOIVENT avoir les accents corrects : e, e, e, e, a, a, u, u, o, c, i, oe, ae. Ca inclut les rapports d'agents et le JSON SEO. "A propos" = FAUX, "A propos" = CORRECT. "Securite" = FAUX, "Securite" = CORRECT. Verifier CHAQUE output d'agent.
20. **NE JAMAIS INVENTER = LOI ABSOLUE** - Aucun agent ne doit inventer de donnees : pas de faux avis, pas de faux chiffres, pas de fausses certifications, pas de faux partenariats. Les temoignages = Lorem Ipsum avec noms placeholder. Les stats = uniquement si verifiees. Les descriptions de services = basees sur le brief client. Toute invention detectee = bug critique a corriger immediatement.
21. **JAMAIS de `<style>` ou `<script>` dans `post_content`** - `wp_kses_post` strippe les tags quand on ecrit via CLI/PHP (pas de capability `unfiltered_html`) ET `wptexturize` bousille les `var(--color-*)` en `var(&#8211;color-*)` et les `""` en guillemets francais. Le CSS va TOUJOURS dans `assets/css/main.css`, le JS dans `assets/js/main.js`. Le post_content doit etre du HTML pur. Si une migration importe du HTML contenant des `<style>`, les deplacer dans main.css AVANT d'inserer en base.
22. **JAMAIS `esc_html()` sur du contenu riche** - Si un champ SCF peut contenir du HTML (wysiwyg, textarea multi-ligne, description, contenu flexible), utiliser `wp_kses_post()`. `esc_html()` rend `<p>` et `<strong>` en texte litteral visible. Reserver `esc_html()` aux titres, labels, attributs, URLs — JAMAIS pour `$card['text']`, `$testimonial['text']`, `cta_text`, `description`, etc. Bug historique : `yv_render_card` utilisait `esc_html($a['text'])` et tout s'affichait en brut sur les pages tarifs/adhesion. Corrige en `wp_kses_post(wpautop($a['text']))`.
23. **Header full-width** - La `.nav-container` doit utiliser `--max-width-wide` (1440px) pas `--max-width` (1320px), sinon le logo se retrouve loin du bord gauche sur ecrans larges et le header fait resserre. Idem pour les layouts avec sidebar : la `.page-with-sidebar > .container` doit utiliser `--max-width-wide` pour donner de la place au contenu principal.

---

## Feedbacks utilisateur (memoire persistante)

### Accents francais
TOUJOURS les accents. L'utilisateur a deja corrige des textes sans accents ("Pret" au lieu de "Pret", "equipe" au lieu de "equipe"). Texte sans accents = site amateur = inacceptable. Si probleme d'encodage SSH, utiliser scp + wp eval-file.

### Ne jamais inventer
JAMAIS de faux avis, faux chiffres ("500+ clients", "98% satisfaction"), fausses donnees. C'est du mensonge, pas professionnel et potentiellement illegal.
- **Temoignages** : Si pas de vrais avis -> Lorem Ipsum avec noms placeholder evidents.
- **Chiffres/stats** : Si pas de donnees verifiees -> NE PAS mettre de section numbers.
- **Contenu texte** : Baser sur infos reelles. Si rien fourni, contenu generique mais honnete sans faits inventes.

### Images Gemini
- API Gemini (gemini-3-pro-image-preview) pour toutes les images placeholder
- Toujours 2K, toujours "no text, no words, no letters, no logos, no watermarks"
- Le ratio depend du texte a cote, PAS du type de section
- Methode : curl en bash, PAS un helper PHP cote serveur

### SCF pas ACF
L'utilisateur insiste : on utilise SCF (Secure Custom Fields), pas ACF. Toujours ecrire SCF dans les commentaires, la doc, et les descriptions. L'API utilise le prefixe `acf_` pour compatibilite mais c'est bien SCF.

### Memory dans CLAUDE.md
L'utilisateur veut que TOUTES les regles et feedbacks soient aussi dans ce fichier CLAUDE.md (pas seulement dans la memoire locale de Claude Code), pour que toute session future les retrouve automatiquement.

---

## Architecture

```
youvanna-starter/
|-- functions.php          # Helpers, SCF fields, admin UX, GTM/GA, Schema.org, OG meta
|-- header.php / footer.php
|-- front-page.php         # Homepage (hero, services, about, testimonials, CTA)
|-- page.php               # Pages interieures (hero + flexible content)
|-- page-contact.php       # Contact (hero + CF7 + coordonnees)
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
    |-- section-text.php (WYSIWYG pleine largeur - mentions legales, CGV, etc.)
    |-- section-video.php (YouTube/Vimeo/MP4/oEmbed responsive)
    |-- section-team.php (grille equipe avec photo/nom/role/bio)
```

## Helpers PHP

```php
yv_option($name, $fallback)              // wp_options avec prefixe yv_
yv_field($name, $fallback, $post_id)     // Champ SCF (gere 0 et valeurs falsy correctement)
yv_image($name, $size, $post_id)         // URL image SCF (retourne string URL)
yv_img($name, $size, $post_id, $attrs)   // Image SCF avec wp_get_attachment_image (width/height/srcset auto)
yv_image_id($name, $post_id)              // Attachment ID from SCF image field (int)
yv_render_hero($args)                     // Bandeau hero (<img fetchpriority="high"> + overlay)
yv_section_header($title, $sub, $badge)  // H2 + subtitle + badge pill. $title supporte <mark>
yv_render_card($args)                     // Carte (image_id > image > icon). link verifie is_array()
yv_render_stats($rows, $class)           // Grille de chiffres (counter animation auto)
```

## Options globales (yv_)

| Cle | Usage |
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
| `yv_business_type` | Schema.org @type (Dentist, Restaurant, LegalService... Defaut: LocalBusiness) |
| `yv_footer_description` | Footer |
| `yv_cta_text` / `yv_cta_link` | Bouton CTA header |
| `yv_social_facebook/instagram/linkedin/youtube/tiktok` | Footer icones FA + schema.org sameAs |
| `yv_gtm_id` / `yv_ga_id` | Analytics (charge apres consentement cookies) |

## Champs SCF par page

**Homepage** : hero_title, hero_subtitle, hero_cta1_text/link, hero_cta2_text/link, hero_image, services_title, services_subtitle, services (repeater: icon, title, description, image, link), about_title, about_text, about_image, about_button (type link), stats (repeater: number, label), testimonials_title, testimonials (repeater: text, name, role, rating, photo), cta_title, cta_text_home, cta_background, cta_button_text/link

**Pages interieures** : page_hero_title, page_hero_subtitle, page_hero_image, sections (flexible content - 11 layouts)

**Page contact** : contact_form_title, contact_form_id, show_map

## Flexible Content layouts (11)

Dispatching : `page.php` fait `get_template_part('template-parts/section', get_row_layout())` — le layout `cards` charge `template-parts/section-cards.php`.

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
| `map` | title (text), map_url (url), height (number, defaut 450) | |
| `numbers` | title (text), badge (text), bg_color (select: light/primary/dark), items (repeater) | items[]: number (text), label (text) |
| `text` | title (text), content (wysiwyg full+media), narrow (true_false, defaut 1) | |
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
| flex cards[].cards[].link | SCF link | dans le repeater imbrique |
| flex cta.button | SCF link | `['url' => '...', 'title' => '...', 'target' => '']` |
| flex text_image.link | SCF link | idem |

### Exemples update_field()

```php
// Homepage hero
update_field('hero_title', 'Bienvenue chez Nom Client', $front_id);
update_field('hero_subtitle', 'Votre artisan a Lorient', $front_id);
update_field('hero_cta1_text', 'Nos services', $front_id);
update_field('hero_cta1_link', '/nos-services', $front_id);
update_field('hero_image', $hero_attachment_id, $front_id); // ID, pas URL !

// Homepage services (repeater)
update_field('services', [
    ['icon' => '', 'title' => 'Service 1', 'description' => 'Texte...', 'image' => $img_id, 'link' => ['url' => '/service-1', 'title' => 'En savoir plus', 'target' => '']],
    ['icon' => '', 'title' => 'Service 2', 'description' => 'Texte...', 'image' => $img_id2, 'link' => ['url' => '/service-2', 'title' => 'En savoir plus', 'target' => '']],
], $front_id);

// Page interieure - flexible content
update_field('sections', [
    ['acf_fc_layout' => 'text_image', 'title' => 'Notre histoire', 'text' => '<p>Texte HTML</p>', 'image' => $img_id, 'image_position' => 'right', 'link' => ['url' => '/contact', 'title' => 'Contactez-nous', 'target' => '']],
    ['acf_fc_layout' => 'cards', 'title' => 'Nos services', 'subtitle' => '', 'badge' => 'Services', 'columns' => '3', 'cards' => [
        ['title' => 'Carte 1', 'description' => 'Texte', 'image' => $img_id, 'link' => ['url' => '/s1', 'title' => 'Voir', 'target' => '']],
    ]],
    ['acf_fc_layout' => 'faq', 'title' => 'Questions frequentes', 'badge' => 'FAQ', 'items' => [
        ['question' => 'Question 1 ?', 'answer' => '<p>Reponse</p>'],
    ]],
    ['acf_fc_layout' => 'gallery', 'title' => 'Galerie', 'badge' => '', 'columns' => '3', 'images' => [$img_id1, $img_id2, $img_id3]],
    ['acf_fc_layout' => 'numbers', 'title' => 'En chiffres', 'badge' => '', 'bg_color' => 'primary', 'items' => [
        ['number' => '15+', 'label' => 'Annees d\'experience'],
    ]],
    ['acf_fc_layout' => 'testimonials', 'title' => 'Avis clients', 'badge' => '', 'items' => [
        ['text' => 'Super service', 'name' => 'Jean Dupont', 'role' => 'Client', 'photo' => $photo_id, 'rating' => 5],
    ]],
    ['acf_fc_layout' => 'team', 'title' => 'Notre equipe', 'subtitle' => '', 'badge' => 'Equipe', 'columns' => '3', 'members' => [
        ['photo' => $photo_id, 'name' => 'Jean', 'role' => 'Gerant', 'bio' => 'Bio courte'],
    ]],
    ['acf_fc_layout' => 'video', 'title' => 'Presentation', 'video_url' => 'https://www.youtube.com/watch?v=xxx'],
    ['acf_fc_layout' => 'text', 'title' => 'Mentions legales', 'content' => '<p>Contenu HTML...</p>', 'narrow' => 1],
    ['acf_fc_layout' => 'map', 'title' => 'Nous trouver', 'map_url' => 'https://www.google.com/maps/embed?pb=...', 'height' => 450],
    ['acf_fc_layout' => 'cta', 'title' => 'Pret a demarrer ?', 'text' => 'Contactez-nous', 'background' => $bg_id, 'button' => ['url' => '/contact', 'title' => 'Contact', 'target' => '']],
], $page_id);
```

### CF7 form ID discovery

Apres clonage du template, trouver l'ID du formulaire Contact Form 7 :
```bash
wp post list --post_type=wpcf7_contact_form --fields=ID,post_title --allow-root
```
Puis mettre a jour : `update_field('contact_form_id', $form_id, $contact_page_id);`

## CSS Variables (dans :root)

```css
--color-primary: #hex;        /* Couleur principale */
--color-primary-rgb: R, G, B; /* MEME couleur en RGB - TOUJOURS sync avec primary ! */
--color-primary-dark: #hex;   /* Hover */
--color-accent: #hex;         /* Accent secondaire */
--color-secondary: #hex;      /* Footer, hero fallback */
--font-heading / --font-body  /* System font stack par defaut, custom via @font-face */
```

## Dependances

- Font Awesome 6.5 subsets (fontawesome + solid + brands, CDN async via preload+onload, ~50KB vs 300KB)
- SCF (Secure Custom Fields - plugin officiel WordPress, fork gratuit d'ACF avec Repeater + Flex Content integres)
- Contact Form 7
- Yoast SEO (gere OG meta quand actif, sinon fallback auto dans functions.php)
- Youvanna Languages (plugin inclus dans plugins/youvanna-languages/ — multilingue i18n)
- Wordfence (securite — auto-config via `post-clone-setup.php`)
- Redis Object Cache (cache objet persistant)
- WP Super Cache (cache de page statique)
- Plugin youvanna-cookies (bandeau RGPD auto)

## Performance

- Cache busting automatique via `filemtime()`
- Font Awesome subsets async (fontawesome + solid + brands, preload + onload swap, ~50KB) + inline `font-display: swap` override (FA CDN uses `block` by default)
- Google Fonts TOUJOURS avec `&display=swap` dans l'URL
- WebP conversion automatique a l'upload (image_editor_output_format filter)
- Compression images 82% (wp_editor_set_quality filter)
- Big images cappees a 2560px (big_image_size_threshold filter)
- Cache headers statiques : configures via Nginx (1 an, immutable) — voir SKILL etape 0b2
- System font stack par defaut (0 requete font). Custom via @font-face + variables CSS
- Hero LCP : vrai `<img class="hero-bg-img" fetchpriority="high">` avec `wp_get_attachment_image()` (srcset/sizes auto, pas de background-image)
- CTA banners : meme pattern `<img class="hero-bg-img">` (lazy loaded)
- Preconnect cdnjs + GTM/GA
- `wp_get_attachment_image()` partout (width/height/srcset/sizes automatiques, 0 CLS)
- Lazy loading sur toutes les images below-fold + photos temoignages
- `<noscript>` fallback pour animations `.reveal` (cards, faq, stats, testimonials, team)
- `prefers-reduced-motion` respecte : parallax, counter, marquee, stagger
- Bloat WP supprime (block CSS, emoji, oEmbed, REST link, jquery-migrate)
- `scroll-padding-top` pour offset header fixe sur ancres
- Nav link underline animation CSS
- Media queries consolides (1 bloc 960px, 1 bloc 768px)
- Scroll handlers consolides (1 seul rAF pour header, parallax, back-to-top)
- Body scroll lock sur mobile menu (overflow-hidden)
- Mobile nav max-height + overflow-y auto
- Focus-visible WCAG 2.1 AA sur tous les elements interactifs
- Gallery hover overlay avec icone FA search

## SEO

- `<meta name="description">` + `<link rel="canonical">` automatiques (skip si Yoast/RankMath)
- `<meta name="robots" content="noindex, follow">` sur 404 et search
- Open Graph + Twitter Card + `og:locale` automatiques (fallback si Yoast/RankMath absent)
- OG tags gere correctement archives, categories, search (pas seulement singular)
- WebSite schema avec SearchAction
- LocalBusiness schema complet (address + addressLocality + postalCode + geo GeoCoordinates + openingHours, sameAs, image, logo)
- BlogPosting avec wordCount, mainEntityOfPage, image fallback logo
- FAQPage schema automatique sur pages avec section FAQ
- BreadcrumbList sur toutes les pages sauf homepage
- Gallery items avec aria-label
- aria-current="page" sur les liens nav actifs
- Testimonial stars avec aria-label + role="img"

## Multilingue (Youvanna Languages)

Plugin inclus dans `plugins/youvanna-languages/`. Apres activation :
- Admin : Langues → Gerer les langues (ajouter/supprimer)
- Admin : Langues → Traduire (page complete avec tous les champs par langue)
- Admin : Langues → Exporter/Importer (JSON)
- URLs : langue par defaut = pas de prefixe, secondaires = `/en/`, `/de/`, etc.
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

## Post-clone setup (securite + cache + plugins)

Script unique qui configure TOUT apres le clonage : `post-clone-setup.php`
```bash
wp eval-file wp-content/themes/youvanna-starter/post-clone-setup.php --allow-root
```
Configure automatiquement :
- Youvanna Languages : copie + activation
- Redis Object Cache : install + activation + enable (si Redis dispo sur le serveur)
- WP Super Cache : install + activation + enable
- Wordfence : install + WAF bootstrap + Plesk auto_prepend_file + firewall + brute force + scanner + login security + XMLRPC
- Wordfence Central : deconnexion (sites clones)
100% automatique, aucune etape manuelle.

## Infos serveur

- **SSH** : root@82.29.173.183 (credentials dans la memoire locale Claude Code)
- **Demo** : /var/www/vhosts/demo.youvanna.com/httpdocs
- **PHP** : export PATH=/opt/plesk/php/8.3/bin:$PATH
- **WP-CLI** : wp --allow-root
- **GitHub** : agenceyouvanna/youvanna-starter
- **Gemini** : credentials dans la memoire locale Claude Code
- **Note** : Les credentials (PAT, API keys, mots de passe) ne sont JAMAIS dans le git. Ils sont dans les fichiers memoire de Claude Code uniquement.
