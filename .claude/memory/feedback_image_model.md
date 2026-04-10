---
name: Gemini Image Model - JAMAIS Imagen
description: Règle absolue sur le modèle de génération d'images à utiliser - toujours gemini-3-pro-image-preview, jamais imagen
type: feedback
---

**TOUJOURS utiliser `gemini-3-pro-image-preview` pour générer des images. JAMAIS `imagen-3`, `imagen-3.0-generate-002`, `imagen-4`, ni aucun modèle Imagen.**

**Why:** Le user m'a déjà repris une première fois quand j'ai mentionné "imagen-3" dans un rapport d'audit et un plan d'images pour sfscmfco.youvanna.com. Il avait explicitement configuré `gemini-3-pro-image-preview` dans la mémoire `reference_gemini_api.md` et dans le skill `youvanna-new-site` mais pas dans CLAUDE.md. Depuis cet incident, la règle est aussi dans CLAUDE.md pour qu'elle soit ultra-visible et que je ne l'oublie plus jamais, même en déléguant à un sous-agent (qui peut ne pas voir cette mémoire).

**How to apply:**
- Chaque fois que je génère une image via l'API Gemini → endpoint `https://generativelanguage.googleapis.com/v1beta/models/gemini-3-pro-image-preview:generateContent`
- Chaque fois que je délègue à un sous-agent une tâche de génération d'images → mentionner EXPLICITEMENT `gemini-3-pro-image-preview` dans le prompt de l'agent, pas juste "Gemini" ou "imagen" ou "le modèle Gemini"
- Chaque fois que je fais un plan d'images → le plan doit mentionner `gemini-3-pro-image-preview`, jamais "imagen"
- Body API : `{"contents":[{"parts":[{"text":"..."}]}], "generationConfig":{"responseModalities":["IMAGE"], "imageConfig":{"aspectRatio":"16:9"}}}`
- Ratios acceptés : 1:1, 3:2, 4:3, 16:9, 9:16, 2:3, 3:4
- Toujours inclure dans les prompts : "no text, no words, no letters, no logos, no watermarks"
