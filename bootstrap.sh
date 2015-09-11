#!/usr/bin/env bash
su vagrant
ln -s /var/www/public/Config/database.vagrant.php /var/www/public/Config/database.php
ln -s /var/www/public/Config/core.vagrant.php /var/www/public/Config/core.php
ln -s /var/www/public/Config/bootstrap.vagrant.php /var/www/public/Config/bootstrap.php
cd /var/www/public
composer update
bower update
gem install compass
compass compile
mysql -u root scotchbox  < Config/Schema/dump.sql
