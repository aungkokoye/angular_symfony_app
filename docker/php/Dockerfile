FROM php:8.4-apache

# Install dependencies including supervisor
RUN apt-get update && apt-get install -y \
    vim libzip-dev libpng-dev libonig-dev zlib1g-dev \
    supervisor curl gnupg \
    && rm -rf /var/lib/apt/lists/*

# Install Xdebug
RUN yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.log=/tmp/xdebug.log" >> /usr/local/etc/php/conf.d/xdebug.ini

# Install PHP extensions
RUN docker-php-ext-install mbstring zip gd pdo_mysql mysqli bcmath

# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    rm -rf /var/lib/apt/lists/*

# Verify Node.js and npm versions
RUN node -v && npm -v

# Install Angular
RUN npm install -g @angular/cli

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/backend

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Create Supervisor log directories
RUN mkdir -p /var/log/supervisor

# Expose Apache
EXPOSE 80

# Launch Supervisor (which starts Apache and your script)
CMD ["/usr/bin/supervisord", "-n"]