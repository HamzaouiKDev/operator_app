# Fichier : docker/php/Dockerfile

# Étape de base : On utilise PHP 8.3, requis pour Laravel 12, avec FPM.
FROM php:8.3-fpm

# Arguments pour l'utilisateur, à passer depuis docker-compose.yml.
# Cela évite les problèmes de permissions de fichiers.
ARG user
ARG uid

# Variable d'environnement pour accepter la licence des drivers Microsoft.
ENV ACCEPT_EULA=Y

# 1. Installation des dépendances système nécessaires.
# Inclut les outils pour les extensions gd, zip, et les drivers SQL Server.
RUN apt-get update && apt-get install -y \
    gnupg \
    lsb-release \
    curl \
    wget \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libgssapi-krb5-2 \
    unixodbc-dev

# 2. Installation des extensions PHP de base (gd et zip).
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install zip

# 3. Installation des drivers Microsoft ODBC pour SQL Server.
# Compatible avec la base Debian 12 de l'image php:8.3.
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
    && curl https://packages.microsoft.com/config/debian/12/prod.list > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && apt-get install -y msodbcsql18 mssql-tools18

# 4. Installation des extensions PHP PECL pour la connexion à SQL Server.
RUN pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

# 5. Installation de Composer (le gestionnaire de paquets pour PHP).
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 6. Création d'un utilisateur système pour correspondre à votre utilisateur local.
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# 7. Définition du répertoire de travail à l'intérieur du conteneur.
WORKDIR /var/www

# Ajustement des permissions pour le stockage et le cache de Laravel.
RUN chown -R www-data:www-data /var/www

# Exposition du port 9000, utilisé par PHP-FPM.
EXPOSE 9000

# Commande par défaut pour démarrer le service PHP-FPM.
CMD ["php-fpm"]