# Use the official PHP image with Apache
FROM php:8.3-apache
# Set the ServerName to localhost
RUN echo "ServerName localhost" | tee -a /etc/apache2/apache2.conf
ENV TZ=Europe/Paris
# Enable Apache mod_rewrite
RUN a2enmod rewrite
#https via ssl
RUN a2enmod ssl && \
    a2ensite default-ssl

# Generate a self-signed SSL certificate
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/apache-selfsigned.key \
    -out /etc/ssl/certs/apache-selfsigned.crt \
    -subj "/C=FR/ST=Occitanie/L=Paris/O=MonApp/CN=localhost"

# Configure Apache to use the self-signed certificate
RUN sed -i 's|/etc/ssl/certs/ssl-cert-snakeoil.pem|/etc/ssl/certs/apache-selfsigned.crt|g' \
        /etc/apache2/sites-available/default-ssl.conf && \
    sed -i 's|/etc/ssl/private/ssl-cert-snakeoil.key|/etc/ssl/private/apache-selfsigned.key|g' \
        /etc/apache2/sites-available/default-ssl.conf

# Expose port 443
EXPOSE 443

# Install necessary PHP extensions
RUN apt-get update && \
apt-get install -y \
libpq-dev \
tzdata \
libsqlite3-dev \
libaio-dev \
unzip \
nano \
acl \
wget && \
docker-php-ext-install pdo pdo_mysql pdo_pgsql pdo_sqlite
# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# Install npm
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.1/install.sh | bash && \
export NVM_DIR="$HOME/.nvm" && \
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh" && \
[ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion"&& \
nvm install --lts
RUN echo "date.timezone = Europe/Paris" > $PHP_INI_DIR/conf.d/timezone.ini
# Install Xdebug
RUN pecl install xdebug && \
docker-php-ext-enable xdebug
# Configure Xdebug
RUN echo "zend_extension=xdebug.so" > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
echo "xdebug.start_with_request=trigger" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
# PHP Ini development
RUN cp $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/conf.d/php.ini
# Set up the working directory
WORKDIR /var/www/html
# Copy the local project files to the container
# COPY . /var/www/html
# Expose port 80
EXPOSE 80
# Start Apache server
CMD ["apache2-foreground"]
