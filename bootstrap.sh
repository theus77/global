#!/usr/bin/env bash

export PATH="$HOME/.rbenv/bin:$PATH"
eval "$(rbenv init -)"
export PATH="$HOME/.rbenv/plugins/ruby-build/bin:$PATH"
export PATH="$HOME/.rbenv/bin:$PATH"
eval "$(rbenv init -)"
export PATH="$HOME/.rbenv/plugins/ruby-build/bin:$PATH"


echo -e "\n--- Create vagrant config symlinks ---\n"
if ! [ -L /var/www/public/Config/database.php ]; then
 ln -s /var/www/public/Config/database.vagrant.php /var/www/public/Config/database.php
fi
if ! [ -L /var/www/public/Config/core.php ]; then
	ln -s /var/www/public/Config/core.vagrant.php /var/www/public/Config/core.php
fi
if ! [ -L /var/www/public/Config/bootstrap.php ]; then
	ln -s /var/www/public/Config/bootstrap.vagrant.php /var/www/public/Config/bootstrap.php
fi

cd /var/www/public/

echo -e "\n--- Composer (Load cakePHP, ApertureConnector, ...) ---\n"
composer update

echo -e "\n--- Bower (Bootstrap, CKEditor, jquery, ...) ---\n"
bower update

gem install compass

compass compile

echo -e "\n--- Load default DB dump ---\n"
mysql -uroot -proot scotchbox  < /var/www/public/Config/Schema/dump.sql
