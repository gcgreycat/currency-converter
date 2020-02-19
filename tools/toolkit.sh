#!/bin/bash

USER=1000
GROUP=1000

SCRIPT=$(readlink -f "$0")
PROJECTPATH=$(dirname "$SCRIPT")

CACHE_FOLDER="$PROJECTPATH/../cache"
SYMFONY_CACHE_FOLDER="$CACHE_FOLDER/symfony"
COMPOSER_CACHE_FOLDER="$CACHE_FOLDER/composer"

if [ ! -d "$CACHE_FOLDER" ]; then
  mkdir -m 0777 "$CACHE_FOLDER"
  chown -R "$USER":"$GROUP" "$CACHE_FOLDER"
fi

if [ ! -d "$SYMFONY_CACHE_FOLDER" ]; then
  mkdir -m 0777 "$SYMFONY_CACHE_FOLDER"
  chown -R "$USER":"$GROUP" "$SYMFONY_CACHE_FOLDER"
fi

if [ ! -d "$COMPOSER_CACHE_FOLDER" ]; then
  mkdir -m 0777 "$COMPOSER_CACHE_FOLDER"
  chown -R "$USER":"$GROUP" "$COMPOSER_CACHE_FOLDER"
fi

docker run --rm -ti \
  -u "$USER":"$GROUP" \
  -v "$PROJECTPATH/../":/usr/src/app \
  -v "$COMPOSER_CACHE_FOLDER":/.composer \
  -v "$SYMFONY_CACHE_FOLDER":/.symfony \
  -w /usr/src/app/application \
  currency-converter/toolkit:latest "$@"
