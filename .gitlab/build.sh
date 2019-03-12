#!/bin/bash

# We need to install dependencies only for Docker
[[ ! -e /.dockerenv ]] && exit 0

set -xe

# Install git (the php image doesn't have it) which is required by composer
apt-get update -yqq
apt install software-properties-common
add-apt-repository -y ppa:ondrej/php
apt update -yqq
apt install php7.1-cli
apt-get install git openjdk-8-jdk gcc g++ composer -yqq

# Install phpunit, the tool that we will use for testing
curl --location --output /usr/local/bin/phpunit https://phar.phpunit.de/phpunit-7.phar
chmod +x /usr/local/bin/phpunit

composer install

