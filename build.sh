#!/bin/bash

# Activer le mode strict pour voir les erreurs
set -e

echo "🚀 Déploiement en cours..."

# Aller dans le dossier de l'application
cd /var/www/zetta_api

# Réinitialiser le dépôt local avec GitHub
echo "📥 Réinitialisation du dépôt local avec GitHub..."
git fetch origin
git reset --hard origin/main

# Installer les dépendances Composer (PHP)
echo "📦 Installation des dépendances PHP..."
composer install --no-dev --optimize-autoloader

# Installer les dépendances npm (si nécessaire pour le frontend)
if [ -f "package.json" ]; then
    echo "📦 Installation des dépendances npm..."
    npm install && npm run build
fi

# Mettre à jour l'environnement
echo "⚙️ Configuration de l'environnement..."

# Exécuter les migrations (si besoin)
echo "📊 Exécution des migrations..."
php artisan migrate:refresh --seed --force

# Vider et optimiser le cache
echo "🧹 Nettoyage du cache..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimiser l'application
echo "⚡ Optimisation de l'application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Redémarrer les services
echo "🔄 Redémarrage de PHP-FPM et Nginx..."
systemctl restart php8.3-fpm
systemctl restart nginx

echo "✅ Déploiement terminé avec succès ! 🎉"