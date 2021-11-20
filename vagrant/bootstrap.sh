#!/bin/bash

# update / upgrade
echo "Updating apt-get..."
sudo apt-get update
sudo apt-get -y upgrade

sudo apt-get install build-essential apt-transport-https lsb-release ca-certificates

# install nginx
echo "Installing Nginx..."
sudo apt-get install -y nginx
echo "Installing Redis..."
sudo apt-get install -y redis-server
systemctl enable redis-server.service
# install php8
echo "Installing PHP..."
wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
sudo sh -c 'echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list'
apt-get update
sudo apt-get install -y php8.0-fpm php8.0-mysql php8.0-xml php8.0-gd php8.0-zip php8.0-redis php8.0-curl

# Nginx Config
echo "Configuring Nginx..."
sudo ln -s /home/kriekon/api/vagrant/nginx_vhost /etc/nginx/sites-enabled/nginx_vhost

sudo rm -rf /etc/nginx/sites-enabled/default
sudo rm -rf /var/www/html
#sudo mkdir /etc/nginx/certs
#cd /etc/nginx/certs
#sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/nginx/certs/local.api.kriekon.com.key -out /etc/nginx/certs/local.api.kriekon.com.crt -subj '/CN=local.api.kriekon.com'

echo "Restarting PHP..."
sudo systemctl restart php8.0-fpm
# Restarting Nginx for config to take effect
echo "Restarting Nginx..."
sudo systemctl restart nginx
