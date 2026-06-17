# 1. Usa a imagem oficial do PHP versão 8.2 com servidor Apache
FROM php:8.2-apache

# 2. Habilita o mod_rewrite do Apache (essencial para o seu arquivo .htaccess funcionar no Render)
RUN a2enmod rewrite

# 3. Instala as extensões do PHP necessárias para conectar ao banco de dados (MySQL/MariaDB)
RUN docker-php-ext-install pdo pdo_mysql mysqli

# 4. Copia todos os arquivos do seu repositório para a pasta pública do Apache
COPY . /var/www/html/

# 5. Ajusta as permissões das pastas para que o servidor possa ler/gravar arquivos (útil para a sua pasta 'uploads' e 'logs')
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# 6. Expõe a porta 80 para a internet
EXPOSE 80