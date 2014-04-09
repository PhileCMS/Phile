#!/usr/bin/env bash

for FILE in Phile.wiki/*.md
  do
    # echo "file - $FILE"
    marked -o ${FILE%.*}.html $FILE --gfm
done

exit 0
