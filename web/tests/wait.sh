#!/bin/bash
while ! nc -z delain_dbtu 5432; do
  sleep 10
  echo "Waiting for postgres to be up"
done

until psql -h delain_dbtu -U delain -d delain -c "select 1" > /dev/null 2>&1 ; do
  echo "Waiting for postgres server, wait 10 seconds..."
  sleep 10
done
echo "Postgres UP !"