#!/usr/bin/env bash

if [ $# -lt 1 ]; then
  echo "usage: $0 <dest-path>"
  exit 1
fi

SRC=$(dirname "$(dirname "$(readlink -f "$0")")")
TAR=$(readlink -f "$1")
DST="$TAR/swpmu-term-merger"

if [ "$SRC" == "TAR" ]; then
  echo "DST equals to SRC."
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
  'vite/tailwind.config.js'
  'vite/tsconfig.app.json'
  'vite/tsconfig.json'
  'vite/tsconfig.node.json'
  'vite/vite.config.json'
  'composer.json'
  'composer.lock'
  'index.php'
  'LICENSE'
  'readme.txt'
  'swpmu-term-merger.php'
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

# Remove bojaghi bin, tests
find "$DST/vendor/bojaghi" -maxdepth 2 -type d  \( -name 'bin' -o -name 'tests' \) -exec rm -rf {} \;

# Remove all .dic files under bojaghi
find "$DST/vendor/bojaghi" -maxdepth 2 -type f -name 'custom.dic' -exec rm {} \;
