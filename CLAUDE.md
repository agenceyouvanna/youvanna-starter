# Youvanna Starter - Guide Claude Code

Ce fichier est lu automatiquement par Claude Code. Il contient toutes les regles, conventions et feedbacks pour travailler sur les sites Youvanna.

---

## Regles absolues

1. **JAMAIS de couleur hardcodee** - Toute couleur passe par les CSS variables `:root` dans `main.css`. NULLE PART ailleurs.
2. **JAMAIS de code duplique** - Utilise les helpers PHP existants (yv_field, yv_option, yv_image, yv_render_card, etc.)
3. **JAMAIS hardcoder une info client** - Tout passe par `wp option update yv_xxx` ou `update_field()`
4. **TOUJOURS les accents francais** - Si SSH pose des problemes d'encodage, utiliser `scp` + `wp eval-file`.
5. **JAMAIS inventer ce qu'on ne sait pas** - Pas de faux avis, pas de faux chiffres. Lorem Ipsum pour les temoignages. Pas de section numbers si pas de chiffres verifies.
6. **JAMAIS de tirets speciaux** - Utiliser `-` uniquement. Pas de em-dash, en-dash, smart quotes.
7. **TOUJOURS des images Gemini sur les cartes** - Pas d'icones quand on peut mettre une image. Icones FA en fallback uniquement si 6+ cartes.
8. **JAMAIS de texte sur les images Gemini** - Toujours inclure "no text, no words, no letters, no logos, no watermarks" dans les prompts.
9. **Ratio d'image = reflechir au contenu** - Le ratio depend de la quantite de texte a cote. Rediger le contenu AVANT de generer les images.
10. **Schema.org = automatique** - LocalBusiness, WebSite, BreadcrumbList, FAQPage, BlogPosting. JAMAIS ecrire de schema manuellement.
11. **Animations = automatiques** - Counter, stagger, parallax, marquee, FAQ smooth, back-to-top sont dans le theme.
12. **TOUJOURS des FAQ pour le SEO** - Ajouter une section FAQ sur chaque page pertinente.
13. **Section headers style Framer** - `<mark>` pour highlight, badge pill en 3e argument de `yv_section_header()`.
14. **TOUJOURS sync --color-primary-rgb** - Quand on change `--color-primary`, toujours mettre a jour `--color-primary-rgb` avec les valeurs R, G, B correspondantes.

---

## Architecture

```
youvanna-starter/
|-- functions.php          # Helpers, SCF fields, admin UX, GTM/GA, Schema.org
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
    |-- section-text.php (WYSIWYG pleine largeur)
    |-- section-video.php (YouTube/Vimeo embed)
    |-- section-team.php (grille equipe)
```

## Helpers PHP

```php
yv_option($name, $fallback)              // wp_options avec prefixe yv_
yv_field($name, $fallback, $post_id)     // Champ SCF (gere 0 et valeurs falsy)
yv_image($name, $size, $post_id)         // URL image SCF
yv_render_hero($args)                     // Bandeau hero (background-image)
yv_section_header($title, $sub, $badge)  // H2 + subtitle + badge pill. $title supporte <mark>
yv_render_card($args)                     // Carte (image_id > image > icon)
yv_render_stats($rows, $class)           // Grille de chiffres (counter animation auto)
```

## Options globales (yv_)

| Cle | Usage |
|-----|-------|
| `yv_phone` | Header, footer, contact, schema.org |
| `yv_email` | Footer, contact, schema.org |
| `yv_address` | Footer, contact, schema.org |
| `yv_opening_hours` | Contact, schema.org |
| `yv_maps_embed_url` | Contact (iframe Google Maps) |
| `yv_business_type` | Schema.org @type (Dentist, Restaurant, etc. Defaut: LocalBusiness) |
| `yv_footer_description` | Footer |
| `yv_cta_text` / `yv_cta_link` | Bouton CTA header |
| `yv_social_facebook/instagram/linkedin/youtube/tiktok` | Footer + schema.org sameAs |
| `yv_gtm_id` / `yv_ga_id` | Analytics (charge apres consentement cookies) |

## Flexible Content layouts (11)

`text_image`, `cards`, `cta`, `testimonials`, `faq`, `gallery`, `map`, `numbers`, `text`, `video`, `team`

## CSS Variables (dans :root)

```css
--color-primary: #hex;        /* Couleur principale */
--color-primary-rgb: R, G, B; /* MEME couleur en RGB — TOUJOURS sync */
--color-primary-dark: #hex;   /* Hover */
--color-accent: #hex;         /* Accent secondaire */
--font-heading / --font-body  /* Polices */
```

## Dependances

- Font Awesome 6.5 (CDN, charge en async non-bloquant)
- SCF (Secure Custom Fields)
- Contact Form 7
- Yoast SEO
- Plugin youvanna-cookies (bandeau RGPD auto)

## Performance

- Cache busting automatique via `filemtime()`
- Font Awesome async (preload + onload swap)
- Hero image preload avec `fetchpriority="high"`
- Preconnect cdnjs + GTM
- Lazy loading sur toutes les images below-fold
- `<noscript>` fallback pour animations `.reveal`
- Bloat WP supprime (block CSS, emoji, oEmbed, REST link)
