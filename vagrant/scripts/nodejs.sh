#!/usr/bin/env bash

echo "Installing NodeJS..."

sudo apt-get install -y nodejs npm; true
sudo ln -s /usr/bin/nodejs /usr/bin/node; true
sudo npm -g update npm; true

echo "Installing Gulp and Bower for global env..."

sudo npm -g -q install bower; true
sudo npm -g -q install gulp; true
sudo npm -g -q install grunt-cli; true

echo "Completed: Installing Gulp and Bower for global env..."
