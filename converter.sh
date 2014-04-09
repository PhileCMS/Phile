#!/usr/bin/env bash

for FILE in Phile.wiki/*.md
  do
    NAME=$(basename $FILE)
    marked -o html/${NAME%.*}.html $FILE --gfm
done

exit 0
