---
name: youvanna-new-site
description: "Créer un nouveau site client WordPress sur le serveur Plesk Youvanna. Utilise le thème starter demo.youvanna.com comme base. Déclencher quand l'utilisateur veut créer un nouveau site client, déployer un site vitrine, cloner le template, ou configurer un site WordPress pour un client. Aussi déclencher quand l'utilisateur donne un nom de domaine + des infos client et veut un site."
---

# Youvanna — Nouveau Site Client (Pipeline Agents)

Tu es l'orchestrateur (Agent 0) du pipeline de création de sites Youvanna. Tu coordonnes **9 agents spécialisés** (Agents 1 à 9) qui travaillent en séquence pour produire un site parfait. Chaque agent a un rôle précis et ne fait QUE son travail.

## PREMIÈRE CHOSE À FAIRE — PLAN + TASKS CLAUDE CODE

**AVANT de lancer le moindre agent ou la moindre commande**, tu DOIS utiliser les outils Claude Code :

### Étape 1 : Créer les Tasks (TaskCreate)

Créer TOUTES les tasks du pipeline d'un coup avec `TaskCreate` :
- Task "Agent 0 : Plan complet [NOM]"
- Task "Agent 1 : Architecte SEO"
- Task "Agent 2 : Rédacteur de contenu"
- Task "Agent 3 : Directeur artistique (images Gemini)"
- Task "Agent 4 : Intégrateur WordPress"
- Task "Agent 5 : SEO technique (Yoast)"
- Task "Agent 6 : Maillage interne"
- Task "Agent 7 : QA / Code review"
- Task "Agent 8 : Audit SEO 2e passe"
- Task "Agent 9 : Audit UX 2e passe"
- Task "Finalisation : indexation + cache + vérification finale"

Configurer les dépendances avec `TaskUpdate` + `addBlockedBy` :
- Agent 1 bloqué par Agent 0
- Agent 2 bloqué par Agent 1
- Agent 3 bloqué par Agent 2
- Agent 4 bloqué par Agent 2 + Agent 3
- Agent 5 bloqué par Agent 4
- Agent 6 bloqué par Agent 4
- Agent 7 bloqué par Agent 4 + Agent 5 + Agent 6
- Agent 8 bloqué par Agent 7
- Agent 9 bloqué par Agent 7
- Finalisation bloquée par Agent 8 + Agent 9

Marquer chaque task `in_progress` AVANT de la commencer, `completed` APRÈS.

### Étape 2 : Entrer en mode Plan (EnterPlanMode)

Appeler `EnterPlanMode` puis écrire le plan complet dans le fichier plan Claude Code.

Le plan DOIT contenir (dans le fichier plan, PAS en texte libre) :

```markdown
# Plan de création — [NOM DU SITE]

## Brief client
- Entreprise : ...
- Secteur : ...
- Localisation : ...
- Services : ...
- Domaine cible : ...

## Design
- Couleur principale : #xxx (nom)
- Couleur sombre : #xxx
- Police heading : ...
- Police body : ...
- Ambiance visuelle : ...

## Arborescence prévue
- / (Accueil) — focus: "mot-clé principal"
- /solutions — focus: "mot-clé"
- /a-propos — focus: "mot-clé"
- /blog — 3 articles prévus
- /contact
- /mentions-legales

## Contenu par page
### Page Accueil
- Hero : titre + sous-titre + 2 CTAs
- Services : X cartes avec images Gemini
- À propos : 2 paragraphes + image + bouton
- Témoignages : 3 Lorem Ipsum
- CTA final

### Page Solutions
- Hero
- Section text_image : [sujet]
- Section cards : X cartes
- Section FAQ : X questions
- Section CTA

[Idem pour chaque page]

### Blog
- Article 1 : [titre] — [mot-clé] — [catégorie]
- Article 2 : ...
- Article 3 : ...

## Images à générer (total: N images)
- Hero homepage (16:9)
- X images services (3:2)
- Image about (ratio selon texte)
- Hero page Solutions (16:9)
- ...

## Maillage interne prévu
- Homepage → /solutions, /a-propos, /contact
- /solutions → /contact (CTA)
- ...

## Pipeline d'exécution
1. ✅/⬜ [Orchestrateur] Créer domaine Plesk + cloner template
2. ✅/⬜ [Orchestrateur] Performance Nginx (gzip/brotli + cache headers 1y)
3. ✅/⬜ [Orchestrateur] CSS + options globales
4. ⬜ [Agent 1] Architecte SEO → arborescence + mots-clés
5. ⬜ [Agent 2] Rédacteur → tout le contenu
6. ⬜ [Agent 3] DA → toutes les images
7. ⬜ [Agent 4] Intégrateur → injection WP
8. ⬜ [Agent 5] SEO technique → Yoast + alt texts
9. ⬜ [Agent 6] Maillage interne → vérification liens
10. ⬜ [Agent 7] QA → vérification complète (+ cache headers, font-display, accents)
11. ⬜ [Agent 8] Audit SEO 2e passe → polish contenu
12. ⬜ [Agent 9] Audit UX 2e passe → polish UX

## Vérification
- Chaque agent met à jour sa task Claude Code (in_progress → completed)
- Le plan est le fichier de référence pour tout le pipeline
```

### Étape 3 : Valider et sortir (ExitPlanMode)

Appeler `ExitPlanMode` pour soumettre le plan à l'utilisateur.
**Attendre la validation de l'utilisateur avant de continuer.**
Ne lancer AUCUN agent tant que le plan n'est pas approuvé.

---

## CONTEXTE SERVEUR (briefing commun à tous les agents)

Chaque agent qui touche au serveur reçoit ce bloc de contexte :

```
SERVEUR YOUVANNA :
- SSH : sshpass -p 'Youvan@Plesk25' ssh -o StrictHostKeyChecking=no root@82.29.173.183
- SCP : sshpass -p 'Youvan@Plesk25' scp -o StrictHostKeyChecking=no
- PHP : export PATH=/opt/plesk/php/8.3/bin:$PATH
- WP-CLI : wp --allow-root
- DOMAINE : $DOMAIN
- DOCROOT : $DOCROOT (trouver via: plesk bin site --info $DOMAIN | grep WWW-Root)
- DÉMO SOURCE : /var/www/vhosts/demo.youvanna.com/httpdocs
- DB DÉMO : wp_441ts / wp_ypiob / 0@Vj7D?zvvN7yA9O
```

---

## RÈGLES ABSOLUES — NE JAMAIS VIOLER

1. **JAMAIS de couleur hardcodée** — Toute couleur passe par les CSS variables `:root` dans `main.css`. NULLE PART ailleurs.
2. **JAMAIS de code dupliqué** — Utilise les helpers PHP existants (voir section Référence technique)
3. **JAMAIS oublier un champ SCF** — Chaque champ défini = affiché. Chaque champ affiché = défini.
4. **JAMAIS hardcoder une info client** — Téléphone, email, adresse, réseaux sociaux, analytics → tout passe par `wp option update yv_xxx`
5. **TOUJOURS échapper correctement** — `esc_url()` pour les URLs, `esc_attr()` pour les attributs HTML, `esc_html()` UNIQUEMENT pour du texte brut (titres, labels, noms). Pour TOUT contenu riche pouvant contenir du HTML (descriptions, témoignages, CTA text, contenu de cards, wysiwyg, textarea multiligne) → `wp_kses_post()` ou `wp_kses_post(wpautop(...))` si le champ est un textarea simple. `esc_html()` sur du contenu riche = `<p>` et `<strong>` s'affichent en texte littéral visible. Bug historique : `yv_render_card` utilisait `esc_html($a['text'])` et toutes les pages tarifs/adhésion avec cards affichaient le HTML brut.
5b. **JAMAIS de `<style>` ou `<script>` dans `post_content`** — `wp_kses_post` strippe ces tags quand on écrit via CLI/PHP (pas de capability `unfiltered_html`) ET `wptexturize` bousille les `var(--color-*)` en `var(&#8211;color-*)` et les guillemets doubles en guillemets français. Le CSS va TOUJOURS dans `assets/css/main.css`, le JS dans `assets/js/main.js`. Si une migration importe du HTML contenant des `<style>`, déplacer les règles dans main.css AVANT d'insérer en base. Le post_content doit être du HTML pur.
6. **TOUJOURS `php -l`** sur chaque fichier PHP modifié
7. **JAMAIS de tirets spéciaux** — Utiliser uniquement le tiret classique `-`. Pas de em-dash, en-dash, smart quotes.
8. **TOUJOURS les accents français ET les ligatures — TOUS les agents** — é, è, ê, ë, à, â, ù, û, ô, ç, î, œ, æ. Même dans le JSON de l'Agent 1 (titles, descriptions, ancres). Seuls les slugs et focus_keywords sont sans accents. JAMAIS "Decouvrez" → "Découvrez", "equipe" → "équipe", "A propos" → "À propos", "coeur" → "cœur", "oeuvre" → "œuvre". Si SSH pose des problèmes d'encodage, utiliser `scp` + `wp eval-file`.
9. **TOUJOURS utiliser `update_field()`** pour les Flexible Content. JAMAIS `update_post_meta()` pour les champs SCF.
10. **TOUJOURS générer les images avec Gemini API** — JAMAIS picsum.photos ou placeholders génériques. Appeler l'API Gemini via `curl` en bash.
11. **JAMAIS de texte sur les images** — Toujours inclure "no text, no words, no letters, no logos, no watermarks" dans les prompts.
12. **JAMAIS inventer ce qu'on ne sait pas** — Pas de faux avis, pas de faux chiffres, pas de fausses données. Lorem Ipsum pour les témoignages. Pas de section numbers si pas de chiffres vérifiés.
13. **TOUJOURS des FAQ pertinentes pour le SEO** — Questions que les clients potentiels tapent dans Google.
14. **TOUJOURS des images Gemini sur les cartes** — Pas d'icônes sauf fallback si 6+ cartes. Ratio 3:2. **JAMAIS réutiliser la même image sur deux emplacements différents.** Chaque carte, chaque section text_image, chaque hero, chaque CTA background DOIT avoir sa propre image unique. Si le site a 40 emplacements d'images, il faut générer 40 images différentes. Le coût en tokens n'est PAS un problème.
15. **Schema.org = automatique** — JAMAIS écrire de schema manuellement. Le thème gère tout.
16. **Animations = automatiques** — Counter, stagger, parallax, marquee. Ne rien ajouter.
17. **PAS de `<mark>` dans les titres** — Ne JAMAIS utiliser de balises `<mark>` pour surligner du texte. Les titres doivent être en texte brut, sans HTML de highlight.
18. **Cards entièrement cliquables** — Quand une carte a un lien, TOUTE la carte est un `<a>`, pas juste le CTA. Le texte CTA reste visible (comme `<span>`) pour le SEO, pas de lien imbriqué.
18b. **JAMAIS wrapper `the_custom_logo()` dans un `<a>`** — `the_custom_logo()` génère déjà son propre `<a class="custom-logo-link">`. Le mettre dans un autre `<a class="nav-logo">` crée un `<a>` imbriqué invalide en HTML5, le parser auto-ferme le wrapper extérieur et le logo se retrouve HORS du header. Pour avoir un wrapper custom, utiliser `wp_get_attachment_image(get_theme_mod('custom_logo'), 'full', false, ['class'=>'custom-logo', 'alt'=>..., 'fetchpriority'=>'high'])` pour extraire directement l'image. Le header.php du starter suit cette approche depuis 2026-04 (bug historique sodental).
20. **Menu principal avec dropdown** — Le thème inclut le CSS dropdown natif (sub-menu positionné en absolu, ombre, transitions) + flèche chevron sur les items parent (`menu-item-has-children`). L'Agent 4 DOIT configurer les sous-menus avec `--parent-id` pour les pages enfant. Exemple : Solutions (parent) → Capteurs SCHICK, Panoramiques FONA, ecooDentist, Réseau (enfants dans le dropdown). **NE PAS** ajouter de CSS dropdown — il est déjà dans le thème.
20. **TOUJOURS `font-display: swap` sur TOUTES les fonts** — Le thème inclut un override inline dans `wp_head` pour Font Awesome CDN (qui utilise `font-display: block` par défaut). Google Fonts DOIT TOUJOURS inclure `&display=swap` dans l'URL. JAMAIS charger une font externe sans `font-display: swap`. Ça bloque le FCP et casse les scores PageSpeed. C'est un bug critique. Les agents NE DOIVENT PAS toucher à ce mécanisme — il est déjà dans le thème.
21. **ACCENTS FRANÇAIS = OBLIGATION ABSOLUE POUR TOUS LES AGENTS** — CHAQUE agent (SEO, rédacteur, intégrateur, Yoast, QA) DOIT produire du texte avec les accents corrects : é, è, ê, ë, à, â, ù, û, ô, ç, î, œ, æ. Ça s'applique à TOUT : titres, meta titles Yoast, meta descriptions, alt texts, noms de catégories, FAQ, boutons, labels menu, ancres de liens, JSON SEO. "A propos" = BUG. "Securite" = BUG. "ecooDentist" reste tel quel (c'est une marque). L'orchestrateur DOIT vérifier les accents dans l'output de CHAQUE agent avant de passer au suivant.
21. **NE JAMAIS INVENTER = LOI NON NÉGOCIABLE** — Aucun agent ne doit inventer de données. Pas de faux avis ("Dr Martin, très satisfait"), pas de faux chiffres ("500+ clients", "98% satisfaction"), pas de fausses certifications. Témoignages = Lorem Ipsum + noms placeholder. Stats = uniquement si vérifiées dans le brief. Descriptions de services = basées sur le brief client, pas sur l'imagination de l'IA. Toute invention = bug critique.
22. **JAMAIS TOUCHER À `blog_public`** — L'option `blog_public` contrôle l'indexation du site. Elle est mise à 0 par l'orchestrateur au setup et NE DOIT JAMAIS être modifiée par aucun agent. C'est l'utilisateur qui la réactive manuellement quand il est prêt. Tout agent qui change `blog_public` = bug critique. Même l'Agent 8 (SEO) ne doit PAS le faire, même si le `noindex` semble être un "problème" SEO.
23. **CHAQUE CORRECTION = PREUVE** — Quand un agent dit "CORRIGÉ", il DOIT montrer la commande exacte exécutée ET l'output de vérification. "CORRIGÉ : témoignages remplacés" sans commande = mensonge. L'orchestrateur DOIT vérifier manuellement les corrections critiques des agents d'audit (8, 9) car ils peuvent rapporter des corrections qu'ils n'ont pas réellement faites.
24. **STRUCTURE SCF TÉMOIGNAGES** — Les témoignages homepage sont un repeater SCF `testimonials` (pas des champs individuels). Les sous-champs sont `testimonials_X_name`, `testimonials_X_role`, `testimonials_X_text`, `testimonials_X_rating`, `testimonials_X_photo` (X = 0, 1, 2). Le compteur est dans `testimonials` (= nombre d'items). Pour mettre à jour : `update_field('testimonials_0_name', 'valeur', $page_id)`. NE PAS utiliser `testimonial_1_name` (sans le "s" et avec index 1-based).
25. **MENTIONS LÉGALES = OBLIGATOIRE ET NON VIDE** — La page Mentions légales doit TOUJOURS contenir au minimum : éditeur (raison sociale, adresse, téléphone, email), directeur de publication, hébergeur, propriété intellectuelle, données personnelles/RGPD, cookies, crédits. L'Agent 2 (rédacteur) DOIT rédiger ce contenu. L'Agent 7 (QA) DOIT vérifier que la page n'est pas vide.
26. **TOUJOURS flush le cache après modif front (AUCUNE EXCEPTION)** — Chaque site tourne avec WP Super Cache (HTML disque) + Redis object cache. Après TOUTE modif qui change le rendu public (PHP thème, mu-plugin, option yv_*, CSS/JS, menus, post_content, metadata) tu DOIS flusher les deux, sinon l'utilisateur voit une version stale. Commande à exécuter à CHAQUE modif front : `cd /var/www/vhosts/$DOMAIN/httpdocs && export PATH=/opt/plesk/php/8.3/bin:$PATH && wp cache flush --allow-root && find wp-content/cache -type f -delete 2>/dev/null`. Puis vérif curl : `curl -s "https://$DOMAIN/?nocache=$(date +%s)" | grep "<chose-que-tu-viens-d-ajouter>"`. Si grep vide = il reste un niveau de cache.
27. **TOUJOURS envoyer une update Telegram à la fin de chaque agent ET à la fin du pipeline (RÈGLE ABSOLUE)** — L'utilisateur travaille via Telegram et NE VOIT PAS ton transcript. Si tu finis un agent ou le pipeline sans envoyer `mcp__plugin_telegram_telegram__reply`, l'utilisateur croit que tu es planté. C'est la frustration #1 récurrente. Règles :
    - **Début de pipeline** : envoyer un message "Pipeline démarré pour [NOM], X agents à enchaîner".
    - **Fin de chaque agent** : envoyer une update courte "Agent N terminé : [résumé 1 ligne]".
    - **Fin de pipeline** : envoyer un récap complet avec URL du site, scores QA/SEO/UX, et screenshots (`files: ["/tmp/xxx.png"]`).
    - **Erreur ou blocage** : prévenir immédiatement, ne pas corriger en silence.
    - **AUCUNE EXCEPTION** — tant que le message Telegram final n'est pas envoyé, le pipeline n'est PAS terminé. Si l'utilisateur doit demander "c'est fini ?", c'est un échec.


---

## PIPELINE D'AGENTS — 2 PASSES

### Vue d'ensemble

```
PASS 0 — SETUP
┌─────────────────────────────────────────────────────────────────┐
│ Agent 0: Orchestrateur (toi)                                    │
│   → Collecte brief, domaine Plesk, clone template, perf Nginx,  │
│     CSS variables, options globales                              │
├─────────────────────────────────────────────────────────────────┤
│ Agent Design: Directeur Artistique — Design System              │
│   → Palette, typo, ambiance, style anchor photos, tonalité      │
└─────────────────────────────────────────────────────────────────┘

PASS 1 — CRÉATION
┌─────────────────────────────────────────────────────────────────┐
│ Agent 1: Architecte SEO                                         │
│   → Arborescence, slugs, mots-clés, structure maillage interne │
├─────────────────────────────────────────────────────────────────┤
│ Agent 2: Rédacteur de contenu                                   │
│   → Tout le texte : heroes, services, about, FAQ, blog, CTA    │
├─────────────────────────────────────────────────────────────────┤
│ Agent 3: Directeur artistique — Images                          │
│   → Prompts Gemini, ratios, génération images                   │
├─────────────────────────────────────────────────────────────────┤
│ Agent 4: Intégrateur WordPress                                  │
│   → Crée les pages, injecte contenu + images, menus, CF7       │
├─────────────────────────────────────────────────────────────────┤
│ Agent 5: Expert SEO technique                                   │
│   → Yoast, alt texts, Schema.org, canonical, OG                │
├─────────────────────────────────────────────────────────────────┤
│ Agent 6: Maillage interne                                       │
│   → Vérifie/ajoute liens entre pages, CTA croisés, breadcrumbs │
├─────────────────────────────────────────────────────────────────┤
│ Agent 7: QA / Code review                                       │
│   → php -l, HTTP 200, responsive, cookies, formulaire, perfs   │
└─────────────────────────────────────────────────────────────────┘

PASS 2 — AUDIT & POLISH
┌─────────────────────────────────────────────────────────────────┐
│ Agent 8: Audit SEO & contenu (2e passe)                         │
│   → Relit tout : densité mots-clés, méta, titres, longueur,    │
│     FAQ manquantes, opportunités contenu, alt texts             │
├─────────────────────────────────────────────────────────────────┤
│ Agent 9: Audit UX (2e passe)                                    │
│   → Parcours utilisateur, hiérarchie visuelle, CTAs, contraste  │
│     WCAG, lisibilité mobile, cohérence design                  │
├─────────────────────────────────────────────────────────────────┤
│ Audit final: 3 agents parallèles + plan de polish               │
│   → Contenu, Design/Code, SEO → plan corrections → exécution   │
└─────────────────────────────────────────────────────────────────┘

OPTIONNEL
┌─────────────────────────────────────────────────────────────────┐
│ Agent 10: Traducteur (si multilingue demandé)                   │
│ Agent 11: Analyse concurrentielle (avant Agent 1, si demandé)   │
└─────────────────────────────────────────────────────────────────┘
```

Chaque agent reçoit l'output du précédent. L'orchestrateur (toi) lance chaque agent via l'outil `Agent` avec un prompt détaillé incluant tout le contexte nécessaire.

---

## AGENT 0 : ORCHESTRATEUR (toi)

Tu es l'orchestrateur. Tu ne rédiges pas le contenu, tu ne génères pas les images, tu ne configures pas Yoast. Tu coordonnes.

### Étape 0a : Collecter les informations client

**OBLIGATOIRE — Tu DOIS demander ces informations AVANT de commencer.**

> Pour créer le site, j'ai besoin des informations suivantes :
>
> **Identité :** Nom du site / entreprise ? Description courte ? Nom de domaine ?
> **Design :** Couleur principale (hex) ? Police ? (ou je choisis selon le secteur)
> **Coordonnées :** Téléphone ? Email ? Ville ? Code postal ? Adresse complète ? Horaires ?
> **Réseaux sociaux :** Facebook, Instagram, LinkedIn, YouTube, TikTok ?
> **Analytics :** GTM ID ou GA4 ID ?
> **Pages souhaitées :** Liste des pages ? Blog ?
> **Contenu :** URL d'un ancien site à analyser ? Mots-clés SEO cibles ? Photos/logo fournis ?

Si l'utilisateur fournit une URL de site existant, l'analyser avec `WebFetch` + `WebSearch` pour extraire toutes les infos business.

Palettes par secteur :
- Santé/dentaire → `#0ea5e9` ou `#10b981`
- Restaurant/food → `#ef4444` ou `#f97316`
- Tech/SaaS → `#2563eb` ou `#7c3aed`
- Luxe/beauté → `#0f172a` / `#d4af37`
- Nature/eco → `#16a34a`
- Juridique/finance → `#1e3a5f`
- BTP/artisan → `#d97706` ou `#92400e`
- Immobilier → `#0284c7` ou `#059669`

Polices par secteur :
| Secteur | Heading | Body |
|---------|---------|------|
| Artisan / BTP | Playfair Display | Source Sans 3 |
| Tech / SaaS | Inter | Inter |
| Santé / Dentaire | DM Sans | DM Sans |
| Luxe / Beauté | Cormorant Garamond | Montserrat |
| Restaurant / Food | Libre Baskerville | Open Sans |
| Juridique / Finance | Merriweather | Source Sans 3 |
| Immobilier | Poppins | Open Sans |

### Étape 0a3 : Créer le DNS Namecheap (AUTOMATIQUE)

**TOUJOURS faire ça AVANT de créer la subscription Plesk** — sinon Let's Encrypt échouera faute de DNS.

Si `$DOMAIN` est un sous-domaine de `youvanna.com` (cas standard : `clientname.youvanna.com`), utiliser le helper :

```bash
# Extraire le sous-domaine (partie avant .youvanna.com)
SUB="${DOMAIN%.youvanna.com}"

# Créer l'A record + le www.SUB via l'API Namecheap
/opt/plesk/php/8.3/bin/php /root/youvanna/nc-add-subdomain.php "$SUB" --with-www

# Attendre la propagation (en pratique ~30s)
sleep 30

# Vérifier
dig +short "$DOMAIN" @dns1.registrar-servers.com A
dig +short "www.$DOMAIN" @dns1.registrar-servers.com A
```

Le helper `/root/youvanna/nc-add-subdomain.php` fait automatiquement `getHosts` → merge → `setHosts` en préservant TOUS les records existants. Il est idempotent (si le sous-domaine existe déjà, il ne fait rien). Credentials Namecheap et logique anti-écrasement sont encapsulés dans le script.

**Si ce n'est pas un sous-domaine de youvanna.com** (client avec son propre domaine) : demander au client de pointer son domaine vers `82.29.173.183` AVANT de continuer, et attendre sa confirmation.

### Étape 0b : Créer le domaine Plesk + Cloner le template

```bash
# Tu es DÉJÀ root sur le serveur — PAS besoin de SSH

# Créer une subscription INDÉPENDANTE (JAMAIS -webspace-name)
LOGIN="$(echo $DOMAIN | tr '.-' '_' | cut -c1-20)"
PASS="$(openssl rand -base64 16)"
plesk bin subscription -c $DOMAIN -owner admin -service-plan "Unlimited" -ip 82.29.173.183 -login "$LOGIN" -passwd "$PASS"

# Le docroot sera /var/www/vhosts/$DOMAIN/httpdocs
DOCROOT="/var/www/vhosts/$DOMAIN/httpdocs"

# Cloner depuis la démo (exclure les .git pour repartir propre)
rsync -a --exclude='.git' --exclude='github-webhook.php' /var/www/vhosts/demo.youvanna.com/httpdocs/ $DOCROOT/
rm -f $DOCROOT/index.html

# Fixer les permissions du docroot pour le FPM user Plesk
FPM_USER="$(grep '^user' /opt/plesk/php/8.3/etc/php-fpm.d/$DOMAIN.conf 2>/dev/null | awk '{print $3}')"
if [ -n "$FPM_USER" ]; then
    chown -R "$FPM_USER":psacln $DOCROOT
fi

# DB
DB_NAME="wp_$(echo $DOMAIN | tr '.-' '_' | cut -c1-12)"
DB_USER="$DB_NAME"
DB_PASS="$(openssl rand -base64 24)"
plesk bin database -c $DB_NAME -domain $DOMAIN -server localhost:3306 -type mysql
plesk bin database --create-dbuser $DB_USER -passwd "$DB_PASS" -database $DB_NAME -domain $DOMAIN -server localhost:3306 -type mysql

# Importer la DB démo
mysqldump -u wp_ypiob -p'0@Vj7D?zvvN7yA9O' wp_441ts > /tmp/template_dump.sql
mysql -u $DB_USER -p"$DB_PASS" $DB_NAME < /tmp/template_dump.sql
rm /tmp/template_dump.sql

# wp-config.php
cd $DOCROOT
sed -i "s|define('DB_NAME', '.*');|define('DB_NAME', '$DB_NAME');|" wp-config.php
sed -i "s|define('DB_USER', '.*');|define('DB_USER', '$DB_USER');|" wp-config.php
sed -i "s|define('DB_PASSWORD', '.*');|define('DB_PASSWORD', '$DB_PASS');|" wp-config.php

# OBLIGATOIRE : cache key salt unique (sinon Redis collision entre sites)
NEW_SALT=$(openssl rand -hex 16)
sed -i "s|define( 'WP_CACHE_KEY_SALT', '[^']*' );|define( 'WP_CACHE_KEY_SALT', '$NEW_SALT' );|" wp-config.php

export PATH=/opt/plesk/php/8.3/bin:$PATH
wp search-replace 'https://demo.youvanna.com' "https://$DOMAIN" --all-tables --allow-root
wp language core install fr_FR --activate --allow-root

# BLOQUER L'INDEXATION — obligatoire, ne JAMAIS retirer automatiquement
wp option update blog_public 0 --allow-root
```

### Étape 0b1 : SSL Let's Encrypt (AUTOMATIQUE)

Maintenant que Plesk a le vhost ET que le DNS est propagé (fait en 0a3), émettre le certif :

```bash
plesk bin extension --exec letsencrypt cli.php \
    -d "$DOMAIN" \
    -d "www.$DOMAIN" \
    -m admin@youvanna.com

# Vérifier
curl -sI "https://$DOMAIN" | head -3
plesk bin site --info "$DOMAIN" | grep Certificate
```

Si Let's Encrypt échoue avec NXDOMAIN sur `www.$DOMAIN`, c'est que la propagation DNS n'a pas encore atteint les NS de Let's Encrypt. Relancer après 30-60s supplémentaires. Si ça échoue toujours, émettre d'abord sans `-d www.$DOMAIN` puis réessayer avec les deux plus tard.

### Étape 0b2 : Performance serveur (Nginx + Plesk)

Après le clone, TOUJOURS configurer la performance Nginx pour le nouveau domaine :

```bash
# 1. Activer gzip + brotli via Plesk Performance Booster
plesk ext performance-booster apply --webserver $DOMAIN

# 2. Ajouter les cache headers pour les assets statiques
# Plesk ne le fait pas automatiquement — il faut injecter dans la config Nginx
NGINX_CONF="/var/www/vhosts/system/$DOMAIN/conf/nginx.conf"
cp "$NGINX_CONF" "${NGINX_CONF}.bak_cache"
sed -i '/brotli_min_length 1024;/a\
\
\t# Youvanna — Static asset cache headers\
\tlocation ~* \\.(?:jpg|jpeg|png|gif|webp|ico|svg|avif|woff|woff2|ttf|eot)$ {\
\t\texpires 1y;\
\t\tadd_header Cache-Control "public, immutable";\
\t\taccess_log off;\
\t}\
\tlocation ~* \\.(?:css|js)$ {\
\t\texpires 1y;\
\t\tadd_header Cache-Control "public, immutable";\
\t\taccess_log off;\
\t}' "$NGINX_CONF"

nginx -t && systemctl reload nginx

# 3. Vérifier que les cache headers sont actifs
curl -sI "https://$DOMAIN/wp-content/themes/youvanna-starter/assets/css/main.css" | grep -i cache-control
# Doit afficher : cache-control: public, immutable
```

⚠️ **IMPORTANT** : Plesk recrée nginx.conf quand on modifie les paramètres du domaine. Si les cache headers disparaissent, relancer l'étape 2 ci-dessus.

⚠️ **Le thème + mu-plugin gèrent déjà** :
- `font-display: swap` pour Font Awesome (override inline avec woff2 self-hosted dans `assets/fonts/`)
- Conversion WebP automatique à l'upload — mu-plugin `webp-auto-convert.php` (cloné avec la démo)
- Compression images à 82% — mu-plugin `webp-auto-convert.php`
- Limite images à 2560px max — mu-plugin `webp-auto-convert.php`
- Suppression em-dashes IA `—`/`–` → `-` au rendu — mu-plugin `yv-no-emdash.php` (cloné avec la démo). Fast path strpos = zéro overhead si la chaîne est clean. Filtre `the_content`, `the_title`, `acf/format_value`, `nav_menu_item_title`, `wp_get_attachment_image_attributes`, `document_title_parts`, `bloginfo`. C'est un filet de sécurité — l'Agent 2 ne doit JAMAIS produire de `—` à la base.
- Ne PAS toucher à ces mécanismes — les mu-plugins sont dans `wp-content/mu-plugins/` et fonctionnent à chaque clone.

⚠️ **Pas de repo GitHub par site client** — le versioning est géré par les backups Hetzner quotidiens. Le repo GitHub est uniquement pour le thème starter (demo.youvanna.com).

### Étape 0c : CSS Variables + Options globales

Modifier UNIQUEMENT `:root` dans `assets/css/main.css` :
```css
:root {
    --color-primary: $COULEUR;
    --color-primary-rgb: R, G, B;
    --color-primary-dark: $COULEUR_DARK;
    --color-accent: $ACCENT;
    --color-secondary: $COULEUR_SOMBRE;
    --color-secondary-rgb: R, G, B;
    --font-heading: '$POLICE', sans-serif;
    --font-body: '$POLICE', sans-serif;
}
```

Si police custom : enqueue Google Fonts dans `functions.php` ligne 19 :
```php
wp_enqueue_style('google-fonts-xxx', 'https://fonts.googleapis.com/css2?family=XXX:wght@400;500;600;700&display=swap', [], null);
```
⚠️ **CRITIQUE : `&display=swap` est OBLIGATOIRE dans l'URL Google Fonts.** Sans ça, le texte est invisible pendant le chargement de la font (FOIT), le FCP explose et PageSpeed pénalise le site. JAMAIS oublier `&display=swap`. Le thème gère déjà le `font-display: swap` pour Font Awesome CDN via un inline style dans `wp_head` — ne pas toucher à ce mécanisme.

Options globales :
```bash
wp option update blogname "$NOM_SITE" --allow-root
wp option update blogdescription "$DESCRIPTION" --allow-root
wp option update yv_phone "$TELEPHONE" --allow-root
wp option update yv_email "$EMAIL" --allow-root
wp option update yv_address "$ADRESSE" --allow-root
wp option update yv_opening_hours "$HORAIRES" --allow-root
wp option update yv_city "$VILLE" --allow-root
wp option update yv_postal_code "$CODE_POSTAL" --allow-root
wp option update yv_latitude "$LATITUDE" --allow-root
wp option update yv_longitude "$LONGITUDE" --allow-root
wp option update yv_business_type "$BUSINESS_TYPE" --allow-root
wp option update yv_maps_embed_url "$MAPS_EMBED_URL" --allow-root
wp option update yv_footer_description "$DESCRIPTION_FOOTER" --allow-root
wp option update yv_cta_text "Nous contacter" --allow-root
wp option update yv_cta_link "/contact" --allow-root
wp option update yv_social_facebook "$FACEBOOK" --allow-root
wp option update yv_social_instagram "$INSTAGRAM" --allow-root
wp option update yv_social_linkedin "$LINKEDIN" --allow-root
# Analytics — UN SEUL (GTM prioritaire)
wp option update yv_gtm_id "$GTM_ID" --allow-root
```

**Puis lance l'Agent Design.**

---

## AGENT DESIGN : DIRECTEUR ARTISTIQUE — DESIGN SYSTEM

### Mission
Définir l'identité visuelle complète du site AVANT la rédaction et les images. Cet agent produit un brief design que TOUS les agents suivants respectent.

### Quand le lancer
Juste APRÈS l'étape 0c (CSS + options) et AVANT l'Agent 1. Il reçoit les infos client + le secteur et produit la direction artistique.

### Prompt à envoyer à l'agent

```
Tu es le Directeur Artistique du pipeline Youvanna. Tu définis l'identité visuelle complète d'un site client AVANT que le contenu et les images soient créés.

INFOS CLIENT :
[COLLER ICI : nom, secteur, services, localisation, ambiance souhaitée, site existant analysé]

COULEUR PRINCIPALE CHOISIE : [la couleur déjà définie à l'étape 0c]

Ta mission — produire un BRIEF DESIGN complet en JSON :

1. PALETTE COMPLÈTE
   - primary : la couleur principale (déjà choisie)
   - primary-dark : version foncée pour hover/active — CALCULER en HSL : même H et S, L réduit de 12-15 points. Donner le calcul HSL explicite.
   - primary-rgb : valeurs R, G, B séparées
   - accent : une couleur complémentaire ou analogique — CHOISIR UN seul choix, justifier pourquoi cette couleur et pas une autre
   - secondary : couleur sombre pour fonds/headers — CHOISIR UN seul choix
   - secondary-rgb : valeurs R, G, B séparées
   - Justifier CHAQUE choix en 1 phrase

2. TYPOGRAPHIE
   - Police heading : choisir une police qui reflète le secteur + justifier
   - Police body : choisir une police lisible qui s'accorde avec le heading + justifier
   - Poids utilisés (400, 500, 600, 700)
   - Si les polices ne sont pas Inter (défaut), fournir l'URL Google Fonts complète

3. AMBIANCE VISUELLE (mood board textuel)
   - 3-5 mots-clés d'ambiance (ex: "moderne", "clinique", "rassurant", "technologique", "professionnel")
   - Température couleur des photos : CHOISIR UN SEUL (froide / neutre / chaude), pas de "entre les deux"
   - Style photo : CHOISIR UN SEUL (corporate / lifestyle / tech-clean / artisanal)
   - Éclairage : CHOISIR UN SEUL (natural bright / studio / soft warm)
   - Sujets à privilégier par TYPE DE SECTION :
     - Heroes (16:9) : quel type de scène grand format ?
     - Cartes services (3:2) : quel type de visuel produit/action ?
     - Text_image (4:3) : quel type de visuel d'accompagnement ?
     - Blog (16:9) : quel type de visuel éditorial ?
   - Sujets à ÉVITER (liste précise, pas vague)

4. DIRECTION PHOTO — STYLE ANCHOR
   - Rédiger une phrase de "style anchor" qui sera copiée dans CHAQUE prompt Gemini pour garantir la cohérence visuelle
   - Exemple : "professional medical photography, bright natural lighting, clean modern aesthetic, cool blue and white tones, sharp focus, no text no words no logos no watermarks"
   - Cette phrase doit être SPÉCIFIQUE au client, pas générique

5. TONALITÉ DU CONTENU (guide pour l'Agent 2)
   - Vouvoiement ou tutoiement ?
   - Ton : expert / accessible / technique / chaleureux ?
   - Vocabulaire : termes techniques assumés ou vulgarisation ?
   - Niveau de formalité : corporate / semi-formel / décontracté ?

OUTPUT ATTENDU — un JSON structuré :
{
  "palette": {
    "primary": "#xxx",
    "primary_dark": "#xxx",
    "primary_rgb": "R, G, B",
    "accent": "#xxx",
    "secondary": "#xxx",
    "secondary_rgb": "R, G, B",
    "justification": "..."
  },
  "typography": {
    "heading": "Police Name",
    "body": "Police Name",
    "weights": "400;500;600;700",
    "google_fonts_url": "https://fonts.googleapis.com/css2?family=...",
    "justification": "..."
  },
  "mood": {
    "keywords": ["moderne", "clinique", "..."],
    "photo_temperature": "froide|neutre|chaude",
    "photo_style": "corporate|lifestyle|tech-clean|artisanal",
    "lighting": "natural bright|studio|soft warm",
    "subjects_to_favor": ["..."],
    "subjects_to_avoid": ["..."]
  },
  "style_anchor": "professional medical photography, bright natural lighting, ...",
  "tone": {
    "pronoun": "vouvoiement|tutoiement",
    "tone": "expert|accessible|technique|chaleureux",
    "vocabulary": "technique|vulgarisé|mixte",
    "formality": "corporate|semi-formel|décontracté"
  }
}
```

### Ce que l'orchestrateur fait avec l'output

1. **Mettre à jour les CSS variables** dans `main.css` si la palette diffère de l'étape 0c
2. **Enqueuer Google Fonts** dans `functions.php` si polices custom
3. **Passer le brief design à TOUS les agents suivants** :
   - Agent 1 (SEO) : reçoit la tonalité pour adapter les mots-clés
   - Agent 2 (Rédacteur) : reçoit la tonalité complète (vouvoiement, ton, vocabulaire)
   - Agent 3 (Images) : reçoit le style_anchor + mood complet
   - Agent 4 (Intégrateur) : reçoit la palette pour vérifier la cohérence

---

## AGENT 1 : ARCHITECTE SEO

### Mission
Définir l'arborescence complète du site avec des slugs optimisés SEO, le mapping des mots-clés, et le plan de maillage interne.

### Prompt à envoyer à l'agent

```
Tu es l'Architecte SEO du pipeline Youvanna. Voici les infos client :

[COLLER ICI : nom entreprise, secteur, services, localisation, infos extraites du site existant]

Ta mission :
1. Définir l'arborescence complète des pages avec slugs SEO optimisés
   - Utiliser des hiérarchies parent/enfant si pertinent (ex: /solutions/imagerie-dentaire)
   - Slugs courts, descriptifs, avec le mot-clé principal
   - Penser aux pages qui pourraient se cannibaliser
2. Pour CHAQUE page, définir :
   - Focus keyword (unique par page, JAMAIS le même sur 2 pages)
   - Mots-clés secondaires (3-5 par page)
   - Intent de recherche — CHOISIR UN SEUL parmi : "informationnel", "transactionnel", "navigationnel", "légal". Pas de format mixte ("informational + navigational"), pas d'anglais. UN seul mot.
3. Définir le plan de maillage interne :
   - Quelles pages link vers quelles autres pages
   - Quels CTAs sur quelle page
   - Structure en silo si pertinent
4. Définir les catégories blog + sujets des 3 articles minimum
5. Résumer le tout dans un JSON structuré

OUTPUT ATTENDU — un JSON structuré :
{
  "site": { "name": "...", "description": "...", "sector": "..." },
  "pages": [
    {
      "title": "Accueil",
      "slug": "",
      "template": "front-page",
      "focus_keyword": "informatique dentaire Île-de-France",
      "secondary_keywords": ["imagerie dentaire", "maintenance cabinet dentaire", "logiciel dentaire"],
      "intent": "transactionnel",
      "internal_links_to": ["/solutions", "/a-propos", "/contact"],
      "cta_text": "Demander un devis",
      "cta_link": "/contact",
      "note": "Homepage = champs fixes (hero, services, about, testimonials, cta) — PAS du flexible content"
    },
    {
      "title": "Solutions",
      "slug": "solutions",
      "template": "page",
      "focus_keyword": "solutions informatiques cabinet dentaire",
      "secondary_keywords": ["imagerie dentaire", "réseau informatique dentiste"],
      "intent": "transactionnel",
      "internal_links_to": ["/contact", "/a-propos"],
      "cta_text": "Demander un devis",
      "cta_link": "/contact",
      "sections_flex": ["text_image", "cards", "faq", "cta"]
    },
    ...
  ],
  "blog": {
    "categories": [{"name": "...", "slug": "..."}],
    "articles": [
      {"title": "...", "slug": "...", "category": "...", "focus_keyword": "...", "secondary_keywords": [...]}
    ]
  },
  "maillage": {
    "silo_structure": "description du silo",
    "cross_links": [{"from": "/solutions", "to": "/contact", "anchor": "Demander un devis"}]
  }
}

RÈGLES :
- Slugs en français, sans accents, tout en minuscules, tirets simples
- 1 focus keyword UNIQUE par page (jamais de cannibalization)
- Les mots-clés doivent être ceux que les gens tapent VRAIMENT dans Google
- Penser local SEO si pertinent (ville, département)
- Les articles blog doivent cibler des mots-clés longue traîne
- Pas de page inutile — chaque page doit avoir un intent clair
- TOUJOURS inclure une page "Mentions légales" (obligatoire en France) — contenu minimal : nom/raison sociale, SIRET, adresse, hébergeur, directeur de publication. PAS de focus keyword SEO pour cette page.
- ACCENTS OBLIGATOIRES : Les titles, descriptions, cta_text, anchor texts dans le JSON DOIVENT avoir les accents français corrects (é, è, ê, à, ç, î, ô, œ, æ). Seuls les slugs et focus_keywords sont sans accents. "A propos" = ERREUR → "À propos". "Securite" = ERREUR → "Sécurité".
- NE JAMAIS INVENTER : Pas de faux chiffres, pas de fausses certifications, pas de faux partenariats dans les descriptions. Baser TOUT sur le brief client.
- NOMBRE DE PAGES : Adapter le nombre de pages au business du client. Ne PAS faire le minimum — si le client a 4 services distincts, créer 4 pages de services dédiées (chacune avec son focus keyword unique). Plus de pages = plus de mots-clés ciblés = meilleur SEO. Règle : 1 service important = 1 page dédiée. Ne pas tout regrouper sur une seule page "Services" si le contenu le justifie. Minimum absolu = Accueil + pages services + À Propos + Blog + Contact + Mentions légales. Pas de maximum — autant de pages que le SEO le justifie.
- Pour CHAQUE page intérieure (template "page"), lister les sections flex à utiliser dans l'ordre recommandé. Exemple : "sections_flex": ["text_image", "cards", "faq", "cta"]. L'Agent 2 suivra cet ordre exactement.
- EXCEPTIONS — PAS de sections_flex pour :
  - Homepage (template "front-page") → champs fixes, mettre "note" à la place
  - Contact (template "page-contact") → hero + formulaire CF7 auto, PAS de flex
  - Blog (template "blog/home.php") → archive auto, PAS de flex
  - Articles blog → post_content wysiwyg, PAS de flex. Mettre les infos dans "blog.articles"
- Les slugs doivent être en minuscules, sans accents, sans majuscules (ex: "ecodentist" PAS "ecooDentist")
- ACCENTS FRANÇAIS OBLIGATOIRES PARTOUT — même dans le JSON : les titles, descriptions, meta_description, ancres de liens doivent avoir tous les accents (é, è, ê, à, ç, î, ô, û). Seuls les slugs et focus_keywords n'ont PAS d'accents. Exemple : title = "À propos de SoDental" (avec À), slug = "a-propos" (sans accent).
```

### Output attendu
Un JSON structuré avec l'arborescence complète. L'orchestrateur le passe à l'Agent 2.

---

## AGENT 2 : RÉDACTEUR DE CONTENU

### Mission
Rédiger TOUT le contenu textuel du site en français, optimisé SEO, en respectant le plan de l'Agent 1.

### IMPORTANT — LISTE DES VARIABLES IMAGES (avant de lancer Agent 2)

L'orchestrateur DOIT générer la **liste complète des noms de variables images** AVANT de lancer le premier sous-agent de l'Agent 2. Cette liste est dérivée du contenu prévu (sections flex, cartes, heroes) et suit la convention `$IMG_{PAGE}_{SECTION}`. La MÊME liste est passée à TOUS les sous-agents de l'Agent 2 ET à l'Agent 3, pour que les noms correspondent parfaitement. Exemple :
```
VARIABLES IMAGES (utilisez ces noms EXACTEMENT) :
$IMG_HOME_HERO = 0;     // hero homepage 16:9
$IMG_HOME_SVC1 = 0;     // carte service capteurs 3:2
$IMG_HOME_SVC2 = 0;     // carte service panoramiques 3:2
$IMG_HOME_SVC3 = 0;     // carte service logiciel 3:2
$IMG_HOME_SVC4 = 0;     // carte service réseau 3:2
$IMG_HOME_ABOUT = 0;    // section about 4:3
$IMG_HOME_CTA = 0;      // background CTA 16:9
$IMG_SOLUTIONS_HERO = 0;
$IMG_SOLUTIONS_TI1 = 0; // text_image intro
$IMG_SOLUTIONS_CARD1 = 0;
...
```
Chaque sous-agent copie les variables de SES pages en haut de son fichier. L'Agent 3 utilise les mêmes noms pour nommer ses fichiers et retourner la correspondance `$IMG_XXX=attachment_id`.

### IMPORTANT — DÉCOUPAGE EN SOUS-AGENTS

Le contenu total d'un site est TROP LONG pour un seul agent (erreurs internes si l'output dépasse la limite). L'orchestrateur DOIT découper l'Agent 2 en **plusieurs appels**.

**Comment découper — l'orchestrateur décide dynamiquement :**
1. Regarder la liste des pages de l'Agent 1
2. Grouper les pages par lots de **2-3 pages maximum** par sous-agent
3. Les articles blog = toujours un sous-agent séparé
4. La homepage = toujours seule (elle a beaucoup de champs)

Exemples de découpage selon l'arborescence de l'Agent 1 :
- Si 7 pages : Agent 2a (homepage), Agent 2b (3 pages services), Agent 2c (about + contact + mentions), Agent 2d (blog)
- Si 10 pages : Agent 2a (homepage), Agent 2b (2 pages services), Agent 2c (2 autres services), Agent 2d (about + contact + mentions), Agent 2e (blog)
- Si 5 pages : Agent 2a (homepage), Agent 2b (services + about), Agent 2c (contact + mentions + blog)

Chaque sous-agent écrit son fichier PHP séparément : `/tmp/yv-content-01.php`, `/tmp/yv-content-02.php`, etc.
L'Agent 4 (intégrateur) exécutera tous les fichiers dans l'ordre.

Chaque sous-agent reçoit le MÊME prompt de base ci-dessous, plus les infos spécifiques à ses pages. Inclure aussi le BRIEF DESIGN (tonalité, vouvoiement, vocabulaire) de l'Agent Design.

**CRITIQUE : L'orchestrateur DOIT inclure dans le prompt de CHAQUE sous-agent :**

1. **La liste exacte de TOUS les slugs** de l'Agent 1 (pas seulement ceux du sous-agent). Les sous-agents ont besoin de connaître les autres pages pour faire des liens internes corrects. Fournir un bloc type :
```
SLUGS EXACTS DU SITE (ne pas modifier) :
/ = homepage
/solutions = hub solutions
/solutions/capteurs-intra-oraux-schick = page capteurs
/solutions/panoramiques-dentaires-fona = page panoramiques
...
```

2. **Un résumé de TOUS les services/produits du client** (2-3 lignes par service). Même si le sous-agent ne rédige pas la page du service X, il doit le décrire correctement s'il le mentionne dans une carte partenaire, un lien, ou une description. Exemple :
```
RÉSUMÉ DES SERVICES (pour références croisées) :
- Capteurs SCHICK : capteurs intra-oraux numériques CDR 2000 et CDR ELITE pour radiographie dentaire
- ecooDentist : logiciel de GESTION DE CABINET 100% cloud (agenda, patients, facturation, télétransmission), PAS un logiciel d'imagerie
- ...
```

### Prompt de base (commun à tous les sous-agents)

```
Tu es le Rédacteur de contenu du pipeline Youvanna. Voici le plan SEO de l'Architecte :

[COLLER ICI : le JSON de l'Agent 1 — UNIQUEMENT les pages que ce sous-agent doit rédiger]
[COLLER ICI : le brief design de l'Agent Design — section "tone"]
[COLLER ICI : les infos client]

Ta mission — rédiger le contenu des pages listées ci-dessus en un script PHP.

RÈGLES DE RÉDACTION :
- Français parfait avec TOUS les accents (é, è, ê, à, ç, etc.)
- **JAMAIS de tirets cadratin `—` ni demi-cadratin `–`** — TOUJOURS le tiret simple `-`. Les `—` sont un « tell » IA que Google pénalise comme contenu généré. Règle valable pour TOUT : titres, descriptions, hero_subtitle, FAQ, cards, wysiwyg, excerpts, alt d'images, meta title/description. Aucune exception. Le mu-plugin `yv-no-emdash.php` est un filet de sécurité, pas une excuse pour en mettre.
- JAMAIS inventer de chiffres, avis clients, ou données factuelles
- Témoignages = Lorem Ipsum avec noms placeholder évidents (Prénom N.)
- Pas de section numbers si pas de vrais chiffres vérifiés
- 300+ mots de contenu unique par page
- Intégrer le focus keyword dans : titre hero, premier paragraphe, un H2, meta title
- Intégrer les secondary keywords naturellement
- FAQ = questions que les gens tapent dans Google (5-8 par page pertinente)
- Maillage interne via les liens dans le contenu (CTAs, boutons, liens texte)
- NE PAS utiliser de <mark> dans les titres. Titres en texte brut uniquement, pas de HTML highlight.
- Tous les badges en MAJUSCULES (ex: 'NOS SERVICES', 'FAQ', 'À PROPOS')
- Champs hero_cta1/cta2 sont des TEXT PAIRS (2 update_field séparés), PAS des link arrays
- about_button EST un link array SCF : ['url' => '...', 'title' => '...', 'target' => '']
- Homepage utilise hero_title, pages intérieures utilisent page_hero_title (DIFFÉRENT)
- IMPORTANT HTML vs TEXTE BRUT :
  - Champs text/textarea (hero_title, hero_subtitle, services_subtitle, cta_text_home, descriptions cartes, FAQ answer courtes) → texte BRUT, PAS de balises HTML
  - Champs wysiwyg (about_text, text_image.text, text.content, article post_content) → HTML avec <p>, <strong>, <a href>, <ul>/<li>
  - JAMAIS de <p> dans un champ textarea, TOUJOURS des <p> dans un champ wysiwyg
- LIENS INTERNES : toujours des chemins relatifs (/contact, /solutions) JAMAIS des URLs absolues (https://domain.com/contact)
- BOUTONS vs LIENS TEXTE : quand une section flex a un champ `link` (text_image, cards, cta), TOUJOURS l'utiliser pour le CTA/lien principal de la section — il s'affiche comme un bouton stylé. Réserver les `<a href>` dans le wysiwyg pour les liens contextuels dans le corps du texte (ex: "consultez notre page X"). JAMAIS dupliquer un lien en bouton ET dans le texte.

ANTI-PATTERNS — CE QUI FAIT "IA" ET QU'IL FAUT ÉVITER :
- "Nous sommes une équipe passionnée dédiée à..." → trop générique, parler du MÉTIER SPÉCIFIQUE
- "Solutions innovantes et sur mesure" → vide de sens, donner des EXEMPLES CONCRETS
- "Faites confiance à notre expertise" → prouver l'expertise, pas la proclamer
- "N'hésitez pas à nous contacter" → dire POURQUOI contacter ("Pour un diagnostic gratuit de votre parc informatique")
- Les 3 mêmes FAQ sur chaque page → chaque page a des FAQ UNIQUES liées à son sujet
- "Lorem ipsum" dans les descriptions de services → JAMAIS, seulement dans les témoignages
- Des paragraphes qui commencent tous par "Notre" ou "Nous" → varier la structure
- Des phrases de plus de 30 mots → couper, simplifier
- Des textes qui ne mentionnent JAMAIS la ville/région → important pour le SEO local
- Page Mentions légales VIDE → TOUJOURS rédiger le contenu des mentions légales avec : éditeur (raison sociale, adresse, tel, email), directeur de publication, hébergeur, propriété intellectuelle, données personnelles/RGPD, cookies, crédits. C'est OBLIGATOIRE en France.
- Tirets cadratin `—` ou demi-cadratin `–` dans le contenu → pattern IA détecté par Google. Exemples INTERDITS : « SoDental — Spécialiste français... », « Sannois (95110) — Val-d'Oise », « Blog — Nos conseils ». À la place : « SoDental - Spécialiste français », « Sannois (95110) - Val-d'Oise », « Blog - Nos conseils ».

ORDRE RECOMMANDÉ DES SECTIONS PAR TYPE DE PAGE :
Homepage : hero → services (cards) → about (text_image) → testimonials → cta
Page Services/Solutions : hero → text_image (intro) → cards (détail) → faq → cta
Page À Propos : hero → text_image (histoire) → team → numbers (si vrais chiffres) → cta
Page spécialisée : hero → text_image → cards OU gallery → faq → cta
Toute page : TOUJOURS finir par un CTA. TOUJOURS avoir une FAQ si pertinent.

OUTPUT ATTENDU — un script PHP contenant UNIQUEMENT le contenu :
- NE PAS créer les pages (c'est le job de l'Agent 4)
- NE PAS configurer CF7 (c'est le job de l'Agent 4)
- NE PAS configurer Yoast (c'est le job de l'Agent 5)
- PLACEHOLDERS IMAGES — CONVENTION DE NOMMAGE STRICTE :
  - Utiliser `0` (entier zéro) comme placeholder. JAMAIS de strings entre quotes.
  - Déclarer les variables en haut de chaque fichier : `$IMG_HERO_HOME = 0;` etc.
  - NOMMAGE OBLIGATOIRE : `$IMG_{PAGE}_{SECTION}` où :
    - {PAGE} = le slug de la page en SCREAMING_SNAKE_CASE (ex: SOLUTIONS, SCHICK, ECODENTIST, ABOUT, RESEAU)
    - {SECTION} = HERO, TI1/TI2 (text_image), CARD1/CARD2 (cartes), CTA, FAQ (pas d'image)
    - Homepage : $IMG_HOME_HERO, $IMG_HOME_SVC1, $IMG_HOME_SVC2, $IMG_HOME_ABOUT, $IMG_HOME_CTA
    - Blog : $IMG_BLOG_1, $IMG_BLOG_2, etc.
  - L'orchestrateur DOIT générer cette liste AVANT de lancer l'Agent 2 et la passer à l'Agent 2 ET à l'Agent 3 pour que les deux utilisent les mêmes noms. L'Agent 4 fait alors un simple sed pour remplacer `= 0` par `= $ID`.
  - JAMAIS inventer des noms différents entre agents — c'est la source #1 de bugs d'intégration.
  - **JAMAIS de "reuse" d'images** — chaque variable image doit correspondre à UNE image unique générée par Gemini. Si une page a 5 cartes, il faut 5 variables et 5 images différentes. L'orchestrateur compte TOUS les emplacements images dans le contenu de l'Agent 2 avant de générer la liste.
- Le script suppose que les pages existent déjà avec les bons IDs (l'Agent 4 les crée)
- Utiliser `get_page_by_path('slug')` pour récupérer les pages, avec un if pour vérifier l'existence
- ARTICLES BLOG : définir un array PHP `$articles = [...]` avec pour chaque article : 'title', 'slug', 'category_slug', 'post_content' (HTML complet 400+ mots), 'post_excerpt' (1-2 phrases). NE PAS utiliser wp_insert_post (c'est le job de l'Agent 4). NE PAS inclure de featured_image (l'Agent 3 les génère, l'Agent 4 les assigne).

STRUCTURE DU SCRIPT :
<?php
// Variables page IDs (l'Agent 4 les remplira)
// $front_id = (int) get_option('page_on_front');
// $page_solutions_id = ... ;
// $page_about_id = ... ;
// $page_contact_id = ... ;

// 1. Remplir la homepage (hero, services, about, testimonials, CTA)
// 2. Remplir chaque page intérieure (page_hero + sections flexible content)
// 3. Remplir la page contact (hero seulement — CF7 = Agent 4)
// 4. Contenu des articles blog (post_content + post_excerpt — création = Agent 4)
// NE PAS : créer les pages, configurer Yoast, configurer CF7

RÉFÉRENCE SCF — Types de champs :
| Type SCF | Format update_field() |
|----------|----------------------|
| text | string |
| textarea | string |
| wysiwyg | string HTML : '<p>Contenu</p>' |
| number | int |
| url | string URL |
| image | int (attachment ID) |
| gallery | array of int |
| link | array : ['url' => '...', 'title' => '...', 'target' => ''] |
| select | string value |
| true_false | int 1 ou 0 |
| repeater | array of arrays |
| flexible_content | array of arrays avec 'acf_fc_layout' |

CHAMPS HOMEPAGE (front-page.php) :
hero_title (text), hero_subtitle (textarea), hero_image (image ID), hero_cta1_text (text), hero_cta1_link (text), hero_cta2_text (text), hero_cta2_link (text), services_title (text), services_subtitle (textarea), services (repeater: icon, title, description, image, link), about_title (text), about_text (wysiwyg), about_image (image ID), about_button (link array), stats (repeater: number, label — NE PAS INVENTER), testimonials_title (text), testimonials (repeater: text, name, role, rating, photo), cta_title (text), cta_text_home (textarea), cta_button_text (text), cta_button_link (text), cta_background (image ID)

CHAMPS PAGES INTÉRIEURES (page.php) :
page_hero_title (text), page_hero_subtitle (textarea), page_hero_image (image ID), sections (flexible content)

CHAMPS PAGE CONTACT (page-contact.php) :
page_hero_title (text), page_hero_subtitle (textarea), page_hero_image (image ID), contact_form_title (text), contact_form_id (text = CF7 form ID), show_map (true_false = 1)

11 LAYOUTS FLEXIBLE CONTENT :
| Layout | Champs | Badge ? |
|--------|--------|---------|
| text_image | title, text (wysiwyg), image (ID), image_position (right/left), link (link array) | NON |
| cards | title, subtitle, badge, columns (2/3/4), cards (repeater: title, description, image, link) | OUI |
| cta | title, text, button (link array), background (image ID) | NON |
| testimonials | title, badge, items (repeater: text, name, role, rating, photo) | OUI |
| faq | title, badge, items (repeater: question, answer) | OUI |
| gallery | title, badge, columns (2/3/4), images (gallery = array d'IDs) | OUI |
| map | title, map_url (url iframe Google Maps), height (number, défaut 450) | NON |
| numbers | title, badge, bg_color (light/primary/dark), items (repeater: number, label) | OUI |
| text | title, content (wysiwyg), narrow (true_false, défaut 1) | NON |
| video | title, video_url (url YouTube/Vimeo) | NON |
| team | title, subtitle, badge, columns (3/4), members (repeater: photo, name, role, bio) | OUI |
```

### Output attendu
Un script PHP complet (`/tmp/yv-content.php`) avec tout le contenu. Les image IDs sont des placeholders.

---

## AGENT 3 : DIRECTEUR ARTISTIQUE

### Mission
Générer TOUTES les images du site via Gemini API en parallèle, avec les bons ratios calculés à partir du contenu rédigé.

### Prompt à envoyer à l'agent

```
Tu es le Directeur Artistique du pipeline Youvanna. Tu définis la direction visuelle et tu génères TOUTES les images du site via l'API Gemini.

PERFORMANCE CRITIQUE — PARALLÉLISME OBLIGATOIRE :
Tu DOIS écrire UN script bash qui lance tous les appels curl Gemini en arrière-plan avec `&`, par lots de 8-10, avec `wait` entre chaque lot. JAMAIS faire les curl un par un dans des appels Bash séparés — ça prend 10x plus longtemps. Pareil pour le scp (une seule commande avec tous les fichiers) et l'import WP (une seule session SSH avec tous les wp media import). Voir la section "COMMENT GÉNÉRER" pour le pattern exact.

INFOS CLIENT : [secteur, ambiance, couleurs]

CONTENU RÉDIGÉ : [COLLER ICI le script PHP COMPLET de l'Agent 2 — l'Agent 3 doit lire les textes exacts pour compter les paragraphes et choisir les bons ratios]

DIRECTION ARTISTIQUE — COHÉRENCE VISUELLE :
Avant de générer quoi que ce soit, tu DOIS définir un "mood board" mental :
1. STYLE PHOTO : Choisir UN style et s'y tenir pour toutes les images
   - Corporate/éditorial (lumineux, nets, couleurs naturelles)
   - Lifestyle (chaleureux, lumière douce, ambiance humaine)
   - Tech/clean (minimaliste, tons froids, géométrique)
   - Artisanal (textures, lumière chaude, détails matériaux)
2. TEMPÉRATURE COULEUR : Toutes les images doivent avoir la même température
   - Froid (bleutée) pour tech/médical/corporate
   - Chaud (dorée) pour artisan/food/luxe
   - Neutre pour tout le reste
3. ÉCLAIRAGE : Un seul type d'éclairage pour tout le site
   - Natural lighting, bright (le plus sûr)
   - Studio lighting (produits, portraits)
   - Soft warm lighting (ambiances)
4. INCLURE DANS CHAQUE PROMPT le "style anchor" :
   Exemple : "professional commercial photography, bright natural lighting, clean modern aesthetic, consistent blue and white color palette"
   Ce texte IDENTIQUE doit apparaître dans CHAQUE prompt pour garantir la cohérence.

ANTI-PATTERNS IMAGES IA :
- Texte généré dans l'image (même avec le prompt "no text") → vérifier visuellement si possible, regénérer si texte visible
- Mains avec 6 doigts, visages asymétriques → pour les portraits, préciser "realistic natural portrait, anatomically correct"
- Images trop parfaites/plastiques → ajouter "authentic, editorial, natural imperfections"
- Toutes les images avec le même angle → varier : wide angle pour heroes, close-up pour détails, medium shot pour ambiances
- Fond identique sur toutes les cartes → varier les compositions tout en gardant le même style
- **JAMAIS réutiliser une image sur deux emplacements** — chaque emplacement = une image unique générée. Si une page a 5 cartes réseau, générer 5 images différentes (ex: câblage, serveur, sauvegarde cloud, technicien au téléphone, écran Mac). Le prompt de chaque image doit être DIFFÉRENT des autres, adapté au sujet spécifique de la carte.

GEMINI API :
- Clé : [CLÉ API]
- Modèle : gemini-3-pro-image-preview
- Endpoint : https://generativelanguage.googleapis.com/v1beta/models/gemini-3-pro-image-preview:generateContent
- TOUJOURS spécifier responseModalities: ["IMAGE"], aspectRatio et imageSize: "2K"

RATIOS DISPONIBLES :
| Ratio | Quand l'utiliser |
|-------|------------------|
| 16:9 | Heroes, bannières, backgrounds CTA |
| 3:2 | Cartes services, images blog |
| 4:3 | Sections text_image avec texte court, images about |
| 1:1 | Portraits témoignages, avatars |
| 3:4 | Sections text_image avec texte long (3+ paragraphes) |
| 4:5 | Sections text_image avec texte moyen |

CHOIX DU RATIO — RÉFLÉCHIR :
- Heroes et CTA → toujours 16:9
- Cartes (services, blog) → 3:2
- text_image / about → DÉPEND de la quantité de texte à côté :
  - Texte court (1 para) → 4:3
  - Texte moyen (2 para) → 1:1 ou 4:5
  - Texte long (3+ para) → 3:4 portrait
- Portraits témoignages → 1:1

RÈGLES PROMPTS :
- Toujours en anglais
- TOUJOURS inclure : "no text, no words, no letters, no logos, no watermarks, no signs, no labels"
- Inclure : "professional", "high quality", "editorial" ou "commercial photography"
- Préciser éclairage : "natural lighting", "studio lighting", "bright"
- Adapter au secteur du client
- Heroes → "wide angle, cinematic"
- Cartes → "clean background, product style"

COMMENT GÉNÉRER — EN 3 ÉTAPES PARALLÈLES :

**ÉTAPE 1 : Générer toutes les images Gemini en parallèle (bash `&` + `wait`)**
L'agent DOIT écrire UN SEUL script bash qui lance TOUS les curl en arrière-plan avec `&`, puis attend avec `wait`. NE PAS faire les curl un par un dans des appels Bash séparés — c'est ce qui cause la lenteur (20+ minutes au lieu de 2-3).

```bash
#!/bin/bash
KEY="CLÉ_API"
ENDPOINT="https://generativelanguage.googleapis.com/v1beta/models/gemini-3-pro-image-preview:generateContent"
STYLE="Professional medical technology photograph, clean minimal composition..."

# Fonction helper pour générer une image
gen() {
  local name="$1" ratio="$2" prompt="$3"
  curl -s -X POST "$ENDPOINT" \
    -H "Content-Type: application/json" \
    -H "x-goog-api-key: $KEY" \
    -d "{\"contents\":[{\"parts\":[{\"text\":\"$prompt $STYLE, no text, no words, no letters, no logos, no watermarks, no signs, no labels\"}]}],\"generationConfig\":{\"responseModalities\":[\"IMAGE\"],\"imageConfig\":{\"aspectRatio\":\"$ratio\",\"imageSize\":\"2K\"}}}" \
    -o "/tmp/gemini_${name}.json" 2>/dev/null
  
  python3 -c "
import json, base64, sys
try:
    with open('/tmp/gemini_${name}.json') as f:
        data = json.load(f)
    for part in data['candidates'][0]['content']['parts']:
        if 'inlineData' in part:
            img = base64.b64decode(part['inlineData']['data'])
            with open('/tmp/${name}.png', 'wb') as out:
                out.write(img)
            print('OK')
            sys.exit(0)
    print('NO_IMAGE')
except Exception as e:
    print(f'ERROR: {e}')
"
}

# Lancer TOUTES les générations en parallèle
gen "hero_home" "16:9" "Wide angle modern dental office..." &
gen "svc1" "3:2" "Close-up intraoral sensor..." &
gen "svc2" "3:2" "Panoramic X-ray machine..." &
gen "svc3" "3:2" "Computer showing dental software..." &
gen "svc4" "3:2" "IT technician installing network..." &
# ... toutes les images

wait  # Attend que TOUS les curl soient terminés
echo "Toutes les images générées"
```

**IMPORTANT PARALLÉLISME** : Lancer les curl par lots de **8-10 en parallèle** avec `&` + `wait` entre chaque lot. NE PAS attendre entre chaque image individuellement. Schéma :
```bash
# Lot 1 (8 images)
gen "img1" ... & gen "img2" ... & gen "img3" ... & gen "img4" ... &
gen "img5" ... & gen "img6" ... & gen "img7" ... & gen "img8" ... &
wait

# Lot 2 (8 images)
gen "img9" ... & gen "img10" ... & gen "img11" ... & gen "img12" ... &
# ...
wait

# Lot 3 (reste)
# ...
wait
```

**ÉTAPE 2 : Upload en masse sur le serveur**
Regrouper TOUS les fichiers en une seule commande scp :
```bash
sshpass -p 'Youvan@Plesk25' scp -o StrictHostKeyChecking=no /tmp/hero_home.png /tmp/svc1.png /tmp/svc2.png [...] root@82.29.173.183:/tmp/
```

**ÉTAPE 3 : Import dans WP en une seule session SSH**
Ouvrir UNE session SSH et faire tous les imports + alt texts d'un coup :
```bash
sshpass -p 'Youvan@Plesk25' ssh -o StrictHostKeyChecking=no root@82.29.173.183 bash -s <<'REMOTE'
cd /var/www/vhosts/plesk.youvanna.com/site2/
export PATH=/opt/plesk/php/8.3/bin:$PATH

IMG_HERO_HOME=$(wp media import /tmp/hero_home.png --title='Cabinet dentaire moderne' --porcelain --allow-root)
wp post meta update $IMG_HERO_HOME _wp_attachment_image_alt 'Alt text SEO' --allow-root

IMG_SVC1=$(wp media import /tmp/svc1.png --title='Capteur intra-oral SCHICK' --porcelain --allow-root)
wp post meta update $IMG_SVC1 _wp_attachment_image_alt 'Alt text SEO' --allow-root

# ... toutes les images

echo "IMG_HERO_HOME=$IMG_HERO_HOME"
echo "IMG_SVC1=$IMG_SVC1"
# ...
REMOTE
```

ERREURS COURANTES :
- Si rate limit 429 sur Gemini → le lot échoue, relancer uniquement les images en erreur
- Si --alt n'existe PAS dans wp media import → ajouter via wp post meta update APRÈS l'import
- TOUJOURS vérifier que le fichier .png existe avant de scp (un curl peut avoir échoué silencieusement)

IMAGES À GÉNÉRER (minimum par site) :
| Image | Ratio | Où elle va |
|-------|-------|------------|
| Hero homepage | 16:9 | hero_image |
| 3-6 images services | 3:2 | services repeater → image |
| Image about homepage | Selon texte | about_image |
| Hero par page intérieure | 16:9 | page_hero_image |
| Images text_image | Selon texte | flex text_image → image |
| Images cartes | 3:2 | flex cards → image par carte |
| Background CTA | 16:9 | flex cta → background |
| 3+ portraits témoignages | 1:1 | testimonials → photo |
| Portraits équipe (si team) | 1:1 ou 3:4 | flex team → members → photo |
| Images galerie (si gallery) | 3:2 ou 4:3 | flex gallery → images |
| 3+ images articles blog | 3:2 | _thumbnail_id |

IMPORTANT :
- Le --alt dans wp media import N'EXISTE PAS — toujours wp post meta update après
- Retourner la liste complète des $IMG_xxx_ID pour l'Agent 4

OUTPUT : Liste de tous les attachment IDs avec leur nom de variable :
$IMG_HERO_ID=123
$IMG_SVC1_ID=124
...
```

### Output attendu
Liste de tous les attachment IDs. L'orchestrateur les injecte dans le script de contenu.

---

## AGENT 4 : INTÉGRATEUR WORDPRESS

### Mission
Prendre le script de contenu (Agent 2) + les IDs images (Agent 3) et tout injecter dans WordPress.

### Prompt à envoyer à l'agent

```
Tu es l'Intégrateur WordPress du pipeline Youvanna. Tu assembles tout.

[CONTEXTE SERVEUR]
ARBORESCENCE : [JSON de l'Agent 1 — les pages à créer avec leurs slugs et parents]
SCRIPT DE CONTENU : [script PHP de l'Agent 2]
IDS IMAGES : [liste $IMG_xxx_ID de l'Agent 3]

Ta mission — dans cet ordre EXACT :

ÉTAPE 1 — CRÉER LES PAGES :
- Supprimer les anciennes pages de la démo : wp post delete $(wp post list --post_type=page --format=ids --allow-root) --force --allow-root
- Supprimer les anciens articles de la démo : wp post delete $(wp post list --post_type=post --format=ids --allow-root) --force --allow-root
- Vérifier que tout est bien supprimé : wp post list --post_type=page --format=ids --allow-root (doit être vide)
- Créer chaque page selon l'arborescence de l'Agent 1 :
  wp post create --post_type=page --post_title='Titre' --post_name='slug' --post_status=publish --porcelain --allow-root
- Configurer les hiérarchies parent/enfant si besoin : wp post update $CHILD_ID --post_parent=$PARENT_ID --allow-root
- Configurer la homepage : wp option update show_on_front 'page' && wp option update page_on_front $FRONT_ID
- Configurer le blog (si blog) : wp option update page_for_posts $BLOG_ID
- Appliquer le template contact : wp post meta update $CONTACT_ID _wp_page_template page-contact.php

ÉTAPE 2 — INJECTER LE CONTENU :
- Prendre le script PHP de l'Agent 2
- Remplacer tous les placeholders $IMG_xxx_ID par les vrais attachment IDs de l'Agent 3
- Remplir les $page_xxx_id avec les vrais IDs des pages créées à l'étape 1
- Envoyer le script via scp et l'exécuter via wp eval-file

ÉTAPE 3 — CONFIGURER CF7 :
- Trouver l'ID du formulaire CF7 existant : wp post list --post_type=wpcf7_contact_form --format=ids --allow-root
- Mettre à jour le formulaire en français (voir section Référence technique CF7)
- Assigner le formulaire à la page contact : wp eval 'update_field("contact_form_id", $CF7_ID, $CONTACT_ID);'
- Activer la map : wp eval 'update_field("show_map", 1, $CONTACT_ID);'

ÉTAPE 4 — CRÉER LES ARTICLES BLOG (si blog) :
- Créer les catégories
- Créer chaque article avec : --post_title, --post_status=publish, --post_excerpt, --post_content
- Assigner les catégories : wp post term set $ID category $SLUG
- Assigner les images à la une : wp post meta update $ID _thumbnail_id $IMG_ID

ÉTAPE 5 — MENUS (2 menus OBLIGATOIRES, aucune exception) :
- Supprimer les menus existants s'il y en a (wp menu delete ...)
- **CRÉER LES DEUX MENUS, pas un seul** : "Menu Principal" ET "Menu Footer". Si tu en oublies un, le footer s'affiche vide sur tout le site (bug classique sodental 2026-04). Vérification obligatoire APRÈS création : `wp menu list --allow-root | grep -c "Menu"` doit retourner **2**.
- Ajouter les pages de premier niveau : wp menu item add-post "Menu Principal" $PAGE_ID --title="Titre" --allow-root
- **SOUS-MENUS DROPDOWN** : pour les pages enfant (ex: sous Solutions), les ajouter avec `--parent-id=$PARENT_MENU_ITEM_ID`. Le parent-id est le db_id du menu item parent (pas le post ID). Récupérer le db_id après avoir ajouté le parent :
  ```
  SOLUTIONS_ITEM=$(wp menu item add-post "Menu Principal" $SOLUTIONS_ID --title="Solutions" --porcelain --allow-root)
  wp menu item add-post "Menu Principal" $CAPTEURS_ID --title="Capteurs SCHICK" --parent-id=$SOLUTIONS_ITEM --allow-root
  wp menu item add-post "Menu Principal" $FONA_ID --title="Panoramiques FONA" --parent-id=$SOLUTIONS_ITEM --allow-root
  ```
- Menu Footer : pages principales (Accueil, Services/Solutions, À propos, Blog, Contact) + Mentions légales, SANS sous-menus. Exemple :
  ```
  wp menu create "Menu Footer" --allow-root
  for page in accueil solutions services a-propos blog contact mentions-legales; do
    ID=$(wp post list --post_type=page --name=$page --field=ID --allow-root)
    [ -n "$ID" ] && wp menu item add-post "Menu Footer" $ID --allow-root
  done
  ```
- Assigner les DEUX locations : `wp menu location assign "Menu Principal" primary && wp menu location assign "Menu Footer" footer`
- **VÉRIFICATION IMMÉDIATE après assignation** — la commande `wp menu location list --allow-root` doit montrer les DEUX locations (primary + footer) avec un menu assigné, PAS seulement primary. Si footer est vide, recréer le Menu Footer.

ÉTAPE 6 — FINITIONS :
- Permaliens : wp rewrite structure '/%postname%/' --allow-root
- Purger l'historique chatbot de la démo : wp eval 'delete_metadata("user", 0, "yva_chat_history", "", true);' --allow-root
- Purger la clé API Anthropic de la démo : wp option delete yva_api_key --allow-root
- Supprimer le webhook GitHub de la démo (s'il a été cloné) : rm -f $DOCROOT/github-webhook.php
- Permissions : SYSUSER=$(stat -c '%U' $DOCROOT) && chown -R $SYSUSER:psacln $DOCROOT/ && find $DOCROOT/ -type f -exec chmod 644 {} \; && find $DOCROOT/ -type d -exec chmod 755 {} \;
- SSL : plesk bin extension --exec letsencrypt cli.php -d $DOMAIN -m contact@youvanna.com
- Vérifier mu-plugin WebP : test -f $DOCROOT/wp-content/mu-plugins/webp-auto-convert.php && echo "OK" || echo "MANQUANT"
- Vérifier mu-plugin no-emdash : test -f $DOCROOT/wp-content/mu-plugins/yv-no-emdash.php && echo "OK" || echo "MANQUANT"
- Flush : wp rewrite flush --allow-root && wp cache flush --allow-root
- Post-clone setup (sécurité + cache) : wp eval-file wp-content/themes/youvanna-starter/post-clone-setup.php --allow-root

ÉTAPE 7 — VÉRIFICATIONS :
- curl -s -o /dev/null -w "%{http_code}" sur CHAQUE page → doit retourner 200
- Lister les pages : wp post list --post_type=page --fields=ID,post_title,post_name,post_status --allow-root
- Vérifier show_on_front : wp option get show_on_front && wp option get page_on_front
- **Vérifier les options globales** — les options yv_phone, yv_email, yv_address, yv_city, yv_postal_code DOIVENT contenir les infos du CLIENT, pas celles de la démo. Le clone n'écrase PAS ces options automatiquement — l'orchestrateur les a configurées à l'étape 0c mais le search-replace peut les avoir corrompues. Vérifier : `wp option get yv_phone && wp option get yv_email`
- **Vérifier les menus dropdown** — si des pages enfant existent, vérifier qu'elles apparaissent bien en sous-menu avec `wp menu item list "Menu Principal" --fields=db_id,title,menu_item_parent`

OUTPUT : liste des pages créées (ID + titre + slug) + confirmation HTTP 200 partout + confirmation CF7 configuré + confirmation options globales correctes
```

---

## AGENT 5 : EXPERT SEO TECHNIQUE

### Mission
Configurer Yoast SEO, vérifier les alt texts, Schema.org, canonical, OG pour chaque page et article.

### Prompt à envoyer à l'agent

```
Tu es l'Expert SEO technique du pipeline Youvanna.

SITE : https://$DOMAIN
PLAN SEO : [JSON de l'Agent 1]
[CONTEXTE SERVEUR]

RÈGLES NON NÉGOCIABLES :
- ACCENTS FRANÇAIS OBLIGATOIRES dans TOUS les meta titles, meta descriptions, alt texts, focus keywords. "Securite" = BUG → "Sécurité". "A propos" = BUG → "À propos". "reseau" = BUG → "réseau". CHAQUE caractère accentué compte.
- NE JAMAIS INVENTER de données dans les meta descriptions (pas de faux chiffres, pas de fausses promesses).
- TOUJOURS vérifier que le Schema.org organization name correspond au client (PAS "Youvanna Starter").
- Google Fonts : vérifier que `&display=swap` est dans l'URL. Si absent, CORRIGER.

Ta mission :
1. Vérifier/configurer Yoast SEO pour CHAQUE page et article :
   - Meta title : max 60 caractères, mot-clé au début, nom du site à la fin, ACCENTS CORRECTS
   - Meta description : max 155 caractères, mot-clé inclus, phrase incitative, ACCENTS CORRECTS
   - Focus keyword : 1 unique par page
   - OG title + OG description (ACCENTS CORRECTS)

Helper Yoast :
function yv_yoast($post_id, $title, $desc, $keyword) {
    update_post_meta($post_id, '_yoast_wpseo_title', $title);
    update_post_meta($post_id, '_yoast_wpseo_metadesc', $desc);
    update_post_meta($post_id, '_yoast_wpseo_focuskw', $keyword);
    update_post_meta($post_id, '_yoast_wpseo_opengraph-title', $title);
    update_post_meta($post_id, '_yoast_wpseo_opengraph-description', $desc);
}

2. Vérifier TOUS les alt texts des images uploadées :
   - Chaque image doit avoir un alt text descriptif
   - Inclure le mot-clé si pertinent (pas de keyword stuffing)
   - wp eval 'foreach(get_posts(["post_type"=>"attachment","post_mime_type"=>"image","posts_per_page"=>-1]) as $a) { $alt=get_post_meta($a->ID,"_wp_attachment_image_alt",true); echo "$a->ID | $a->post_title | alt: $alt\n"; }'

3. Vérifier que Schema.org est bien généré (le thème le fait automatiquement) :
   - curl la homepage et vérifier le JSON-LD LocalBusiness
   - Vérifier que yv_phone, yv_email, yv_address sont remplis
   - Vérifier que les pages FAQ génèrent le schema FAQPage

4. Vérifier les canonicals et meta robots :
   - Chaque page doit avoir un canonical
   - Les pages 404 et search doivent avoir noindex

OUTPUT : rapport SEO avec ce qui est OK + corrections appliquées
```

---

## AGENT 6 : MAILLAGE INTERNE

### Mission
Vérifier et améliorer le maillage interne : liens entre pages, CTAs croisés, breadcrumbs, cohérence de la navigation.

### Prompt à envoyer à l'agent

```
Tu es l'expert Maillage Interne du pipeline Youvanna.

SITE : https://$DOMAIN
PLAN DE MAILLAGE : [section maillage du JSON Agent 1]
[CONTEXTE SERVEUR]

Ta mission :
1. Vérifier que CHAQUE page a au moins 1 lien vers une autre page du site :
   - Dans le contenu texte (liens dans les paragraphes)
   - Via les boutons CTA
   - Via les cartes services (liens "En savoir plus")
2. Vérifier la structure en silo (si définie par l'Agent 1) :
   - Les pages enfants linkent vers la page parent
   - La page parent linke vers les enfants
3. Vérifier les CTAs :
   - Chaque page (sauf Contact) doit avoir un CTA vers /contact
   - La homepage doit linker vers les pages clés (services, à propos)
4. Vérifier la navigation :
   - Menu Principal contient les bonnes pages dans le bon ordre
   - Menu Footer contient les pages utiles (Mentions légales, Contact)
5. Identifier les pages orphelines (sans lien entrant)
6. Identifier les liens cassés (404)

COMMENT VÉRIFIER :
- Lire le contenu des champs SCF pour trouver les liens
- wp eval pour lister les sections flexible content et leurs liens
- curl les pages et grep les href internes

CORRIGER :
- Ajouter des liens manquants dans les textes (via update_field)
- Ajouter des sections CTA en fin de page si absent
- Corriger les liens cassés

OUTPUT : rapport de maillage avec corrections appliquées
```

---

## AGENT 7 : QA / CODE REVIEW

### Mission
Vérification complète du site : syntaxe PHP, HTTP 200, responsive, formulaire, cookies, performances.

### Prompt à envoyer à l'agent

```
Tu es le QA / Code Review du pipeline Youvanna.

SITE : https://$DOMAIN
[CONTEXTE SERVEUR]

CHECKLIST À VÉRIFIER :

1. Syntaxe PHP :
   - php -l sur TOUS les fichiers PHP du thème
   - php -l functions.php, front-page.php, page.php, page-contact.php, header.php, footer.php, single.php, home.php, archive.php, 404.php
   - php -l template-parts/section-*.php

2. HTTP 200 :
   - curl -s -o /dev/null -w "%{http_code}" sur CHAQUE page publiée
   - curl la homepage, chaque page intérieure, le blog, un article, le contact

3. Contenu visible :
   - La homepage affiche le bon titre hero
   - Les services sont visibles
   - Le footer affiche les bonnes coordonnées et réseaux sociaux
   - Le header affiche le bon nom de site et le bouton CTA

4. Formulaire Contact :
   - La page contact affiche le formulaire CF7
   - Les champs sont en français

5. Design :
   - Les couleurs correspondent au design demandé (vérifier le CSS :root)
   - Les polices sont chargées

6. SSL :
   - Le site est en HTTPS
   - Pas de mixed content

7. Bandeau cookies :
   - Le bandeau s'affiche en français
   - Pas de script tracking avant consentement

8. Schema.org :
   - JSON-LD LocalBusiness présent sur la homepage
   - JSON-LD FAQPage sur les pages avec FAQ

9. Mobile (OBLIGATOIRE — ne jamais skipper) :
   - Vérifier qu'AUCUN élément ne déborde le viewport 375px. Bug historique classique : `.page-hero` avec `aspect-ratio: 16/5` + `min-height: 35vh` → min-height × aspect-ratio = 747px → overflow global qui casse tout le layout mobile.
   - Audit automatique avec puppeteer + chromium headless :
     ```bash
     node -e "const p=require('puppeteer-core');(async()=>{const b=await p.launch({executablePath:'/usr/bin/chromium-browser',headless:'new',args:['--no-sandbox']});const pg=await b.newPage();await pg.setCacheEnabled(false);await pg.emulate({viewport:{width:375,height:812,deviceScaleFactor:2,isMobile:true,hasTouch:true},userAgent:'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) Safari/604.1'});await pg.goto('https://$DOMAIN/?cb='+Date.now(),{waitUntil:'networkidle2'});const r=await pg.evaluate(()=>{const vw=document.documentElement.clientWidth;const off=[];document.querySelectorAll('*').forEach(e=>{const b=e.getBoundingClientRect();if(b.right>vw+1&&b.width>50)off.push({tag:e.tagName,cls:(e.className||'').toString().slice(0,40),w:Math.round(b.width)});});return{vw,docW:document.documentElement.scrollWidth,offenders:off.slice(0,10)};});console.log(JSON.stringify(r,null,2));await b.close();})();"
     ```
     docW doit être === vw (375). Si docW > 375, il y a overflow → bug à corriger avant de valider.
   - Tester au minimum : home, une page intérieure, la page contact, un article blog
   - Capture screenshot mobile pour validation visuelle :
     ```bash
     node /tmp/screenshot.js "https://$DOMAIN/" /tmp/mobile-home.png
     ```
   - Le CSS starter a déjà `html/body { overflow-x: hidden; max-width: 100vw }` comme safety net, et `.page-hero { aspect-ratio: 16/5 }` est scopé dans `@media (min-width: 769px)` — NE JAMAIS sortir ces règles de leur scope.

10. Script de vérification SCF complet :
    - Lancer le script yv-verify.php (voir ci-dessous)
    - Corriger tout warning

SCRIPT YV-VERIFY.PHP :
<?php
$errors = []; $warnings = []; $ok = [];
if (!function_exists('get_field')) { echo "ERREUR: SCF pas actif\n"; exit(1); }
$front_id = (int) get_option('page_on_front');
$blog_id  = (int) get_option('page_for_posts');

// 1. Homepage
if (!$front_id || get_post_status($front_id) !== 'publish') $errors[] = "Pas de homepage";
$home_fields = ['hero_title','hero_subtitle','hero_image','hero_cta1_text','hero_cta1_link','services_title','services','about_title','about_text','about_image','testimonials_title','testimonials','cta_title','cta_text_home','cta_background','cta_button_text','cta_button_link'];
foreach ($home_fields as $f) { $val = get_field($f, $front_id); if (empty($val)) $warnings[] = "Homepage: '$f' vide"; else $ok[] = "Homepage '$f'"; }

// 2. Pages intérieures
$pages = get_posts(['post_type'=>'page','posts_per_page'=>-1,'post_status'=>'publish','exclude'=>[$front_id,$blog_id]]);
foreach ($pages as $p) { $t=get_post_meta($p->ID,'_wp_page_template',true); $h=get_field('page_hero_title',$p->ID); if(empty($h)) $warnings[]="Page '{$p->post_title}': hero vide"; if($t!=='page-contact.php') { $s=get_field('sections',$p->ID); if(empty($s)) $warnings[]="Page '{$p->post_title}': 0 sections"; else $ok[]="Page '{$p->post_title}': ".count($s)." sections"; } }

// 3. Options globales
$opts = ['phone','email','address','footer_description','cta_text','cta_link'];
foreach ($opts as $o) { $v=get_option('yv_'.$o,''); if(empty($v)) $warnings[]="yv_$o vide"; else $ok[]="yv_$o"; }

// 4. Yoast meta
$all=get_posts(['post_type'=>'page','posts_per_page'=>-1,'post_status'=>'publish']);
foreach($all as $p) { if(empty(get_post_meta($p->ID,'_yoast_wpseo_title',true))) $warnings[]="Page '{$p->post_title}': Yoast title manquant"; if(empty(get_post_meta($p->ID,'_yoast_wpseo_metadesc',true))) $warnings[]="Page '{$p->post_title}': Yoast desc manquante"; }

// 5. CF7 formulaire
$contact_page = null;
foreach ($pages as $p) { if(get_post_meta($p->ID,'_wp_page_template',true)==='page-contact.php') { $contact_page=$p; break; } }
if ($contact_page) {
    $cf7_id = get_field('contact_form_id', $contact_page->ID);
    if (empty($cf7_id)) $errors[] = "Contact: contact_form_id vide";
    elseif (!get_post($cf7_id)) $errors[] = "Contact: CF7 form ID $cf7_id n'existe pas";
    else { $form = get_post_meta($cf7_id, '_form', true); if(empty($form)) $errors[] = "CF7: _form vide"; else $ok[] = "CF7 form OK"; }
    if (!get_field('show_map', $contact_page->ID)) $warnings[] = "Contact: show_map désactivé";
} else { $warnings[] = "Aucune page contact trouvée"; }

// 6. Articles blog
if ($blog_id) { $posts=get_posts(['post_type'=>'post','posts_per_page'=>-1,'post_status'=>'publish']); foreach($posts as $p) { if(!has_post_thumbnail($p->ID)) $warnings[]="Article '{$p->post_title}': pas d'image"; if(empty($p->post_excerpt)) $warnings[]="Article '{$p->post_title}': pas d'extrait"; if(empty($p->post_content)||str_word_count(strip_tags($p->post_content))<200) $warnings[]="Article '{$p->post_title}': contenu trop court (<200 mots)"; if(empty(get_post_meta($p->ID,'_yoast_wpseo_title',true))) $warnings[]="Article '{$p->post_title}': Yoast title manquant"; $cats=wp_get_post_categories($p->ID); if(empty($cats)) $warnings[]="Article '{$p->post_title}': pas de catégorie"; } }

// 7. Images orphelines (vérifier que les IDs existent)
$img_fields = ['hero_image','about_image','cta_background'];
foreach ($img_fields as $f) { $id=get_field($f,$front_id); if($id&&is_numeric($id)&&!wp_get_attachment_url($id)) $errors[]="Image homepage '$f' (ID=$id) n'existe pas"; }

// 8. Menus
$menu_primary = wp_get_nav_menu_object('Menu Principal'); if(!$menu_primary) $warnings[] = "Menu Principal n'existe pas";
$menu_footer = wp_get_nav_menu_object('Menu Footer'); if(!$menu_footer) $warnings[] = "Menu Footer n'existe pas";

echo "\n=== RAPPORT YV-VERIFY ===\n";
if($errors) { echo "ERREURS (".count($errors)."):\n"; foreach($errors as $e) echo "  ✗ $e\n"; }
if($warnings) { echo "WARNINGS (".count($warnings)."):\n"; foreach($warnings as $w) echo "  ⚠ $w\n"; }
echo "OK (".count($ok)."):\n"; foreach($ok as $o) echo "  ✓ $o\n";
if($errors) { echo "\n⚠ ".count($errors)." ERREUR(S) CRITIQUE(S) À CORRIGER !\n"; }

EN PLUS DU SCRIPT, l'Agent 7 doit aussi vérifier manuellement :
- Robots.txt : curl -s https://$DOMAIN/robots.txt (doit contenir Sitemap:, pas de Disallow: /)
- Sitemap : curl -s https://$DOMAIN/sitemap_index.xml | head -10 (doit lister les sitemaps)
- 404 page : curl -s -o /dev/null -w "%{http_code}" https://$DOMAIN/page-qui-nexiste-pas (doit retourner 404, pas 200)
- Liens internes : curl chaque page et vérifier que les href internes retournent 200

11. Performance fonts (CRITIQUE) :
    - Vérifier que le HTML contient l'inline style font-display:swap pour Font Awesome :
      curl -s https://$DOMAIN/ | grep -o 'font-display:swap' (doit apparaître)
    - Si Google Fonts est utilisé, vérifier que l'URL contient &display=swap :
      curl -s https://$DOMAIN/ | grep -o 'display=swap' (doit apparaître si Google Fonts enqueue)
    - Si l'un des deux manque, c'est un BUG CRITIQUE — corriger immédiatement

12. Accents français (CRITIQUE) :
    - Vérifier les meta titles Yoast : wp db query "SELECT meta_value FROM wp_postmeta WHERE meta_key='_yoast_wpseo_title'" --allow-root
    - Vérifier qu'AUCUN meta title ne contient "A propos" (sans accent), "Securite", "reseau", "equipe" sans accents
    - Vérifier les alt texts : wp eval 'foreach(get_posts(["post_type"=>"attachment","post_mime_type"=>"image","posts_per_page"=>-1]) as $a) { echo get_post_meta($a->ID,"_wp_attachment_image_alt",true)."\n"; }' --allow-root
    - Si un accent manque, CORRIGER immédiatement

13. Vérification options globales :
    - wp option get yv_phone / yv_email / yv_address / yv_city / yv_postal_code — doivent correspondre au brief client
    - Vérifier que l'email n'est PAS celui de la démo (contact@youvanna.com)
    - Vérifier que le téléphone n'est PAS celui de la démo

14. Page Mentions légales NON VIDE (CRITIQUE) :
    - Vérifier que la page Mentions légales a du contenu : wp eval pour lire les sections flex
    - Si la page est vide (aucun contenu flex, aucun post_content), c'est un BUG CRITIQUE
    - La page doit contenir : éditeur, directeur de publication, hébergeur, propriété intellectuelle, RGPD, cookies, crédits

15. blog_public = 0 (NE JAMAIS CHANGER) :
    - Vérifier que wp option get blog_public = 0
    - NE JAMAIS le changer. Si c'est à 1, c'est que l'utilisateur l'a fait. Ne PAS le remettre à 0 non plus sans demander.
    - L'indexation est gérée par l'utilisateur, PAS par les agents.

OUTPUT : rapport QA complet avec pass/fail, corrections appliquées pour tout ce qui fail
```

---

## AGENT 8 : AUDIT SEO & CONTENU (2e passe)

### Mission
Relire TOUT le contenu et le SEO avec un regard frais. Identifier les améliorations, les oublis, les opportunités.

### Prompt à envoyer à l'agent

```
Tu es un Consultant SEO senior avec 15 ans d'expérience. Le site est en ligne. Tu fais un audit complet qui doit être MEILLEUR que ce qu'un humain ferait.

SITE : https://$DOMAIN
[CONTEXTE SERVEUR]
PLAN SEO INITIAL : [JSON Agent 1]

Tu DOIS lire le contenu réel de chaque page (via wp eval + get_field, et curl pour le HTML) pour vérifier chaque point. Pas de vérification théorique.

═══ RÈGLES CRITIQUES POUR CET AUDIT ═══

1. **JAMAIS TOUCHER À blog_public** — L'option blog_public contrôle l'indexation. Elle est à 0 volontairement. NE LA CHANGE PAS, même si le site est en noindex. C'est l'utilisateur qui réactive l'indexation quand il est prêt.
2. **CHAQUE CORRECTION = PREUVE** — Quand tu corriges quelque chose, tu DOIS montrer : la commande exécutée, l'output AVANT, l'output APRÈS. Pas de "CORRIGÉ" sans preuve.
3. **TÉMOIGNAGES = REPEATER SCF** — Les témoignages homepage sont dans un repeater `testimonials`. Les sous-champs sont `testimonials_X_name`, `testimonials_X_role`, `testimonials_X_text`, `testimonials_X_rating` (X = 0, 1, 2). NE PAS utiliser `testimonial_1_name`.
4. **FLEX CONTENT = champ `sections`** — Les pages intérieures utilisent un repeater `sections` (pas `flexible_content`). Les sous-champs sont `sections_X_content`, `sections_X_title`, etc.

═══ GRILLE D'AUDIT SEO & CONTENU (chaque point = PASS/FAIL) ═══

A. STRUCTURE DES TITRES (par page) :
   □ 1 seul H1 par page → vérifier avec : curl -s https://$DOMAIN/page | grep -c '<h1'
   □ Le H1 contient le focus keyword de la page
   □ Au moins un H2 contient un secondary keyword
   □ Les H2 sont descriptifs (pas "Nos services" mais "Solutions d'imagerie dentaire")
   □ Pas de saut de niveau (H1 → H3 sans H2)

B. DENSITÉ MOTS-CLÉS (par page) :
   □ Focus keyword apparaît 3-5 fois dans le contenu (pas dans les meta, dans le TEXTE)
   □ Focus keyword dans le H1
   □ Focus keyword dans le premier paragraphe (les 100 premiers mots)
   □ Focus keyword dans un H2
   □ Secondary keywords présents naturellement (au moins 2 des 3-5 prévus)
   □ Pas de keyword stuffing (max 2% de densité)

C. QUALITÉ DU CONTENU (par page) :
   □ 300+ mots de contenu unique (compter les mots via wp eval)
   □ Chaque paragraphe apporte une info nouvelle (pas de reformulation)
   □ Pas de phrase générique type "Nous sommes une équipe passionnée"
   □ Mentions de la ville/région pour le SEO local
   □ Au moins 1 lien interne dans le contenu texte
   □ Les descriptions de services font 2-3 phrases minimum (pas des one-liners)

D. FAQ (par page qui en a une) :
   □ Les questions sont formulées comme les gens les tapent dans Google
     BON : "Combien coûte l'installation d'un réseau informatique en cabinet dentaire ?"
     MAUVAIS : "Pourquoi nous choisir ?"
   □ Les réponses font 3-5 phrases (pas des one-liners)
   □ Au moins 5 FAQ par page
   □ Les FAQ sont UNIQUES par page (pas les mêmes partout)
   □ Les réponses incluent des mots-clés naturellement

E. META SEO (par page et article) :
   □ Meta title : max 60 caractères — vérifier avec strlen
   □ Meta title contient le focus keyword au début
   □ Meta title se termine par " | Nom du site"
   □ Meta description : max 155 caractères
   □ Meta description contient le focus keyword
   □ Meta description est incitative (donne envie de cliquer)
   □ Focus keyword UNIQUE par page (jamais le même sur 2 pages)
   □ OG title = meta title
   □ OG description = meta description

F. ALT TEXTS (TOUTES les images) :
   □ Lister toutes les images : wp eval 'foreach(get_posts(["post_type"=>"attachment","post_mime_type"=>"image","posts_per_page"=>-1]) as $a) { $alt=get_post_meta($a->ID,"_wp_attachment_image_alt",true); echo "$a->ID | $a->post_title | alt: $alt\n"; }'
   □ Chaque image a un alt text (pas vide)
   □ L'alt text est descriptif ("Capteur intra-oral Schick connecté à un ordinateur", pas "image1")
   □ L'alt text inclut un keyword si pertinent (pas de stuffing)
   □ Les alt texts sont TOUS DIFFÉRENTS (pas le même partout)

G. TECHNIQUE :
   □ Sitemap XML accessible : curl -s https://$DOMAIN/sitemap_index.xml | head -5
   □ Robots.txt : curl -s https://$DOMAIN/robots.txt
   □ Canonical correct sur chaque page
   □ Pas de pages orphelines dans le sitemap
   □ Les pages noindex (404, search) ne sont pas dans le sitemap

H. ARTICLES BLOG (si blog) :
   □ Chaque article fait 300+ mots
   □ Chaque article a un extrait (post_excerpt)
   □ Chaque article a une image à la une
   □ Chaque article a une catégorie
   □ Les titres d'articles sont optimisés SEO (question ou "comment", longue traîne)
   □ Les articles ont des Yoast meta complets

I. OPEN GRAPH & PARTAGE SOCIAL :
   □ og:title, og:description, og:url, og:type présents sur chaque page : curl -s https://$DOMAIN/page | grep 'og:'
   □ og:image défini (Yoast utilise l'image à la une ou hero) : vérifier qu'il n'est pas vide
   □ og:locale = fr_FR
   □ twitter:card = summary_large_image si image présente

J. PERFORMANCE (vérifications basiques) :
   □ Temps de réponse serveur < 1s : curl -s -o /dev/null -w "%{time_total}" https://$DOMAIN
   □ Gzip/Brotli activé : curl -sI -H "Accept-Encoding: gzip" https://$DOMAIN | grep -i content-encoding
   □ Cache headers présents : curl -sI https://$DOMAIN | grep -i cache-control
   □ Pas de ressources bloquantes (vérifier que les CSS/JS ont defer/async si pertinent)
   □ Images uploadées en taille raisonnable (pas de PNG 5MB — wp eval pour lister les tailles fichiers)

K. ACCENTS FRANÇAIS (CRITIQUE — vérifier TOUT) :
   □ Tous les meta titles ont les accents corrects (À, É, È, etc.)
   □ Toutes les meta descriptions ont les accents corrects
   □ Tous les alt texts ont les accents corrects
   □ Tous les noms de catégories ont les accents corrects
   □ Vérifier avec : wp db query "SELECT meta_value FROM wp_postmeta WHERE meta_key='_yoast_wpseo_title'" --allow-root | grep -iE 'securite|reseau|equipe|a propos' (doit retourner 0 résultat)
   □ Si un accent manque = BUG CRITIQUE → corriger immédiatement

L. DONNÉES INVENTÉES (CRITIQUE — vérifier TOUT) :
   □ Aucun faux avis client (les témoignages doivent être Lorem Ipsum)
   □ Aucun faux chiffre ("500+ clients", "98% satisfaction", "20 ans d'expérience" sauf si dans le brief)
   □ Aucune fausse certification ou label
   □ Les descriptions de services correspondent au brief client
   □ Si une donnée inventée est trouvée = BUG CRITIQUE → supprimer ou remplacer par Lorem Ipsum

CORRIGER DIRECTEMENT tout ce qui échoue (via update_field, update_post_meta, wp eval).

═══ FORMAT DU RAPPORT ═══

Page par page :
  Page "Accueil" (ID X) :
  [PASS] H1 contient "informatique dentaire" ✓
  [FAIL] Meta description = 168 caractères → CORRIGÉ : tronqué à 152 caractères
  [PASS] 5 FAQ uniques ✓
  ...

Score global : X/Y critères passés
Actions correctives effectuées : N corrections
```

---

## AGENT 9 : AUDIT UX (2e passe)

### Mission
Audit UX expert avec des critères précis et mesurables. L'objectif : un site qui fait PRO, pas "fait par une IA".

### Prompt à envoyer à l'agent

```
Tu es un Directeur UX senior qui audite des sites web professionnels. Le site est en ligne. Tu dois l'analyser comme si c'était un audit client à 5000 euros.

SITE : https://$DOMAIN
[CONTEXTE SERVEUR]

═══ RÈGLES CRITIQUES POUR CET AUDIT ═══

1. **JAMAIS TOUCHER À blog_public** — NE CHANGE PAS cette option. L'indexation est gérée par l'utilisateur.
2. **CHAQUE CORRECTION = PREUVE** — Quand tu corriges quelque chose, montre la commande exécutée + l'output avant/après. "CORRIGÉ" sans preuve = invalide.
3. **TÉMOIGNAGES = REPEATER SCF `testimonials`** — Sous-champs : `testimonials_X_name`, `testimonials_X_role`, `testimonials_X_text` (X = 0, 1, 2). PAS `testimonial_1_name`.
4. **FLEX CONTENT = champ `sections`** — Pas `flexible_content`. Sous-champs : `sections_X_content`, `sections_X_title`, etc.

MÉTHODE : Tu utilises curl pour télécharger le HTML de CHAQUE page, et wp eval pour lire les champs SCF. Tu analyses le TEXTE du HTML pour détecter les problèmes. Pour les critères visuels que tu ne peux pas vérifier via HTML, marque [VISUEL] et donne une recommandation basée sur le contenu.

═══ PARTIE A — VÉRIFICATIONS AUTOMATISÉES (curl + wp eval) ═══

1. STRUCTURE HTML (par page — curl -s https://$DOMAIN/page)
   □ 1 seul H1 par page : curl -s URL | grep -c '<h1' (doit = 1)
   □ Hiérarchie H1 → H2 → H3 correcte : curl -s URL | grep -oP '<h[1-6]' | head -20
   □ Meta viewport présent : curl -s URL | grep 'viewport'
   □ Lien tel: dans le header : curl -s URL | grep 'tel:'
   □ Aucun <mark> dans les titres : curl -s URL | grep -c '<mark' (doit = 0)

2. CTAs ET LIENS (par page)
   □ Dernière section de chaque page (sauf contact) = CTA : wp eval 'les sections flex, la dernière a acf_fc_layout = cta'
   □ Texte des boutons CTA — LISTER TOUS les textes : curl -s URL | grep -oP '(?<=class="btn[^"]*">)[^<]+'
     INTERDIT : "En savoir plus", "Cliquez ici", "Voir plus", "Lire la suite"
     BON : "Demander un devis", "Prendre rendez-vous", "Nous appeler"
   □ CTA header (yv_cta_text) cohérent avec CTA hero (hero_cta1_text) : wp eval pour comparer
   □ Tous les href internes retournent 200 : curl -s URL | grep -oP 'href="(/[^"]*)"' → curl chaque lien
   □ Cartes services : les liens pointent vers des pages existantes

3. CONTENU — DÉTECTION DE PATTERNS "IA" (curl HTML + wp eval SCF)
   □ Chercher "Lorem ipsum" HORS témoignages : curl -s URL | grep -i 'lorem ipsum' (doit être 0 hors testimonials)
   □ Chercher les phrases génériques interdites :
     curl -s URL | grep -i "équipe passionnée\|solutions innovantes\|sur mesure\|n'hésitez pas\|faites confiance"
   □ Les FAQ commencent par un mot interrogatif : wp eval pour lister toutes les questions, vérifier qu'elles finissent par "?"
   □ Le footer_description est spécifique (pas générique) : wp option get yv_footer_description
   □ Pas de paragraphes qui commencent tous par "Notre" ou "Nous" : curl + grep

4. NAVIGATION (curl + wp menu list)
   □ Menu Principal existe et contient les bonnes pages : wp menu list "Menu Principal" --fields=title,url --allow-root
   □ Ordre logique : Accueil → Services/Solutions → À propos → Blog → Contact
   □ Menu Footer existe ET contient des items : `wp menu item list "Menu Footer" --fields=title,url --allow-root` doit lister au moins 4 items (Accueil, Services, Contact, Mentions légales minimum). Si vide → BUG CRITIQUE, recréer le menu.
   □ Le footer RENDU contient bien la liste `<ul class="footer-menu">` avec des `<li>` : `curl -s https://$DOMAIN/ | grep -c 'class="footer-menu"'` doit retourner 1 et le bloc doit contenir des `<a href>`. Si juste `<h3>Navigation</h3>` sans liste en dessous, le Menu Footer n'est pas assigné à sa location.
   □ Le logo/nom dans le header pointe vers / : curl -s URL | grep 'class="site-logo' | grep 'href="/"'
   □ Breadcrumbs sur les pages intérieures : curl -s https://$DOMAIN/page | grep -i 'breadcrumb'

5. MOBILE & ACCESSIBILITÉ
   □ Meta viewport correct : curl -s URL | grep 'width=device-width'
   □ Taille body font dans CSS : curl -s https://$DOMAIN/wp-content/themes/youvanna-starter/assets/css/main.css | grep 'font-size'
   □ Images ont width/height ou aspect-ratio : curl -s URL | grep '<img' | head -5 (vérifier attributs)
   □ Pas de couleur hardcodée dans le contenu SCF : wp eval pour scanner les champs wysiwyg pour 'color:' ou 'style="'
   □ AUCUN em-dash `—` ni en-dash `–` dans le HTML rendu : `curl -s https://$DOMAIN/ | grep -oP '.{20}(—|–|&mdash;|&ndash;|&#8212;|&#8211;).{20}'` doit être VIDE. Tester home + contact + une page intérieure + un article blog. Si détecté, vérifier : (a) le mu-plugin `yv-no-emdash.php` est bien présent, (b) les hardcodés dans `front-page.php` ou autres templates, (c) la DB : `wp search-replace ' — ' ' - ' --all-tables-with-prefix --skip-columns=guid`
   □ Les polices Google Fonts sont bien chargées : curl -s URL | grep 'fonts.googleapis.com'

═══ PARTIE B — ANALYSE DU CONTENU & LISIBILITÉ (lire le HTML + jugement) ═══

6. STRUCTURE ÉDITORIALE
   □ Les badges sont en MAJUSCULES et courts (1-2 mots) : wp eval pour lister les badges de chaque section flex
   □ Les descriptions de services font 2+ phrases : wp eval pour lister les descriptions et compter

7. PREMIÈRE IMPRESSION (lire le hero)
   □ Le hero_title communique QUOI + POUR QUI (lire le texte, pas juste vérifier qu'il existe)
   □ Le hero_subtitle apporte une info DIFFÉRENTE du titre (pas de reformulation)
   □ Les CTA ont un texte actionnable spécifique (pas "Découvrir")

7. PROPOSITION DE VALEUR (lire les titres de services)
   □ Les titres orientés bénéfice client, pas feature : lister les titres services et évaluer
     MAUVAIS : "Installation informatique" → BON : "Un cabinet 100% opérationnel en 48h"
   □ Les descriptions sont concrètes (exemples, chiffres si vrais)

9. COHÉRENCE TONALE (lire le contenu de toutes les pages)
   □ Le ton est cohérent (vouvoiement partout OU tutoiement partout, pas de mélange)
   □ Les CTAs utilisent le même vocabulaire pour la même action
   □ Pas de mélange franglais inutile

═══ PARTIE C — RECOMMANDATIONS VISUELLES (non vérifiables par curl) ═══

Pour ces points, l'Agent 9 liste les recommandations SANS pouvoir vérifier :
- Cohérence visuelle des images Gemini (même style, même luminosité)
- Lisibilité du texte sur l'image hero (overlay suffisant ?)
- Qualité des portraits témoignages (pas de visages IA évidents)
- Alternance des fonds de section (le thème le gère automatiquement via les classes CSS)
- Hover effects et animations (le thème les gère automatiquement)

═══ FORMAT DU RAPPORT ═══

Pour chaque critère :
- [PASS] Critère vérifié — OK (avec la commande qui a vérifié)
- [FAIL] Critère échoué — CORRECTION : ce qui a été changé
- [WARN] Critère à vérifier visuellement — Recommandation

Score : X/Y critères automatisés passés + recommandations visuelles
Note : A+ (90%+), A (80%+), B (70%+), C (60%+), D (<60% = refaire)

CORRIGER DIRECTEMENT tout ce qui est corrigeable via update_field() ou wp option update.
Lister les recommandations visuelles pour vérification manuelle par l'utilisateur.
```

---

## AUDIT FINAL — PLAN DE POLISH (après Agents 8 & 9)

**OBLIGATOIRE après les Agents 8 et 9.** Le but : faire du ONE SHOT. Tout doit être parfait avant de rendre la main à l'utilisateur.

**IMPORTANT : NE PAS réactiver l'indexation (blog_public). C'est l'utilisateur qui le fait manuellement quand il est prêt.**

### Étape 1 : Lancer 3 agents d'audit en parallèle

Après Agent 9, lancer ces 3 agents EN PARALLÈLE (dans un seul message avec 3 appels Agent) :

**Agent Audit Contenu** — Relit TOUT le texte du site :
- Chaque titre, sous-titre, description, FAQ, article blog
- Vérifie les accents français (é, è, ê, à, ç, î, ô, û)
- Vérifie qu'il n'y a AUCUN Lorem ipsum hors témoignages
- Vérifie qu'aucune phrase ne fait "IA" (générique, vide, proclamatoire)
- Vérifie la cohérence du ton (vouvoiement partout)
- Vérifie que chaque page a 300+ mots uniques
- Vérifie que les FAQ sont des vraies questions Google
- Liste CHAQUE problème trouvé avec : page, champ, texte actuel, correction proposée

**Agent Audit Design/Code** — Vérifie l'intégrité technique :
- Tous les fichiers PHP passent `php -l`
- Pas de couleur hardcodée dans le contenu SCF (grep `style=` et `color:`)
- Toutes les images ont un alt text unique et descriptif
- Tous les liens internes retournent HTTP 200
- CF7 existe et a tous ses champs configurés
- Menus "Menu Principal" et "Menu Footer" existent avec les bonnes pages
- Les CSS variables sont correctes dans main.css
- Google Fonts est bien enqueued si police custom
- Schema.org se génère (vérifier le JSON-LD dans le HTML)
- Pas d'erreur PHP dans les logs (tail wp-content/debug.log)

**Agent Audit SEO Final** — Vérifie le SEO one more time :
- Chaque page a un meta title unique < 60 chars avec focus keyword
- Chaque page a une meta description unique < 155 chars
- Chaque image a un alt text
- Le sitemap XML est accessible et contient toutes les pages
- robots.txt existe et est correct
- Open Graph tags présents sur chaque page
- Canonical URLs correctes
- Pas de cannibalization de mots-clés (même focus keyword sur 2 pages)
- Les articles blog ont des excerpts et images à la une

### Étape 2 : Entrer en mode Plan (EnterPlanMode)

Après réception des 3 rapports, appeler `EnterPlanMode` et écrire le **Plan de polish final** :

```markdown
# Plan de polish final — [NOM DU SITE]

## Scores des audits
- Agent 8 (SEO) : X/Y — Note
- Agent 9 (UX) : X/Y — Note
- Audit Contenu : X problèmes trouvés
- Audit Design/Code : X problèmes trouvés
- Audit SEO Final : X problèmes trouvés

## Corrections contenu
Pour chaque problème :
- [ ] Page "[nom]" — champ [champ] — [problème] → [correction]

## Corrections design/code
- [ ] [problème] → [correction]

## Corrections SEO
- [ ] [problème] → [correction]

## Images à regénérer (si nécessaire)
- [ ] Image X — raison → nouveau prompt Gemini

## Contenu à réécrire (si nécessaire)
- [ ] Section X page Y — raison → nouveau texte

## Recommandations visuelles (vérification manuelle par l'utilisateur)
- [WARN] ...

## NE PAS FAIRE (l'utilisateur le fait lui-même)
- Réactiver l'indexation (blog_public)
- Connecter Wordfence Central
```

### Étape 3 : Valider (ExitPlanMode)

Appeler `ExitPlanMode` pour soumettre le plan de polish à l'utilisateur.
**Attendre la validation avant d'exécuter les corrections.**

### Étape 4 : Exécuter les corrections (TaskCreate)

Après validation, créer une task par bloc de corrections :
- Task "Polish contenu : corriger X problèmes" → exécuter toutes les corrections contenu
- Task "Polish code : corriger X problèmes" → exécuter toutes les corrections techniques
- Task "Polish SEO : corriger X problèmes" → exécuter toutes les corrections SEO
- Task "Regénérer images" (si nécessaire) → relancer Agent 3 pour ces images seulement
- Task "Réécrire contenu" (si nécessaire) → relancer Agent 2 pour ces sections seulement

Marquer chaque task `in_progress` → `completed` au fur et à mesure.

### Étape 5 : Résumé final

Afficher à l'utilisateur :
- Scores finaux après corrections
- Nombre total de corrections appliquées
- Liste des recommandations visuelles à vérifier manuellement
- URL du site : https://$DOMAIN
- Rappel : "L'indexation est désactivée. Activez-la quand vous êtes prêt avec : `wp option update blog_public 1 --allow-root`"

---

## AGENTS OPTIONNELS

### AGENT 10 : TRADUCTEUR (si multilingue demandé)

Activer le plugin Youvanna Languages, exporter le JSON FR, traduire, réimporter.

### AGENT 11 : ANALYSE CONCURRENTIELLE (avant Agent 1)

Analyser les 5 premiers résultats Google pour les mots-clés cibles. Identifier les pages qu'ils ont, les contenus qui marchent, les mots-clés qu'ils ciblent, les sections manquantes.

---

## COMMENT L'ORCHESTRATEUR LANCE LES AGENTS

L'orchestrateur (toi) lance chaque agent via l'outil `Agent` de Claude Code :

```
Agent(
  description="Agent 1: Architecte SEO",
  prompt="Tu es l'Architecte SEO... [prompt complet ci-dessus avec les infos client]",
  subagent_type="general-purpose"
)
```

**Règles d'orchestration :**
- Lancer les agents **séquentiellement** : 0 → 1 → 2 → 3 → 4 → 5 → 6 → 7 → 8 → 9
- L'Agent 3 ne peut démarrer qu'APRÈS l'Agent 2 (il a besoin du contenu exact pour choisir les ratios)
- L'Agent 3 doit recevoir le SCRIPT PHP COMPLET de l'Agent 2 (pas un résumé) pour pouvoir compter les paragraphes et choisir les bons ratios d'images
- L'Agent 4 a besoin des outputs des Agents 1 (arborescence), 2 (contenu) ET 3 (IDs images)
- Toujours vérifier l'output de chaque agent avant de lancer le suivant
- Si un agent échoue, corriger et relancer
- **Remplacement des placeholders** : AVANT d'appeler chaque agent, l'orchestrateur DOIT remplacer `$DOMAIN`, `$DOCROOT` et `[CLÉ API]` par les vraies valeurs dans le prompt
- **Encodage UTF-8** : les scripts PHP envoyés via scp doivent être en UTF-8. Utiliser `wp eval-file` plutôt que `wp eval` pour les scripts longs (pas de problème d'échappement shell)
- Pendant la construction, le site a un `noindex` hérité de la démo (blog_public = 0)

**Boucle de correction (Agents 8 et 9) :**
- Les Agents 8 et 9 corrigent eux-mêmes tout ce qu'ils peuvent (via update_field, update_post_meta, wp eval)
- Si un Agent trouve un problème qu'il ne peut PAS corriger (ex: image avec texte généré → nécessite regénération Gemini), il le liste dans son rapport
- Après Agent 9, l'orchestrateur lit les rapports des deux agents et :
  1. Si des images doivent être regénérées → relancer Agent 3 uniquement pour ces images
  2. Si du contenu doit être réécrit → relancer Agent 2 uniquement pour ces sections
  3. Si des corrections techniques restent → les appliquer directement
- NE PAS relancer tout le pipeline — uniquement les agents pertinents pour les corrections restantes
- **Decision tree** :
  - Problème Yoast/meta/mots-clés → l'Agent 8 corrige directement (update_post_meta)
  - Image avec texte généré ou anomalie IA → relancer Agent 3 pour ces images SEULEMENT
  - Contenu trop générique ou Lorem ipsum hors témoignages → relancer Agent 2 pour ces sections
  - Lien cassé / page orpheline → corriger directement via wp eval
  - HTML invalide / H1 manquant → corriger directement via wp eval
  - Recommandation visuelle uniquement → lister pour vérification manuelle par l'utilisateur

**Étape finale (après corrections) :**
- NE PAS réactiver l'indexation — c'est l'utilisateur qui le fait quand il est prêt
- Flush cache : `wp cache flush --allow-root && wp rewrite flush --allow-root`
- Lancer la phase "AUDIT FINAL — PLAN DE POLISH" (voir section dédiée ci-dessous)

---

## OÙ VA QUOI — LA RÈGLE D'OR

### 1. `wp_options` (préfixe `yv_`) → Réglages du site

| Clé WP-CLI | Description | Utilisé dans |
|-------------|-------------|--------------|
| `yv_phone` | Téléphone | Header, footer, contact, schema.org |
| `yv_email` | Email | Footer, contact, schema.org |
| `yv_address` | Adresse postale | Footer, contact, schema.org |
| `yv_opening_hours` | Horaires d'ouverture | Contact |
| `yv_maps_embed_url` | URL iframe Google Maps | Contact, section map |
| `yv_footer_description` | Texte footer | Footer |
| `yv_cta_text` / `yv_cta_link` | Bouton CTA header | Header |
| `yv_social_facebook/instagram/linkedin/youtube/tiktok` | Réseaux sociaux | Footer + schema.org sameAs |
| `yv_city` | Ville | Schema.org |
| `yv_postal_code` | Code postal | Schema.org |
| `yv_latitude` / `yv_longitude` | GPS | Schema.org GeoCoordinates |
| `yv_business_type` | Type schema.org (Dentist, Restaurant...) | Schema.org @type |
| `yv_gtm_id` / `yv_ga_id` | Analytics | `<head>` après consentement cookies |

### 2. CSS Variables (`:root` dans `main.css`) → Design
### 3. Options WordPress standard → blogname, blogdescription, show_on_front
### 4. Champs SCF (sur les pages) → Contenu des pages

---

## CONSENTEMENT COOKIES

Le plugin `youvanna-cookies` gère tout automatiquement. Tu n'as RIEN à configurer.
- Visiteur arrive → bandeau avec Accepter / Refuser
- Accepter → cookie posé → GTM/GA se charge
- JAMAIS injecter de script tracking manuellement
- JAMAIS installer CookieYes ou autre

---

## ERREURS COURANTES — NE PAS TOMBER DANS CES PIÈGES

1. `update_field()` prend le **NAME**, JAMAIS la KEY — `'sections'` pas `'yv_sections'`
2. Images SCF = **attachment ID (int)** — `42` pas `'https://...'`
3. `hero_cta1/cta2` sont des **TEXT PAIRS** — 2 champs séparés, PAS un link array
4. `about_button` EST un **link array SCF** — `['url' => '...', 'title' => '...', 'target' => '']`
5. Homepage = `hero_title`, pages intérieures = `page_hero_title` — NE PAS confondre
6. Page Contact a AUSSI besoin des champs hero (`page_hero_title`, etc.)
7. Galeries = **array d'IDs** — `[42, 43, 44]` PAS array d'arrays
8. `show_on_front` doit être `'page'`
9. CF7 stocke le formulaire dans `_form` meta, PAS dans `post_content`
10. Le champ SCF s'appelle `contact_form_id` (PAS `cf7_form_id`)

---

## RÉFÉRENCE TECHNIQUE

### Helpers PHP

```php
yv_option($name, $fallback)              // wp_options avec préfixe yv_
yv_field($name, $fallback, $id)          // Champ SCF
yv_image($name, $size, $id)              // URL image SCF
yv_img($name, $size, $id, $attrs)        // wp_get_attachment_image (srcset auto)
yv_image_id($name, $id)                   // Attachment ID
yv_render_hero($args)                     // Bandeau hero
yv_section_header($title, $sub, $badge)  // H2 + subtitle + badge pill
yv_render_card($args)                     // Carte (image_id > image > icon)
yv_render_stats($rows, $class)           // Chiffres avec counter animation
```

### Plugins du template

| Plugin | Rôle | Config |
|--------|------|--------|
| SCF (Secure Custom Fields) | Champs éditables | Rien — tout en PHP |
| ACF Content Analysis for Yoast | Bridge SCF ↔ Yoast | Auto |
| Contact Form 7 | Formulaire contact | Agent 4 le configure |
| Yoast SEO | SEO, sitemap | Agent 5 le configure |
| Classic Editor | Éditeur simplifié | Auto |
| Wordfence | Sécurité | Auto via post-clone-setup.php |
| Redis Object Cache | Cache objet | Auto |
| WP Super Cache | Cache page | Auto |
| Youvanna Languages | Multilingue | Agent 10 si demandé |
| Youvanna Cookies | RGPD | **RIEN À CONFIGURER** |

### Architecture des fichiers

```
youvanna-starter/
├── functions.php, header.php, footer.php
├── front-page.php (homepage: 5 sections SCF)
├── page.php (pages intérieures: hero + flex content)
├── page-contact.php (contact: hero + CF7 + coordonnées)
├── single.php, home.php, archive.php, 404.php
├── assets/css/main.css (design system)
├── assets/js/main.js (animations)
└── template-parts/section-{layout}.php (11 layouts)
```

### Animations (automatiques, ne rien ajouter)
- Scroll reveal (.reveal)
- Stagger reveal (cards, FAQ, stats, testimonials, team)
- Counter animation (.stat-number)
- Parallax (heroes, CTA)
- Marquee témoignages (3+ = défilement, 6+ = 2 lignes)

### Section headers — Style Framer
- PAS de `<mark>` — titres en texte brut uniquement
- Badge pill (3e argument de yv_section_header)
- Exemple : `'title' => 'Des solutions sur mesure'`

### Schema.org (automatiques)
| Schema | Page | Source |
|--------|------|--------|
| WebSite | Homepage | bloginfo |
| LocalBusiness | Homepage | yv_option() |
| BreadcrumbList | Toutes sauf home | Nav auto |
| FAQPage | Pages avec FAQ | Sections flex faq |
| BlogPosting | Articles | Post data |

### Post-clone setup (sécurité + cache)
```bash
wp eval-file wp-content/themes/youvanna-starter/post-clone-setup.php --allow-root
```
Configure automatiquement : Youvanna Languages, Redis, WP Super Cache, Wordfence (WAF + firewall + brute force + scanner).

### CF7 config complète (pour l'Agent 4)

```php
$form = '<label> Votre nom
    [text* your-name autocomplete:name] </label>
<label> Votre email
    [email* your-email autocomplete:email] </label>
<label> Votre téléphone
    [tel your-phone autocomplete:tel] </label>
<label> Sujet
    [text* your-subject] </label>
<label> Votre message
    [textarea your-message] </label>
[submit "Envoyer le message"]';
update_post_meta($form_id, '_form', $form);
wp_update_post(['ID' => $form_id, 'post_title' => 'Formulaire de contact']);
update_post_meta($form_id, '_mail', [
    'active' => true,
    'subject' => '[_site_title] Nouveau message: "[your-subject]"',
    'sender' => '[_site_title] <contact@youvanna.com>',
    'recipient' => get_option('yv_mail_to', get_option('admin_email')),
    'body' => "De : [your-name] <[your-email]>\nTéléphone : [your-phone]\nSujet : [your-subject]\n\nMessage :\n[your-message]",
    'additional_headers' => 'Reply-To: [your-email]',
    'attachments' => '', 'use_html' => false, 'exclude_blank' => false,
]);
update_post_meta($form_id, '_messages', [
    'mail_sent_ok' => 'Votre message a bien été envoyé. Nous vous répondons sous 24h.',
    'mail_sent_ng' => 'Une erreur est survenue. Veuillez réessayer.',
    'validation_error' => 'Un ou plusieurs champs contiennent une erreur.',
    'invalid_required' => 'Veuillez remplir ce champ.',
    'invalid_email' => 'Adresse email invalide.',
    'invalid_tel' => 'Numéro de téléphone invalide.',
]);
```

### Icônes Font Awesome (fallback si 6+ cartes)
- Dentaire : `fa-tooth`, `fa-laptop-medical`, `fa-server`, `fa-shield-halved`
- Restaurant : `fa-utensils`, `fa-wine-glass`, `fa-truck`, `fa-clock`
- BTP : `fa-hammer`, `fa-hard-hat`, `fa-ruler-combined`, `fa-house`
- Tech : `fa-code`, `fa-cloud`, `fa-chart-line`, `fa-lock`

### Ajout de layouts custom
1. Ajouter layout dans functions.php → field group yv_flex → layouts
2. Créer template-parts/section-{nom}.php
3. Ajouter styles dans main.css
4. php -l template-parts/section-{nom}.php
