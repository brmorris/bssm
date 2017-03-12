#!/usr/bin/env bash

echo "Installing ELK stack ...taken from https://www.digitalocean.com/community/tutorials/how-to-install-elasticsearch-logstash-and-kibana-elk-stack-on-ubuntu-16-04"

echo -                    Add package repos                          -
echo -----------------------------------------------------------------

sudo add-apt-repository -y ppa:webupd8team/java
sudo apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 7F0CEB10
wget -qO - https://packages.elastic.co/GPG-KEY-elasticsearch | sudo apt-key add -

echo "deb http://packages.elastic.co/elasticsearch/2.x/debian stable main" | sudo tee -a /etc/apt/sources.list.d/elasticsearch-2.x.list
echo "deb http://packages.elastic.co/kibana/4.5/debian stable main" | sudo tee -a /etc/apt/sources.list
echo "deb http://packages.elastic.co/logstash/2.3/debian stable main" | sudo tee -a /etc/apt/sources.list

sudo apt-get update

echo -                    Install packages                           -
echo -----------------------------------------------------------------

sudo apt-get -y install oracle-java8-installer elasticsearch kibana logstash unzip

echo -                    Install beats-dashboards                   -
echo -----------------------------------------------------------------
cd ~
curl -L -O https://download.elastic.co/beats/dashboards/beats-dashboards-1.2.2.zip
unzip beats-dashboards-*.zip
cd beats-dashboards-*
./load.sh
cd ~
curl -O https://gist.githubusercontent.com/thisismitch/3429023e8438cc25b86c/raw/d8c479e2a1adcea8b1fe86570e42abab0f10f364/filebeat-index-template.json
curl -XPUT 'http://localhost:9200/_template/filebeat?pretty' -d@filebeat-index-template.json

echo -                    Configure: manual right now sorry mate     -
echo -----------------------------------------------------------------
echo "configuration steps at https://www.digitalocean.com/community/tutorials/how-to-install-elasticsearch-logstash-and-kibana-elk-stack-on-ubuntu-16-04"

echo -                    Configure: Setup User                      -
echo -----------------------------------------------------------------

sudo -v
echo "kibanaadmin:`openssl passwd -apr1`" | sudo tee -a /etc/nginx/htpasswd.users

