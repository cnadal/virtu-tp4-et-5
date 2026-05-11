# Use the official PHP image with Apache
FROM php:8.4-apache

RUN echo "ServerName web_app" | tee -a /etc/apache2/apache2.conf

RUN apt-get update && \
    apt-get install -y \
        libpq-dev \
        libsqlite3-dev \
        libaio-dev \
        unzip \
        nano \
        acl \
        wget \
        git \
        libzip-dev \
        msmtp \
        msmtp-mta && \
    docker-php-ext-install pdo pdo_mysql pdo_pgsql pdo_sqlite zip && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN echo "account default\nhost mail_test\nport 1025\ntls off\nfrom test@tp5.local\n" > /etc/msmtprc && \
    chmod 600 /etc/msmtprc && \
    chown www-data:www-data /etc/msmtprc

RUN cp $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/conf.d/php.ini && \
    echo 'sendmail_path = "/usr/bin/msmtp -t"' >> $PHP_INI_DIR/conf.d/php.ini

WORKDIR /var/www/html

EXPOSE 80

CMD bash -c "if [ -f composer.json ]; then composer install --no-interaction; fi && apache2-foreground"