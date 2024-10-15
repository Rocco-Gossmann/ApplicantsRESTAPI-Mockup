FROM ubuntu/apache2
COPY ./000-default.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

RUN <<EOF

echo "composer install -q & " >> /root/startup.sh
echo "apache2-foreground" >> /root/startup.sh

EOF

RUN <<EOF

apt update 
apt install -y wget zip \
php8.3 php-curl php8.3-xdebug php8.3-intl \
php8.3-dom php8.3-mysql php8.3-sqlite3 php8.3-mbstring

a2enmod rewrite

cd 
wget https://getcomposer.org/download/2.8.1/composer.phar
chmod +x composer.phar
mv composer.phar /usr/local/bin/composer

cat <<PHPINI >> /etc/php/8.3/apache2/php.ini

; Docker specific overrides
;===============================================================================
[PHP]
display_errors = On
html_errors = On
; requested by CakePHP
zend.assertions = 1 

[XDebug]
xdebug.mode=develop,debug
xdebug.client_host = host.docker.internal
xdebug.client_port = 9003
xdebug.start_with_request = trigger
xdebug.trigger_value = "VSCODE"

PHPINI

EOF

CMD ["sh", "/root/startup.sh"]
