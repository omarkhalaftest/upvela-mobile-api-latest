FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libzip-dev \
    nginx \
    supervisor
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    libsqlite3-dev \
    libicu-dev \
    libbz2-dev \
    libxslt1-dev \
    libgmp-dev \
    libmcrypt-dev \
    libreadline-dev \
    libedit-dev \
    zlib1g-dev \
    libpq-dev \
    libwebp-dev \
    libjpeg-dev \
    libxpm-dev \
    libvpx-dev \
    libmagickwand-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
    bcmath \
    bz2 \
    calendar \
    exif \
    gd \
    gettext \
    intl \
    mbstring \
    opcache \
    pcntl \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    pdo_sqlite \
    shmop \
    soap \
    sockets \
    sysvmsg \
    sysvsem \
    sysvshm \
    xsl \
    zip \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy existing application directory contents
COPY . .

# Copy nginx configuration file
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Supervisor configuration for running both Nginx and PHP-FPM
COPY supervisor.conf /etc/supervisor/conf.d/supervisor.conf

# Install Laravel dependencies without dev dependencies for production
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader

# Ensure the storage and bootstrap/cache directories exist
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www//html/storage /var/www/html/bootstrap/cache

# Expose ports
EXPOSE 80 9000

# Start Supervisor to run both PHP-FPM and Nginx
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisor.conf"]
