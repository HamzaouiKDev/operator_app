@echo off
REM ===================================================================
REM ==    SCRIPT DE DEPLOIEMENT POUR WINDOWS (.bat) - Version Finale ==
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

REM La section sur UID/GID est specifique a Linux/macOS et est supprimee.
REM Votre docker-compose.yml gere deja les valeurs par defaut (1000).

REM 2. Reconstruire les images si necessaire et demarrer les conteneurs.
REM L'option --build garantit que toute modification du Dockerfile est prise en compte.
echo Reconstruction des images et demarrage des conteneurs...
docker-compose up -d --build

REM 3. Attendre un instant pour que les conteneurs se stabilisent.
echo Attente de 10 secondes pour la stabilisation des services...
timeout /t 10 /nobreak > nul

REM 4. Installer les dependances Composer pour la production.
echo Installation des dependances Composer...
docker-compose exec -T -u laravel app composer install --no-dev --optimize-autoloader --no-interaction --no-progress

REM 5. Nettoyer les anciens caches pour eviter les erreurs.
echo Nettoyage des caches Laravel...
docker-compose exec -T -u laravel app php artisan config:clear
docker-compose exec -T -u laravel app php artisan route:clear
docker-compose exec -T -u laravel app php artisan view:clear
docker-compose exec -T -u laravel app php artisan event:clear

REM 6. Executer les migrations de base de donnees.
echo Execution des migrations de base de donnees...
docker-compose exec -T -u laravel app php artisan migrate --force

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