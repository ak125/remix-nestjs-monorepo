# Utiliser une image Node.js compatible ARM
FROM --platform=linux/arm64 node:18-alpine AS base

# ------------------- Builder Stage -------------------
FROM base AS builder

# Installer libc6-compat (si nécessaire pour glibc)
RUN apk add --no-cache libc6-compat
RUN apk update

# Définir le répertoire de travail pour builder
WORKDIR /app

# Installer Turbo globalement
RUN npm install --global turbo

# Copier les fichiers du projet
COPY --chown=node:node . .

# Pruner les dépendances pour le backend
RUN turbo prune @fafa/backend --docker

# ------------------- Installer Stage -------------------
FROM base AS installer

# Utiliser les mêmes dépendances
RUN apk add --no-cache libc6-compat
RUN apk update

# Définir le répertoire de travail
WORKDIR /app

# Copier les fichiers nécessaires depuis le builder
COPY .gitignore .gitignore
COPY --chown=node:node --from=builder /app/out/json/ .
COPY --chown=node:node --from=builder /app/out/package-lock.json ./package-lock.json

# Installer les dépendances
RUN npm install

# Build du projet
COPY --from=builder /app/out/full/ .
COPY turbo.json turbo.json

# Variables d'environnement
ARG TURBO_TEAM
ENV TURBO_TEAM=$TURBO_TEAM

ARG TURBO_TOKEN
ENV TURBO_TOKEN=$TURBO_TOKEN
ENV TZ=Europe/Paris
ENV NODE_ENV="production"

# ------------------- Runner Stage -------------------
FROM base AS runner

# Définir le répertoire de travail
WORKDIR /app

# Créer un utilisateur non-root
RUN addgroup --system --gid 1001 nodejs
RUN adduser --system --uid 1001 remix-api
USER remix-api

# Copier les fichiers nécessaires
COPY --chown=remix-api:nodejs --from=installer /app/backend/package.json ./backend/package.json
COPY --chown=remix-api:nodejs --from=installer /app/backend/dist ./backend/dist
COPY --chown=remix-api:nodejs --from=installer /app/node_modules ./node_modules

# Ajouter le script de démarrage
COPY --chown=remix-api:nodejs --from=builder /app/backend/start.sh ./backend/start.sh

# Démarrer l'application
ENTRYPOINT [ "backend/start.sh" ]
















