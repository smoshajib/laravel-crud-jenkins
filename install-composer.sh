#!/bin/bash
set -e
echo "📦 Updating packages and installing curl, unzip, git..."
apt-get update
apt-get install -y curl unzip git

echo "📦 Installing Composer..."
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --quiet
php -r "unlink('composer-setup.php');"
mv composer.phar /usr/local/bin/composer

echo "📦 Installing PHP dependencies with Composer..."
composer install --no-interaction --prefer-dist --optimize-autoloader
