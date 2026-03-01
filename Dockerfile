# Render: Docker-based Web Service
# PHP + Apache + PDO_PGSQL (Supabase Postgres)

FROM php:8.2-apache

# Dependencias para pdo_pgsql
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-install pdo_pgsql curl \
    && rm -rf /var/lib/apt/lists/*

# Habilitar mod_rewrite (por si luego quieres rutas limpias)
RUN a2enmod rewrite headers

# DocumentRoot -> /public
RUN sed -i 's#/var/www/html#/var/www/html/public#g' /etc/apache2/sites-available/000-default.conf \
    && printf '<Directory /var/www/html/public>\nAllowOverride All\nRequire all granted\n</Directory>\n' > /etc/apache2/conf-available/public-dir.conf \
    && a2enconf public-dir

# Copiar proyecto
WORKDIR /var/www/html
COPY . /var/www/html

# Script para escuchar en el puerto de Render ($PORT, por defecto 10000)
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 10000
ENTRYPOINT ["docker-entrypoint.sh"]
