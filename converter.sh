#!/usr/bin/env bash

for FILE in Phile.wiki/*.md
  do
    NAME=$(basename "$FILE")
    node_modules/.bin/marked -o html/"${NAME%.*}".html "$FILE" --gfm
done

exit 0
