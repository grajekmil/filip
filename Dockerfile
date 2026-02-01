FROM php:8.2-apache

# Instalacja rozszerzeń PHP dla MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Włączenie mod_rewrite (opcjonalnie, przydatne dla routerów)
RUN a2enmod rewrite
