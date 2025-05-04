#!/bin/bash

# Activer le mode strict pour voir les erreurs
set -e

echo "🚀 Déploiement en cours..."

# Aller dans le dossier de l'application
cd /var/www/zetta_api

# Mettre à jour le dépôt depuis GitHub
echo "📥 Mise à jour du dépôt depuis GitHub..."
git pull origin main

# Installer les dépendances PHP avec Composer
echo "📦 Installation des dépendances PHP..."
composer install --no-dev --optimize-autoloader

# Vérifier si npm est installé
if ! command -v npm &> /dev/null; then
    echo "❌ npm n'est pas installé. Veuillez installer Node.js et npm."
    exit 1
fi

# Installer les dépendances npm (si frontend)
echo "📦 Installation des dépendances npm..."
npm install
npm run build

# Configuration de l'environnement
echo "⚙️  Configuration de l'environnement..."

# Exécuter les migrations (avec --force)
echo "📊 Exécution des migrations..."
php artisan migrate:refresh --seed --force

# Nettoyage du cache
echo "🧹 Nettoyage du cache..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimisation
echo "⚡ Optimisation de l'application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Redémarrage des services
echo "🔄 Redémarrage de PHP-FPM et Nginx..."
systemctl restart php8.3-fpm
systemctl restart nginx

echo "✅ Déploiement terminé avec succès ! 🎉"
