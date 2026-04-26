FROM php:8.3-cli

WORKDIR /app

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libssl-dev \
        pkg-config \
        default-mysql-client \
    && docker-php-ext-install mysqli \
    && pecl install redis mongodb \
    && docker-php-ext-enable redis mongodb \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json ./
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

COPY . .

RUN chmod +x /app/start.sh

EXPOSE 10000

CMD ["/app/start.sh"]
