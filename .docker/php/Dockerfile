# Utilisation d'une image PHP basée sur Debian (Bookworm), plus stable pour les paquets externes
FROM php:8.3-fpm-bookworm

# Arguments pour l'utilisateur et le groupe
ARG UID
ARG GID
ENV UID=${UID:-1000}
ENV GID=${GID:-1000}

# Mise à jour et installation des dépendances système via apt-get
# DEBIAN_FRONTEND=noninteractive évite les questions pendant l'installation
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get install -y \
    gnupg \
    curl \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    unixodbc-dev \
    --no-install-recommends

# --- Installation du pilote Microsoft SQL Server pour Debian ---
# Ajout de la clé GPG de Microsoft
RUN curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg

# Ajout du dépôt de paquets de Microsoft
RUN curl -fsSL https://packages.microsoft.com/config/debian/12/prod.list > /etc/apt/sources.list.d/mssql-release.list

# Mise à jour de l'index et installation du pilote
RUN apt-get update && ACCEPT_EULA=Y apt-get install -y msodbcsql18 mssql-tools18
# --- Fin de l'installation du pilote ---

# Installation des extensions PHP requises
# La méthode est la même que sur Alpine
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo pdo_mysql zip

# Installation des extensions PECL pour SQL Server
RUN pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Création d'un utilisateur et d'un groupe non-root
RUN groupadd -g ${GID} laravel && \
    useradd -u ${UID} -g laravel -m -s /bin/bash laravel

# Copier la configuration OPcache personnalisée
COPY .docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Copier le code de l'application
COPY --chown=laravel:laravel . /var/www/html

# Définir le répertoire de travail
WORKDIR /var/www/html

# Changer l'utilisateur pour l'utilisateur non-root
USER laravel

# Exposer le port 9000 et démarrer php-fpm
EXPOSE 9000
CMD ["php-fpm"]