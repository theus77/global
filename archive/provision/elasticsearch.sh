wget -qO - https://packages.elastic.co/GPG-KEY-elasticsearch | sudo apt-key add -
echo "deb http://packages.elastic.co/elasticsearch/2.x/debian stable main" | sudo tee -a /etc/apt/sources.list.d/elasticsearch-2.x.list
sudo apt-get update -y
sudo apt-get install -y default-jre
sudo apt-get install -y elasticsearch
sudo /usr/share/elasticsearch/bin/plugin install mobz/elasticsearch-head
sudo sed -i 's/# network.host: 192.168.0.1/network.host: ::0/i' /etc/elasticsearch/elasticsearch.yml
sudo update-rc.d elasticsearch defaults 95 10
sudo /etc/init.d/elasticsearch start
#bash /var/www/public/Console/cake elastic