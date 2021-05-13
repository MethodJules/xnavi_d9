#/bin/bash
cd docker
docker-compose down
cd ..
rm -rf database
rm -rf docroot/web/sites/default/files/
#rm -rf docroot/web/sites/default/settings.php 