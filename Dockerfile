FROM php:8.2-fpm

# Instalamos dependencias del sistema y extensiones necesarias
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    libzip-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libwebp-dev \
    libxpm-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp --with-xpm \
    && docker-php-ext-install pdo_mysql mbstring zip gd ftp

# Instalar dependencias y mysql-client
RUN apt-get update && apt-get install -y \
    default-mysql-client

# Configurar PHP para permitir archivos grandes
RUN echo "upload_max_filesize = 2048M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 2048M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 2048M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 600" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_input_time = 600" >> /usr/local/etc/php/conf.d/uploads.ini

# Configuramos el directorio de trabajo
WORKDIR /var/www

# Copiamos el proyecto
COPY . .

# Instalamos Composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

#RUN composer install

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
