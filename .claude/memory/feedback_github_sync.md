---
name: GitHub Sync Rule
description: TOUJOURS push les modifs du starter theme vers GitHub après chaque changement — GitHub est la source de vérité
type: feedback
---

Toute modification du thème starter (youvanna-starter) DOIT être commitée et pushée vers GitHub (`origin/main`).

**Why:** L'utilisateur veut que demo.youvanna.com et GitHub soient toujours synchronisés. Un cron pull toutes les 5 minutes, mais si Claude modifie le thème localement sans push, les changements sont perdus pour les futurs clones.

**How to apply:** Après TOUTE modification dans `/var/www/vhosts/demo.youvanna.com/httpdocs/wp-content/themes/youvanna-starter`, faire `git add`, `git commit`, `git push origin main`. Un webhook GitHub déclenche instantanément un `git pull` sur la démo. Le skill `/youvanna-new-site` clone la démo, donc elle doit toujours refléter le dernier état GitHub.
