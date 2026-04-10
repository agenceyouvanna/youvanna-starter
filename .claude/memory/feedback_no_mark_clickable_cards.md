---
name: No mark tags + Clickable cards
description: JAMAIS de <mark> dans les titres, et les cards doivent être entièrement cliquables
type: feedback
---

1. JAMAIS de `<mark>` dans les titres — pas de texte surligné/highlight. Les titres sont en texte brut uniquement.
**Why:** Le client trouve ça moche et non professionnel.
**How to apply:** Ne jamais utiliser `<mark>` dans hero_title, page_hero_title, section titles, cta_title, ni aucun titre. Retirer si présent.

2. Les cards doivent être entièrement cliquables — quand une carte a un lien, toute la carte est un `<a>` (pas juste le texte CTA).
**Why:** Meilleure UX — l'utilisateur peut cliquer n'importe où sur la carte.
**How to apply:** Le template yv_render_card utilise `<a class="card card-clickable">` quand un lien existe. Le texte CTA devient un `<span class="card-link">` (pas un `<a>` imbriqué) pour garder le SEO sans liens nested.
