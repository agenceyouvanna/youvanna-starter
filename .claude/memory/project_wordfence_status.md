---
name: Wordfence status on demo.youvanna.com
description: Wordfence scores were poor — auto-config script created to fix on each new clone
type: project
---

demo.youvanna.com avait des scores Wordfence faibles (35% WAF, 70% rules, 0% blocklist).
- Created `wordfence-autoconfig.php` dans le thème pour corriger automatiquement via WP-CLI
- Le script déconnecte de Central, configure firewall (learning mode 7j), brute force, scanner
- Étape manuelle requise : Wordfence → Firewall → "Optimize Wordfence Firewall"
- Exécuter sur chaque nouveau site après clonage
