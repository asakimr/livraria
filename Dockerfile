FROM serversideup/php:8.3-cli

# Troca para root temporariamente para instalar a extensão intl
USER root
RUN install-php-extensions intl pdo_mysql

# Retorna para o usuário padrão da imagem
#USER www-data

WORKDIR /var/www/html
