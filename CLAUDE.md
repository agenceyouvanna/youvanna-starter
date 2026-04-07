# Youvanna Starter v2.4.0 - Guide Claude Code

Ce fichier est lu automatiquement par Claude Code. Il contient toutes les regles, conventions, feedbacks et contexte pour travailler sur les sites Youvanna.

---

## Regles absolues

1. **JAMAIS de couleur hardcodee** - Toute couleur passe par les CSS variables `:root` dans `main.css`. NULLE PART ailleurs.
2. **JAMAIS de code duplique** - Utilise les helpers PHP existants (yv_field, yv_option, yv_image, yv_render_card, etc.)
3. **JAMAIS hardcoder une info client** - Tout passe par `wp option update yv_xxx` ou `update_field()`
4. **TOUJOURS les accents francais** - e, e, e, e, a, a, u, u, o, c, i. JAMAIS "equipe" -> "equipe", JAMAIS "Pret" -> "Pret". Si SSH pose des problemes d'encodage, utiliser `scp` + `wp eval-file` (preserve l'UTF-8).
5. **JAMAIS inventer ce qu'on ne sait pas** - Pas de faux avis, pas de faux chiffres, pas de fausses donnees. Lorem Ipsum pour les temoignages si pas de vrais avis. Pas de section numbers si pas de chiffres verifies. Les descriptions de services doivent etre basees sur des infos reelles (site existant, brief client). Regle numero 1 de l'utilisateur.
6. **JAMAIS de tirets speciaux** - Utiliser `-` uniquement. Pas de em-dash, en-dash, smart quotes.
7. **TOUJOURS des images Gemini sur les cartes** - Pas d'icones quand on peut mettre une image. Icones FA en fallback uniquement si 6+ cartes.
8. **JAMAIS de texte sur les images Gemini** - Toujours inclure "no text, no words, no letters, no logos, no watermarks" dans les prompts.
9. **Ratio d'image = reflechir au contenu** - Le ratio depend de la quantite de texte a cote. Rediger le contenu AVANT de generer les images. Texte court -> 4:3, texte moyen -> 1:1 ou 4:5, texte long -> 3:4 portrait. Heroes toujours 16:9.
10. **Schema.org = automatique** - LocalBusiness, WebSite (avec SearchAction), BreadcrumbList, FAQPage, BlogPosting. JAMAIS ecrire de schema manuellement.
11. **Animations = automatiques** - Counter, stagger (cards, faq, stats, testimonials, team), parallax, marquee, FAQ smooth, back-to-top. Ne rien ajouter.
12. **TOUJOURS des FAQ pour le SEO** - Ajouter une section FAQ sur chaque page pertinente.
13. **Section headers style Framer** - `<mark>` pour highlight, badge pill en 3e argument de `yv_section_header()`.
14. **TOUJOURS sync --color-primary-rgb** - Quand on change `--color-primary`, mettre a jour `--color-primary-rgb` avec les valeurs R, G, B.
15. **OG Meta = automatique** - Open Graph + Twitter Card generes automatiquement si Yoast/RankMath n'est pas actif.

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
yv_image($name, $size, $post_id)         // URL image SCF
yv_render_hero($args)                     // Bandeau hero (background-image + preload auto)
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

`text_image`, `cards`, `cta`, `testimonials`, `faq`, `gallery`, `map`, `numbers`, `text`, `video`, `team`

**Note link fields** : Les CTA homepage utilisent des champs text separees (hero_cta1_text + hero_cta1_link). Les boutons flex content (section-cta, section-text_image) et about_button utilisent le type `link` ACF qui retourne `['url' => '...', 'title' => '...', 'target' => '']`.

## CSS Variables (dans :root)

```css
--color-primary: #hex;        /* Couleur principale */
--color-primary-rgb: R, G, B; /* MEME couleur en RGB - TOUJOURS sync avec primary ! */
--color-primary-dark: #hex;   /* Hover */
--color-accent: #hex;         /* Accent secondaire */
--font-heading / --font-body  /* Polices */
```

## Dependances

- Font Awesome 6.5 (CDN, charge en async non-bloquant via preload+onload)
- SCF (Secure Custom Fields - fork gratuit d'ACF avec Repeater + Flex Content)
- Contact Form 7
- Yoast SEO (gere OG meta quand actif, sinon fallback auto dans functions.php)
- Plugin youvanna-cookies (bandeau RGPD auto)

## Performance

- Cache busting automatique via `filemtime()`
- Font Awesome async (preload + onload swap, non-render-blocking)
- Hero image preload avec `fetchpriority="high"` (homepage, pages, blog posts, blog listing)
- Preconnect cdnjs + GTM/GA
- Lazy loading sur toutes les images below-fold + photos temoignages
- `<noscript>` fallback pour animations `.reveal` (cards, faq, stats, testimonials, team)
- Bloat WP supprime (block CSS, emoji, oEmbed, REST link, jquery-migrate)
- `scroll-padding-top` pour offset header fixe sur ancres
- Nav link underline animation CSS
- Team members hover + stagger reveal animation

## SEO

- Open Graph + Twitter Card meta automatiques (fallback si Yoast/RankMath absent)
- WebSite schema avec SearchAction
- LocalBusiness schema complet (address avec addressCountry FR, openingHours, sameAs, image, logo)
- BlogPosting avec wordCount, mainEntityOfPage, image fallback logo
- FAQPage schema automatique sur pages avec section FAQ
- BreadcrumbList sur toutes les pages sauf homepage

## Infos serveur

- **SSH** : root@82.29.173.183 (credentials dans la memoire locale Claude Code)
- **Demo** : /var/www/vhosts/demo.youvanna.com/httpdocs
- **PHP** : export PATH=/opt/plesk/php/8.3/bin:$PATH
- **WP-CLI** : wp --allow-root
- **GitHub** : agenceyouvanna/youvanna-starter
- **Gemini** : credentials dans la memoire locale Claude Code
- **Note** : Les credentials (PAT, API keys, mots de passe) ne sont JAMAIS dans le git. Ils sont dans les fichiers memoire de Claude Code uniquement.
