#!/bin/bash

STRUCTURE_FILE="original_structure.txt"
EXCLUDE_DIRS="node_modules|dist|.git|.turbo|.next|.cache|.vscode|.idea|legacy|build"

echo -e "\n🚀 \033[1;34mVérification de la structure du monorepo NestJS + Remix\033[0m"

# Dossiers à vérifier (sans exclusions)
EXPECTED_DIRS=(
    "backend"
    "backend/src"
    "backend/src/auth"
    "backend/src/prisma"
    "backend/src/remix"
    "backend/prisma/migrations"
    "frontend"
    "frontend/app"
    "frontend/app/components"
    "frontend/app/routes"
    "packages"
)

# Fichiers à vérifier (sans exclusions)
EXPECTED_FILES=(
    "Dockerfile"
    "backend/package.json"
    "backend/nest-cli.json"
    "backend/tsconfig.json"
    "backend/start.sh"
    "backend/src/main.ts"
    "backend/src/app.module.ts"
    "backend/src/auth/auth.controller.ts"
    "backend/src/auth/auth.service.ts"
    "backend/src/prisma/prisma.service.ts"
    "backend/prisma/schema.prisma"
    "frontend/package.json"
    "frontend/tailwind.config.cjs"
    "frontend/tsconfig.json"
    "frontend/vite.config.ts"
    "frontend/app/routes/_index.tsx"
    "frontend/app/routes/_public+/login.tsx"
    "frontend/app/routes/_public+/register.tsx"
    "turbo.json"
    "package.json"
)

# Vérification des dossiers
echo -e "\n📂 \033[1;33mVérification des dossiers...\033[0m"
for dir in "${EXPECTED_DIRS[@]}"; do
    if [ ! -d "$dir" ]; then
        echo -e "❌ \033[1;31mDossier manquant : $dir\033[0m"
    else
        echo -e "✅ \033[1;32m$dir\033[0m"
    fi
done

# Vérification des fichiers
echo -e "\n📄 \033[1;33mVérification des fichiers...\033[0m"
for file in "${EXPECTED_FILES[@]}"; do
    if [ ! -f "$file" ]; then
        echo -e "❌ \033[1;31mFichier manquant : $file\033[0m"
    else
        echo -e "✅ \033[1;32m$file\033[0m"
    fi
done

# Vérification des dépendances Backend (NestJS)
echo -e "\n🔍 \033[1;33mVérification des dépendances Backend...\033[0m"
cd backend || exit

BACKEND_DEPENDENCIES=(
    "@nestjs/common"
    "@nestjs/core"
    "@nestjs/platform-express"
    "@prisma/client"
    "bcryptjs"
    "connect-redis"
    "express-session"
    "ioredis"
    "passport"
    "passport-local"
    "reflect-metadata"
    "rxjs"
)

if [ -f package.json ]; then
    for dep in "${BACKEND_DEPENDENCIES[@]}"; do
        if ! jq -e ".dependencies[\"$dep\"] // .devDependencies[\"$dep\"]" package.json &>/dev/null; then
            echo -e "❌ \033[1;31mDépendance manquante : $dep\033[0m"
        else
            echo -e "✅ \033[1;32m$dep installé.\033[0m"
        fi
    done
else
    echo -e "❌ \033[1;31mErreur : package.json introuvable dans backend\033[0m"
fi
cd ..

# Vérification des dépendances Frontend (Remix)
echo -e "\n🔍 \033[1;33mVérification des dépendances Frontend...\033[0m"
cd frontend || exit

FRONTEND_DEPENDENCIES=(
    "@remix-run/node"
    "@remix-run/react"
    "@remix-run/serve"
    "react"
    "react-dom"
    "tailwindcss"
    "vite"
)

if [ -f package.json ]; then
    for dep in "${FRONTEND_DEPENDENCIES[@]}"; do
        if ! jq -e ".dependencies[\"$dep\"] // .devDependencies[\"$dep\"]" package.json &>/dev/null; then
            echo -e "❌ \033[1;31mDépendance manquante : $dep\033[0m"
        else
            echo -e "✅ \033[1;32m$dep installé.\033[0m"
        fi
    done
else
    echo -e "❌ \033[1;31mErreur : package.json introuvable dans frontend\033[0m"
fi
cd ..

# 🔍 Vérification des ajouts/suppressions (sans exclusions)
echo -e "\n🔄 \033[1;34mAnalyse des modifications de structure...\033[0m"

# Générer la nouvelle structure du projet sans `node_modules`, `dist`, `build`, etc.
find backend frontend packages -type d -o -type f | grep -Ev "$EXCLUDE_DIRS" | sort > current_structure.txt

# Comparer avec la structure originale
if [ -f "$STRUCTURE_FILE" ]; then
    echo -e "\n🆕 \033[1;36mFichiers/Dossiers ajoutés depuis la dernière analyse :\033[0m"
    comm -13 "$STRUCTURE_FILE" current_structure.txt | while read -r line; do
        echo -e "➕ \033[1;32m$line\033[0m"
    done

    echo -e "\n🗑️ \033[1;36mFichiers/Dossiers supprimés depuis la dernière analyse :\033[0m"
    comm -23 "$STRUCTURE_FILE" current_structure.txt | while read -r line; do
        echo -e "❌ \033[1;31m$line\033[0m"
    done
fi

# Sauvegarder la nouvelle structure
mv current_structure.txt "$STRUCTURE_FILE"

echo -e "\n✅ \033[1;34mVérification terminée.\033[0m"
