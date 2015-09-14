#!/usr/bin/env bash

export PATH="$HOME/.rbenv/bin:$PATH"
eval "$(rbenv init -)"
export PATH="$HOME/.rbenv/plugins/ruby-build/bin:$PATH"
export PATH="$HOME/.rbenv/bin:$PATH"
eval "$(rbenv init -)"
export PATH="$HOME/.rbenv/plugins/ruby-build/bin:$PATH"


echo "\n--- Create vagrant config symlinks ---\n"
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

echo "\n--- Composer (Load cakePHP, ApertureConnector, ...) ---\n"
composer update

echo "\n--- Bower (Bootstrap, CKEditor, jquery, ...) ---\n"
bower update

gem install compass

compass compile

echo "\n--- Load default DB dump ---\n"
mysql -uroot -proot scotchbox  < /var/www/public/Config/Schema/dump.sql


#echo "\n--- Adds a crontab entry to watch sass at reboot ---\n"
## Cron expression
#cron="@reboot compass watch --poll /var/www/public/"
##cron="5 * * * * curl http://www.google.com"
#    # │ │ │ │ │
#    # │ │ │ │ │
#    # │ │ │ │ └───── day of week (0 - 6) (0 to 6 are Sunday to Saturday, or use names; 7 is Sunday, the same as 0)
#    # │ │ │ └────────── month (1 - 12)
#    # │ │ └─────────────── day of month (1 - 31)
#    # │ └──────────────────── hour (0 - 23)
#    # └───────────────────────── min (0 - 59)
#
## Escape all the asterisks so we can grep for it
#cron_escaped=$(echo "$cron" | sed s/\*/\\\\*/g)
#
## Check if cron job already in crontab
#crontab -l | grep "${cronescaped}"
#if [ $? -eq 0 ] ;
#  then
#    echo "Crontab already exists. Exiting..."
#    exit
#  else
#    # Write out current crontab into temp file
#    crontab -l > mycron
#    # Append new cron into cron file
#    echo "$cron" >> mycron
#    # Install new cron file
#    crontab mycron
#    # Remove temp file
#    rm mycron
#fi
