#!/bin/bash

# update / upgrade
sudo add-apt-repository ppa:ondrej/php -y
echo "Updating apt-get..."
sudo apt-get update
sudo apt-get -y upgrade

sudo apt-get install build-essential

# install nginx
echo "Installing Nginx..."
sudo apt-get install -y nginx
echo "Installing Redis..."
sudo apt-get install -y redis-server
systemctl enable redis-server.service
# install php7-fpm
echo "Installing PHP..."
sudo apt-get install -y php7.4-fpm php7.4-mysql php7.4-xml php7.4-gd php7.4-zip php7.4-redis php7.4-curl

# install libvirt-dev
sudo apt-get install -y libvirt-dev qemu-kvm libvirt-bin

# Nginx Config
echo "Configuring Nginx..."
sudo ln -s /home/kriekon/api/vagrant/nginx_vhost /etc/nginx/sites-enabled/nginx_vhost

sudo rm -rf /etc/nginx/sites-enabled/default
sudo rm -rf /var/www/html
#sudo mkdir /etc/nginx/certs
#cd /etc/nginx/certs
#sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/nginx/certs/local.api.kriekon.com.key -out /etc/nginx/certs/local.api.kriekon.com.crt -subj '/CN=local.api.kriekon.com'

echo "Restarting PHP..."
sudo systemctl restart php7.4-fpm
# Restarting Nginx for config to take effect
echo "Restarting Nginx..."
sudo service nginx restart

echo 'Installing Go...'
cd /tmp
wget https://dl.google.com/go/go1.14.linux-amd64.tar.gz
sudo tar -xvf go1.14.linux-amd64.tar.gz
sudo mv go /usr/local
sudo rm -rf go1.14.linux-amd64.tar.gz

export GOROOT=/usr/local/go
export GOPATH=/home/kriekon
export PATH=$GOPATH/bin:$GOROOT/bin:$PATH
source ~/.profile