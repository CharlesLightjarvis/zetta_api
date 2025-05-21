# #!/bin/bash

# # Activer le mode strict pour voir les erreurs
# set -e

# echo "🚀 Déploiement en cours..."

# # Aller dans le dossier de l'application
# cd /var/www/zetta_api

# # Forcer la remise à zéro des fichiers locaux avant de pull
# # echo "📥 Réinitialisation du dépôt local..."
# # git reset --hard HEAD
# # git clean -fd
# echo "📥 Mise à jour du dépôt depuis GitHub..."
# git pull origin main

# # Installer les dépendances Composer
# echo "📦 Installation des dépendances PHP..."
# composer install --no-dev --optimize-autoloader

# # Installer les dépendances npm (si frontend)
# echo "📦 Installation des dépendances npm..."
# npm install && npm run build

# # Mettre à jour l'environnement
# echo "⚙️  Configuration de l'environnement..."

# # Exécuter les migrations
# # echo "📊 Exécution des migrations..."
# php artisan migrate:refresh --seed --force

# # Vider et optimiser le cache
# echo "🧹 Nettoyage du cache..."
# php artisan cache:clear
# php artisan config:clear
# php artisan route:clear
# php artisan view:clear

# echo "⚡ Optimisation de l'application..."
# php artisan config:cache
# php artisan route:cache
# php artisan view:cache

# # Redémarrer les services
# echo "🔄 Redémarrage de PHP-FPM et Nginx..."
# systemctl restart php8.3-fpm
# systemctl restart nginx

# echo "✅ Déploiement terminé avec succès ! 🎉"



#!/bin/bash

echo "🚀 Déploiement en cours..."

APP_DIR="/var/www/zetta_api"
REPO_URL="git@github.com:CharlesLightjarvis/zetta_api.git"

# Supprimer l'ancien projet si présent
if [ -d "$APP_DIR" ]; then
  echo "🧹 Suppression de l'ancien projet..."
  rm -rf "$APP_DIR"
fi

# Cloner le dépôt depuis GitHub
echo "📥 Clonage du dépôt depuis GitHub..."
git clone "$REPO_URL" "$APP_DIR"

# Aller dans le dossier
cd "$APP_DIR" || exit

# (Optionnel) Installer les dépendances Laravel
echo "📦 Installation des dépendances..."
composer install --no-interaction --prefer-dist --optimize-autoloader

# (Optionnel) Migrer la base de données
echo "🗄️ Migration de la base de données..."
php artisan migrate:refresh --seed --force

# (Optionnel) Autres commandes Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Redémarrer les services
echo "🔄 Redémarrage de PHP-FPM et Nginx..."
systemctl restart php8.3-fpm
systemctl restart nginx

echo "✅ Déploiement terminé avec succès !"
