---
name: French Accents Mandatory
description: TOUJOURS mettre les accents français dans TOUT le contenu — meta titles, descriptions, alt texts, catégories, FAQ, JSON SEO
type: feedback
---

TOUJOURS mettre les accents français dans TOUT le contenu produit, y compris dans le CODE PHP / JS / HTML et les interfaces admin.

**Why:** L'utilisateur a insisté plusieurs fois et s'est énervé plusieurs fois (notamment 2026-04-09 sur le plugin sfscmfco-auth où j'avais écrit "Medecin", "adhesion", "reglages" dans l'admin-dashboard, registration.php, stripe.php). "A propos" au lieu de "À propos", "Securite" au lieu de "Sécurité" = BUG bloquant. Écrire du code sans accents est aussi grave qu'écrire du contenu web sans accents - tout texte qui sera affiché à un humain DOIT avoir les accents.

**How to apply:**
- CHAQUE texte visible : é, è, ê, ë, à, â, ù, û, ô, ç, î, œ, æ
- Inclut : titres, meta titles Yoast, meta descriptions, alt texts, noms de catégories, FAQ, boutons, labels menu, ancres, JSON SEO
- Inclut AUSSI : strings PHP ('Médecin', 'Réglages', 'Adhésion'), labels de formulaires, messages d'erreur, notices admin, commentaires PHPDoc visibles, placeholders, tooltips
- Seuls les slugs URL, noms de variables, clés d'options WP, et focus_keywords Yoast peuvent être sans accents
- Réflexe : quand j'écris "Medecin" / "acces" / "reglages" / "deja" / "apres" dans du code, corriger AVANT de valider
- Si un accent manque dans un output d'agent = corriger immédiatement, ne pas passer à la suite
- Vérifier l'output de CHAQUE agent avant de passer au suivant
