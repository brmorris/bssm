#!/usr/bin/env bash

echo "Configuring Firewall..."

sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw --force enable
sudo ufw status

echo "Completed Configuring Firewall"

