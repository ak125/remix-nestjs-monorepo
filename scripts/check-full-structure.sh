#!/bin/bash

echo "🚀 Vérification approfondie de la structure du monorepo NestJS + Remix"

# Se positionner à la racine du projet
cd "$(dirname "$0")/.."

# Vérifier l'existence des dossiers avant d'exécuter les commandes
if [[ ! -d "backend" || ! -d "frontend" || ! -d "packages" ]]; then
  echo "❌ Erreur : Les dossiers backend, frontend ou packages sont introuvables."
  exit 1
fi

echo "📂 Liste des fichiers :"
find backend frontend packages -type f | sort | uniq -c | sort -rh | head -20

echo "📂 Structure des dossiers :"
tree -I "node_modules|dist|.git|.turbo|.cache|.vscode|.idea|legacy|build|.next|public" backend frontend packages

echo "🕒 Fichiers modifiés récemment :"
find backend frontend packages -type f -mtime -7

echo "📊 Top 20 des fichiers les plus lourds :"
du -ah backend frontend packages | sort -rh | head -20

echo "🔎 Liste des services :"
find backend/src -type f -name "*.service.ts"

echo "🔎 Liste des contrôleurs :"
find backend/src -type f -name "*.controller.ts"

echo "🔎 Liste des DTO :"
find backend/src -type f -name "*.dto.ts"

echo "🔄 Comparaison avec la version enregistrée..."
mkdir -p scripts/snapshots
if [ -f scripts/snapshots/current_structure.txt ]; then
  diff scripts/snapshots/current_structure.txt <(tree -I "node_modules|dist|.git|.turbo|.cache|.vscode|.idea|legacy|build|.next|public" backend frontend packages) || echo "✅ Aucune différence détectée."
else
  echo "⚠️ Aucune structure de référence trouvée. Création d'un snapshot..."
fi
tree -I "node_modules|dist|.git|.turbo|.cache|.vscode|.idea|legacy|build|.next|public" backend frontend packages > scripts/snapshots/current_structure.txt

echo "✅ Analyse terminée !"
