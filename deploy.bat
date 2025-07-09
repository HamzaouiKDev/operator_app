@echo off
REM ===================================================================
REM ==    SCRIPT DE DEPLOIEMENT POUR WINDOWS (.bat) - Version Corrigée ==
REM ===================================================================

echo --- Demarrage du deploiement en production ---

REM 1. Verifier si le fichier .env existe, sinon arreter le script.
if not exist .env (
    echo Fichier .env non trouve. Copie de .env.example...
    copy .env.example .env
    echo.
    echo ATTENTION: VEUILLEZ METTRE A JOUR VOTRE FICHIER .env AVANT DE CONTINUER!
    pause
    exit /b 1
)

REM 2. Reconstruire les images si necessaire et demarrer les conteneurs.
echo Reconstruction des images et demarrage des conteneurs...
docker-compose up -d --build

REM 3. Attendre un instant pour que les conteneurs se stabilisent.
echo Attente de 10 secondes pour la stabilisation des services...
timeout /t 10 /nobreak > nul

REM ======================= CORRECTION DES PERMISSIONS ======================
REM == On donne la propriete des fichiers a l'utilisateur 'laravel'        ==
REM == en executant la commande en tant que 'root' a l'interieur du conteneur. ==
REM =========================================================================
echo Correction des permissions des fichiers dans le conteneur...
docker-compose exec -T -u root app chown -R laravel:laravel /var/www/html


REM 4. Installer les dependances Composer pour la production.
echo Installation des dependances Composer...
docker-compose exec -T -u laravel app composer install --no-dev --optimize-autoloader --no-interaction --no-progress

REM 5. Nettoyer les anciens caches pour eviter les erreurs.
echo Nettoyage des caches Laravel...
docker-compose exec -T -u laravel app php artisan config:clear
docker-compose exec -T -u laravel app php artisan route:clear
docker-compose exec -T -u laravel app php artisan view:clear
docker-compose exec -T -u laravel app php artisan event:clear

REM 6. Executer les migrations et le seeding de la base de donnees.
echo Execution des migrations de base de donnees...
docker-compose exec -T -u laravel app php artisan migrate --force

echo Initialisation de la base de donnees avec les seeders...
docker-compose exec -T -u laravel app php artisan db:seed --force


REM 7. Creer les nouveaux fichiers de cache optimises pour la production.
echo Optimisation de Laravel pour la production...
docker-compose exec -T -u laravel app php artisan config:cache
docker-compose exec -T -u laravel app php artisan route:cache
docker-compose exec -T -u laravel app php artisan view:cache
docker-compose exec -T -u laravel app php artisan event:cache

echo.
echo --- Deploiement termine avec succes ! ---
echo Votre application devrait etre disponible sur http://localhost:8080
echo.

REM Affiche l'etat des conteneurs a la fin du script.
docker-compose ps

pause
