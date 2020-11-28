#!/bin/bash
curl -X POST -H 'Content-type: application/json' --data "{\"text\":\"$1\"}" $2
