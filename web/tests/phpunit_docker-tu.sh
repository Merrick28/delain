#!/bin/bash
# on attend la connection à postgres
while ! nc -z delain_dbtu 5432; do
  sleep 10
  echo "Waiting for postgres to be up"
done
docker exec webtu /home/delain/delain/web/vendor/bin/phpunit /home/delain/delain/web/tests/
