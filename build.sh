#!/bin/bash

# Activer le mode strict
set -e

echo "🚀 Déploiement en cours..."

# Aller dans le dossier de l'application
DEPLOY_DIR="/var/www/zetta_api"
cd "$DEPLOY_DIR" || { echo "❌ Échec du changement de répertoire vers $DEPLOY_DIR"; exit 1; }

# 1. Supprimer complètement le contenu sauf .env et storage
echo "🧹 Nettoyage du répertoire (sauvegarde .env et storage)..."
mkdir -p /tmp/zetta_backup
[ -f .env ] && cp .env /tmp/zetta_backup/
[ -d storage ] && cp -r storage /tmp/zetta_backup/

# 2. Cloner à nouveau le dépôt
echo "📥 Clonage du dépôt..."
rm -rf "$DEPLOY_DIR"/* "$DEPLOY_DIR"/.git
git clone https://github.com/CharlesLightjarvis/zetta_api.git "$DEPLOY_DIR"

# 3. Restaurer .env et storage
echo "🔄 Restauration des fichiers critiques..."
[ -f /tmp/zetta_backup/.env ] && cp /tmp/zetta_backup/.env "$DEPLOY_DIR"/
[ -d /tmp/zetta_backup/storage ] && cp -r /tmp/zetta_backup/storage "$DEPLOY_DIR"/

# 4. Aller dans le dossier
cd "$DEPLOY_DIR" || exit 1

# Installation des dépendances
echo "📦 Installation des dépendances PHP..."
composer install --no-dev --optimize-autoloader

if [ -f "package.json" ]; then
    echo "📦 Installation des dépendances Node..."
    npm ci --silent && npm run build --silent
fi

# Migrations et optimisation
echo "⚙️ Configuration de l'application..."
php artisan migrate --force
php artisan optimize:clear
php artisan optimize

# Redémarrage des services
echo "🔄 Redémarrage des services..."
systemctl restart php8.3-fpm
systemctl restart nginx

# Nettoyage
# rm -rf /tmp/zetta_backup

echo "✅ Déploiement terminé avec succès! 🎉"