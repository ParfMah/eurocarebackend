FROM mlocati/php-extension-installer AS installer

FROM php:8.2-apache-bullseye

# Récupération de l'installateur d'extensions
COPY --from=installer /usr/bin/install-php-extensions /usr/bin/

# Installation immédiate des extensions PHP nécessaires
RUN install-php-extensions pdo pdo_mysql mbstring gd fileinfo

# Configuration du DocumentRoot vers public/
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf

# LA LIGNE CRUCIALE : Désactivation du module MPM en conflit et activation des bons modules
RUN a2dismod mpm_event && a2enmod mpm_prefork rewrite headers expires deflate

# Copier les fichiers du projet backend
COPY . /var/www/html/

# Création des dossiers de stockage et gestion des permissions
RUN mkdir -p /var/www/html/public/assets/uploads/profils \
             /var/www/html/public/assets/uploads/documents \
             /var/www/html/public/assets/uploads/articles \
             /var/www/html/public/assets/uploads/projets \
             /var/www/html/public/assets/uploads/partenaires \
             /var/www/html/public/assets/uploads/temoignages \
             /var/www/html/storage/logs \
             /var/www/html/storage/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]