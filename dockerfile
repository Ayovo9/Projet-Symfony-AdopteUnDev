# Utiliser l'image officielle PHP avec Apache pour Symfony
FROM php:8.2-apache

# Définir le répertoire de travail
WORKDIR /var/www

# Installer les dépendances système nécessaires
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zlib1g-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier les fichiers locaux dans le conteneur
COPY . .

# Installer les dépendances PHP
RUN composer install --no-scripts --no-interaction --no-plugins


# Configurer Apache pour Symfony
RUN a2enmod rewrite
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Définir les permissions nécessaires
# RUN chown -R www-data:www-data /var/www \
#     && chmod -R 775 /var/www/var

RUN mkdir -p /var/www/var \
&& chown -R www-data:www-data /var/www \
&& chmod -R 775 /var/www/var

# Exposer le port 8000
EXPOSE 8000

# Commande de démarrage
CMD ["apache2-foreground"]
