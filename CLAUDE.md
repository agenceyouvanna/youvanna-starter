# Youvanna Starter - Guide Claude Code

Ce fichier est lu automatiquement par Claude Code. Il contient toutes les regles, conventions et feedbacks pour travailler sur les sites Youvanna.

---

## Regles absolues

1. **JAMAIS de couleur hardcodee** - Toute couleur passe par les CSS variables `:root` dans `main.css`. NULLE PART ailleurs.
2. **JAMAIS de code duplique** - Utilise les helpers PHP existants (yv_field, yv_option, yv_image, yv_render_card, etc.)
3. **JAMAIS hardcoder une info client** - Tout passe par `wp option update yv_xxx` ou `update_field()`
4. **TOUJOURS les accents francais** - e, e, e, e, a, a, u, u, o, c, i. JAMAIS "equipe" -> "equipe", JAMAIS "Pret" -> "Pret". Si SSH pose des problemes d'encodage, utiliser `scp` + `wp eval-file`.
5. **JAMAIS inventer ce qu'on ne sait pas** - Pas de faux avis, pas de faux chiffres, pas de fausses donnees. Lorem Ipsum pour les temoignages si pas de vrais avis. Pas de section numbers si pas de chiffres verifies.
6. **JAMAIS de tirets speciaux** - Utiliser `-` uniquement. Pas de em-dash, en-dash, smart quotes.
7. **TOUJOURS des images Gemini sur les cartes** - Pas d'icones quand on peut mettre une image. Icones FA en fallback uniquement si 6+ cartes.
8. **JAMAIS de texte sur les images Gemini** - Toujours inclure "no text, no words, no letters, no logos, no watermarks" dans les prompts.
9. **Ratio d'image = reflechir au contenu** - Pas de ratio fixe par type de section. Le ratio depend de la quantite de texte a cote. Rediger le contenu AVANT de generer les images.
10. **Schema.org = automatique** - Les schemas LocalBusiness, WebSite, BreadcrumbList, FAQPage, BlogPosting sont generes par le theme. JAMAIS ecrire de schema manuellement.
11. **Animations = automatiques** - Counter, stagger, parallax, marquee sont dans le theme. Ne rien ajouter.
12. **TOUJOURS des FAQ pour le SEO** - Ajouter une section FAQ sur chaque page pertinente.
13. **Section headers style Framer** - Utiliser `<mark>` pour highlight, badge pill en 3e argument de `yv_section_header()`.

---

## Architecture

```
youvanna-starter/
|-- functions.php          # Helpers, SCF fields, admin UX, GTM/GA, Schema.org
|-- header.php / footer.php
|-- front-page.php         # Homepage (hero, services, about, testimonials, CTA)
|-- page.php               # Pages interieures (hero + flexible content)
|-- page-contact.php       # Contact (hero + CF7 + coordonnees)
|-- single.php / home.php / archive.php  # Blog
|-- 404.php
|-- CLAUDE.md              # Ce fichier - lu par Claude Code
|-- assets/css/main.css    # Design system - variables CSS, animations, marquee
|-- assets/js/main.js      # Menu, counter, stagger reveal, parallax
|-- template-parts/        # 8 layouts flex content
    |-- section-text_image.php
    |-- section-cards.php
    |-- section-cta.php
    |-- section-testimonials.php (marquee horizontal)
    |-- section-faq.php
    |-- section-gallery.php
    |-- section-map.php
    |-- section-numbers.php (counter animation)
```

## Helpers PHP

```php
yv_option($name, $fallback)              // wp_options avec prefixe yv_
yv_field($name, $fallback, $post_id)     // Champ SCF (gere 0 et valeurs falsy)
yv_image($name, $size, $post_id)         // URL image SCF
yv_render_hero($args)                     // Bandeau hero (background-image)
yv_section_header($title, $sub, $badge)  // H2 + subtitle + badge pill. $title supporte <mark>
yv_render_card($args)                     // Carte (image_id > image > icon)
yv_render_testimonial()                   // Temoignage (dans repeater)
yv_render_stats($rows, $class)           // Grille de chiffres (counter animation auto)
```

## Options globales (yv_)

| Cle | Usage |
|-----|-------|
| `yv_phone` | Header, footer, contact, schema.org |
| `yv_email` | Footer, contact, schema.org |
| `yv_address` | Footer, contact, schema.org |
| `yv_opening_hours` | Contact |
| `yv_maps_embed_url` | Contact (iframe Google Maps) |
| `yv_footer_description` | Footer |
| `yv_cta_text` / `yv_cta_link` | Bouton CTA header |
| `yv_social_facebook/instagram/linkedin/youtube/tiktok` | Footer |
| `yv_gtm_id` / `yv_ga_id` | Analytics (charge apres consentement cookies) |

## Champs SCF par page

**Homepage** : hero_title, hero_subtitle, hero_cta1_text/link, hero_cta2_text/link, hero_image, services_title, services_subtitle, services (repeater: icon, title, description, image, link), about_title, about_text, about_image, about_button, stats (repeater: number, label), testimonials_title, testimonials (repeater: text, name, role, rating, photo), cta_title, cta_text_home, cta_background, cta_button_text/link

**Pages interieures** : page_hero_title, page_hero_subtitle, page_hero_image, yv_sections (flexible content)

**Page contact** : contact_form_title, contact_form_id, show_map

## Flexible Content layouts

`text_image`, `cards`, `cta`, `testimonials`, `faq`, `gallery`, `map`, `numbers`

## CSS Variables (dans :root)

```css
--color-primary: #hex;        /* Couleur principale */
--color-primary-rgb: R, G, B; /* Meme couleur en RGB */
--color-primary-dark: #hex;   /* Hover */
--color-accent: #hex;         /* Accent secondaire */
--font-heading / --font-body  /* Polices */
```

## Dependances

- Font Awesome 6.5 (CDN)
- SCF (Secure Custom Fields - fork gratuit d'ACF avec Repeater + Flex Content)
- Contact Form 7
- Yoast SEO
- Plugin youvanna-cookies (bandeau RGPD auto)
