Getting Started With Chaplean Bundle
=======================================

# Prerequisites

Fork me

# Initialization

Installation step process:

1. Replace `Geolocation` by name of bundle + files:
    * ChapleanGeolocationBundle.php
    * DependencyInjection/ChapleanGeolocationExtension.php
2. Remove index.feature if is useless

# Scrutinizer

Add the fork project in scrutinizer

# Codeship

Config global:

``` bash
# Set php version through phpenv. 5.3, 5.4 and 5.5 available
phpenv local 5.5
# Copy files
cp phpunit.xml.dist phpunit.xml
cp app/config/parameters.yml.dist app/config/parameters.yml
# Configuration
echo "memory_limit = 512M" >> ~/.phpenv/versions/5.5/etc/php.ini
echo "xdebug.max_nesting_level = 250" >> ~/.phpenv/versions/5.5/etc/php.ini
# Install dependencies through Composer
composer install --prefer-source --no-interaction
```

Pipeline Phpunit:

``` bash
phpunit --coverage-clover build/logs/clover.xml
# PHPUnit and Scruti
wget https://scrutinizer-ci.com/ocular.phar
php ocular.phar code-coverage:upload --access-token="7c2737daabf4aeb9d382cbde4d3a9cfb6a408fa4ec597c2c92c295e4fbbb4cfc" --format=php-clover build/logs/clover.xml
```

Pipeline Behat:

``` bash
# Behat
nohup bash -c "java -jar bin/selenium-server-standalone-2.45.0.jar 2>&1 &"
# Launch Web Server
php bin/console server:start localhost:8080
# Test
bin/behat -n
```