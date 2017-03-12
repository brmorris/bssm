#!/usr/bin/env bash

echo "Restarting php5-fpm ..."
sudo service php5-fpm restart
echo "Completed: Restarting php5-fpm ..."

echo "Configuring sudoers.d ..."

sudo cp /vagrant/vagrant/etc/sudoers.d/www-data /etc/sudoers.d/www-data

echo "Completed: post-install.sh. Vagrant provisioning is all done."
