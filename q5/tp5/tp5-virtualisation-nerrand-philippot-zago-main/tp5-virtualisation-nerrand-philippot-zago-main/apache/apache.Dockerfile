FROM php:8.2-apache

RUN apt-get update -y && apt-get install -y \
    nano \
    git \
    libpq-dev \
    zip \
    && docker-php-ext-install pdo_mysql pgsql \
    && apt-get clean -y

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# Add Composer to the PATH
ENV PATH="$PATH:/usr/local/bin"
RUN composer require stevenmaguire/oauth2-keycloak
RUN composer install


RUN mkdir -p /usr/local/apache2/conf/ssl && \
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/localhost.key \
    -out /etc/ssl/certs/localhost.crt \
    -subj "/C=FR/ST=Occitanie/L=Sete/O=IUT/CN=tp5.local"

RUN cat > /etc/apache2/sites-available/my-ssl.conf <<EOF
<VirtualHost *:443>
    DocumentRoot "/var/www/html"
    ServerName tp5.local

    SSLEngine on
    SSLCertificateFile "/etc/ssl/certs/localhost.crt"
    SSLCertificateKeyFile "/etc/ssl/private/localhost.key"
</VirtualHost>
EOF

RUN a2enmod ssl && \
    a2enmod rewrite && \
    a2dissite 000-default default-ssl && \
    a2ensite my-ssl

EXPOSE 80 443

#RUN a2enmod rewrite && service apache2 restart