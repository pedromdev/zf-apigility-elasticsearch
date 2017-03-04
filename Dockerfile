FROM composer/composer:php5

MAINTAINER Pedro Alves <pedromarcelodesaalves@gmail.com>

USER root

RUN wget http://xdebug.org/files/xdebug-2.5.1.tgz && \
    tar -xzf xdebug-2.5.1.tgz && \
    cd xdebug-2.5.1 && \
    phpize && \
    ./configure && \
    make && \
    cp modules/xdebug.so /usr/local/lib/php/extensions/no-debug-non-zts-20131226 && \
    echo "zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20131226/xdebug.so" >> /usr/local/etc/php/php.ini
