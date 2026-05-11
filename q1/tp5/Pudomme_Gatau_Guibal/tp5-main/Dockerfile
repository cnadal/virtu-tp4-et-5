FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    msmtp \
    && rm -rf /var/lib/apt/lists/*

RUN mkdir -p /var/log/msmtp && chown www-data:www-data /var/log/msmtp
RUN echo "defaults" > /etc/msmtprc && \
    echo "auth off" >> /etc/msmtprc && \
    echo "tls off" >> /etc/msmtprc && \
    echo "logfile /var/log/msmtp/msmtp.log" >> /etc/msmtprc && \
    echo "account default" >> /etc/msmtprc && \
    echo "host mail_test" >> /etc/msmtprc && \
    echo "port 1025" >> /etc/msmtprc && \
    echo "from app@tp5.local" >> /etc/msmtprc && \
    chown www-data:www-data /etc/msmtprc && \
    chmod 600 /etc/msmtprc

RUN echo "sendmail_path = /usr/bin/msmtp -t -i" > /usr/local/etc/php/conf.d/sendmail.ini

RUN docker-php-ext-install pdo pdo_mysql mysqli curl

RUN a2enmod rewrite

EXPOSE 80
