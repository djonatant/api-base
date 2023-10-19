# Use uma imagem PHP 8.2 com Apache como base
FROM php:8.2-apache

# Copie os arquivos do seu projeto para o contêiner
COPY . /var/www/html

# Instale as dependências do PHP, como o PDO para MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Habilite o mod_rewrite do Apache para permitir URLs amigáveis
RUN a2enmod rewrite

# Defina a variável de ambiente para o modo de desenvolvimento
ENV APP_ENV=development

# Exponha a porta 80 do contêiner
EXPOSE 80

RUN echo "xdebug.remote_enable=1" >> /usr/local/etc/php/php.ini
RUN echo "xdebug.remote_autostart=1" >> /usr/local/etc/php/php.ini
RUN echo "xdebug.remote_port=9000" >> /usr/local/etc/php/php.ini

# Inicialize o Apache
CMD ["apache2-foreground"]
