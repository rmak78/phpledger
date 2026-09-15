ARG PHP_VERSION=8.3.33
FROM php:${PHP_VERSION}-apache-bookworm
RUN docker-php-ext-install pdo_mysql bcmath \
    && a2enmod rewrite headers \
    && printf 'ServerName localhost\n' > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
RUN apt-get update -qq && apt-get install -y --no-install-recommends unzip \
    && rm -rf /var/lib/apt/lists/*
WORKDIR /var/www/phpledger
COPY composer.json composer.lock* ./
RUN composer install --no-interaction --prefer-dist --no-progress
COPY . .
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/phpledger.ini
