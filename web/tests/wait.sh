#!/bin/bash
while ! nc -z delain_dbtu 5432; do
  sleep 10
  echo "Waiting for postgres to be up"
done
echo "Postgres UP !"