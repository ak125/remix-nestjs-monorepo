# Utilisation de l'image de base Node.js avec Alpine Linux
FROM --platform=amd64 node:18-alpine As base

# Étape de build pour gérer les dépendances et la compilation
FROM base AS builder

# Installation des paquets nécessaires
RUN apk add --no-cache libc6-compat

# Définition du répertoire de travail
WORKDIR /app

# Installation de Turbo pour gérer le monorepo
RUN npm install --global turbo

# Copie des fichiers du projet
COPY --chown=node:node . .

# Donne les bonnes permissions au dossier backend
RUN chmod -R 755 /app/backend

# Étape d'installation des dépendances
FROM base AS installer

# Installation des paquets nécessaires
RUN apk add --no-cache libc6-compat

# Définition du répertoire de travail
WORKDIR /app

# Copie des fichiers nécessaires depuis l'étape builder
COPY --chown=node:node --from=builder /app/out/json/ .
COPY --chown=node:node --from=builder /app/out/package-lock.json ./package-lock.json

# Installation des dépendances NPM
RUN npm install

# Copie du code source complet depuis l'étape builder
COPY --from=builder /app/out/full/ .
COPY turbo.json turbo.json

# Définition des variables d'environnement
ARG TURBO_TEAM
ENV TURBO_TEAM=$TURBO_TEAM

ARG TURBO_TOKEN
ENV TURBO_TOKEN=$TURBO_TOKEN
ENV TZ=Europe/Paris
ENV NODE_ENV="production"

# Construction du projet
RUN npm run build

# Étape de production pour exécuter l'application
FROM base AS runner

# Définition du répertoire de travail
WORKDIR /app

# Création d'un utilisateur non-root
RUN addgroup --system --gid 1001 nodejs \
    && adduser --system --uid 1001 remix-api
USER remix-api

# Copie des fichiers nécessaires depuis l'étape installer
COPY --chown=remix-api:nodejs --from=installer /app/backend/package.json ./backend/package.json
COPY --chown=remix-api:nodejs --from=installer /app/backend/dist ./backend/dist
COPY --chown=remix-api:nodejs --from=installer /app/node_modules ./node_modules
COPY --chown=remix-api:nodejs --from=installer /app/node_modules/@fafa/frontend ./node_modules/@fafa/frontend
COPY --chown=remix-api:nodejs --from=installer /app/node_modules/@fafa/typescript-config ./node_modules/@fafa/typescript-config
COPY --chown=remix-api:nodejs --from=installer /app/node_modules/@fafa/eslint-config ./node_modules/@fafa/eslint-config

# Donne les permissions d'exécution au script de démarrage
COPY --chown=remix-api:nodejs --from=builder /app/backend/start.sh ./backend/start.sh
RUN chmod +x ./backend/start.sh

# Exposition du port de l'application
EXPOSE 3000

# Démarrage de l'application
ENTRYPOINT ["sh", "-c", "./backend/start.sh"]









