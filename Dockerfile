# 1. Usa a imagem oficial do PHP versão 8.2 com servidor Apache
FROM php:8.2-apache

# 2. Instala as dependências do sistema necessárias para o PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# 3. Instala as extensões do PHP para conexão com o PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql pgsql

# 4. Habilita o mod_rewrite do Apache (essencial para o arquivo .htaccess)
RUN a2enmod rewrite

# 5. Copia todos os arquivos do repositório para a pasta pública do Apache
COPY . /var/www/html/

# 6. Ajusta as permissões das pastas para leitura e gravação
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# 7. Expõe a porta 80 para a internet
EXPOSE 80