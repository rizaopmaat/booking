FROM php:8.2-cli

# Installeer systeemafhankelijkheden
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    nodejs \
    npm

# Maak cache leeg
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Installeer PHP-extensies
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Haal recente Composer op
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Stel werkdirectory in
WORKDIR /var/www

# Kopieer bestaande applicatie
COPY . .

# Installeer afhankelijkheden
RUN composer install --no-interaction

# Stel juiste machtigingen in
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0"] 