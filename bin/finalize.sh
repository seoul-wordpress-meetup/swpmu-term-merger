#!/usr/bin/env bash

if [ $# -lt 1 ]; then
  echo "usage: $0 <dest-path>"
  exit 1
fi

SRC=$(dirname "$(dirname "$(readlink -f "$0")")")
DST=$(readlink -f "$1")

if [ -e "$DST" ]; then
  echo "Destination path exists."
  exit 1
fi

ITEMS=(
  'conf'
  'dist'
  'dist/.vite'
  'inc'
  'languages'
  'vendor/bojaghi'
  'vendor/composer'
  'vendor/psr'
  'vendor/autoload.php'
  'vite/src'
  'vite/eslint.config.js'
  'vite/package.json'
  'vite/pnpm-lock.yaml'
  'vite/postcss.config.js'
  'vite/tsconfig.app.json'
  'vite/tsconfig.json'
  'vite/tsconfig.node.json'
  'vite/vite.config.json'
  'composer.json'
  'composer.lock'
  'index.php'
  'LICENSE'
  'readme.txt'
)

for ITEM in "${ITEMS[@]}"
do
  SRC_PATH="$SRC/$ITEM"
  DST_PATH="$DST/$ITEM"
  if [ -d "$SRC_PATH" ]; then
    mkdir -p "$DST_PATH"
    cp -r "$SRC_PATH"/* "$DST_PATH"
  elif [ -f "$SRC_PATH" ]; then
    cp "$SRC_PATH" "$DST_PATH"
  fi
done
