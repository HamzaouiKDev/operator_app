#!/bin/bash

# Arrête le script si une commande échoue
set -e

echo "--- Démarrage du déploiement en production ---"

# 1. Créer le fichier .env à partir de votre .env.production ou équivalent
# Assurez-vous d'avoir un fichier .env.production prêt !
if [ ! -f .env ]; then
    echo "Fichier .env non trouvé. Copie de .env.example."
    cp .env.example .env
    # IMPORTANT: Modifiez votre .env maintenant avec les bonnes valeurs de production !
    # Spécifiquement DB_HOST, qui devrait être le nom de votre conteneur de base de données
    # si vous en ajoutez un, ou l'IP/hostname de votre serveur de base de données.
    echo "VEUILLEZ METTRE À JOUR VOTRE FICHIER .env AVANT DE CONTINUER !"
    exit 1
fi

# 2. Exporter les variables UID/GID pour corriger les permissions de fichiers
export UID=$(id -u)
export GID=$(id -g)
echo "Utilisation de UID=$UID et GID=$GID"

# 3. Construire et démarrer les conteneurs en mode détaché
echo "Construction des images Docker (cela peut prendre un moment)..."
docker-compose build --no-cache --progress=plain
echo "Démarrage des conteneurs..."
docker-compose up -d

# 4. Installer les dépendances Composer sans les packages de dev et optimiser l'autoloader
echo "Installation des dépendances Composer..."
docker-compose exec -u laravel app composer install --no-dev --optimize-autoloader

# 5. Attendre un instant pour que l'application soit prête
sleep 5

# 6. Exécuter les commandes d'optimisation de Laravel pour la production
echo "Optimisation de Laravel pour la production..."
docker-compose exec -u laravel app php artisan config:cache
docker-compose exec -u laravel app php artisan route:cache
docker-compose exec -u laravel app php artisan view:cache
docker-compose exec -u laravel app php artisan event:cache

# 7. Exécuter les migrations de base de données
# Le flag --force est requis en production pour éviter la confirmation manuelle
echo "Exécution des migrations de base de données..."
docker-compose exec -u laravel app php artisan migrate --force

echo "--- Déploiement terminé avec succès ! ---"
echo "Votre application est disponible sur http://localhost:8080"
docker-compose ps