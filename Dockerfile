FROM php:8.2-apache-bullseye

# Extensions PHP
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libwebp-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install pdo pdo_mysql mbstring gd fileinfo \
    && a2enmod rewrite headers expires deflate

# Copier le projet
COPY . /var/www/html/

# DocumentRoot → public/
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf

# AllowOverride pour .htaccess
RUN printf '<Directory /var/www/html/public>\n    AllowOverride All\n    Options -Indexes +FollowSymLinks\n    Require all granted\n</Directory>\n' >> /etc/apache2/apache2.conf

# Dossiers écriture
RUN mkdir -p /var/www/html/public/assets/uploads/profils \
             /var/www/html/public/assets/uploads/documents \
             /var/www/html/public/assets/uploads/articles \
             /var/www/html/public/assets/uploads/projets \
             /var/www/html/public/assets/uploads/partenaires \
             /var/www/html/public/assets/uploads/temoignages \
             /var/www/html/storage/logs \
             /var/www/html/storage/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/public/assets/uploads /var/www/html/storage

EXPOSE 80
CMD ["apache2-foreground"]
