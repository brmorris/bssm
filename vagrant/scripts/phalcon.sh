#!/usr/bin/env bash

echo " *** Installing PHP and Phalcon 2..."
sudo add-apt-repository -y ppa:ondrej/php
sudo apt-get update

echo " *** Upgrading php to version 7"
sudo apt-get remove -y php5-common

sudo apt-get install -y php7.0-fpm \
                        php7.0-cli \
                        php7.0-curl \
                        php7.0-gd \
                        php7.0-intl \
                        php7.0-pgsql \
                        php7.0-mbstring \
                        php7.0-xml \
                        php7.0-mongodb \
                        php-msgpack \
                        curl \
                        vim \
                        wget \
                        git

sudo service nginx restart

echo " *** Installing phalcon for php version 7"

curl -s "https://packagecloud.io/install/repositories/phalcon/stable/script.deb.sh" | sudo bash

sudo apt-get install -y php7.0-phalcon

# restart php-fpm and nginx
sudo service php7.0-fpm restart
sudo service nginx restart
