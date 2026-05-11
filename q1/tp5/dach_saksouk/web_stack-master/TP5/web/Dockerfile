FROM php:8.2-apache

# Extensions nécessaires : PDO MySQL + MySQLi + msmtp (sendmail vers Mailpit)
RUN apt-get update && apt-get install -y --no-install-recommends \
        libpng-dev \
        libjpeg-dev \
        msmtp \
        msmtp-mta \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mysqli \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Configure msmtp pour relayer vers Mailpit (SMTP sur mail_test:1025)
RUN printf 'defaults\nauth off\ntls off\n\naccount mailpit\nhost mail_test\nport 1025\nfrom noreply@tp5.local\n\naccount default : mailpit\n' \
    > /etc/msmtprc && chmod 644 /etc/msmtprc

# Indique à PHP d'utiliser msmtp comme sendmail
RUN echo 'sendmail_path = /usr/bin/msmtp -t' >> /usr/local/etc/php/conf.d/mail.ini

# Active mod_rewrite Apache (utile pour de futures règles)
RUN a2enmod rewrite

# Copie les sources PHP dans le document root
COPY . /var/www/html/

# Droits corrects
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && find /var/www/html -type d -exec chmod 755 {} \;
