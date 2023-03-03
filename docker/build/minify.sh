#!/bin/bash

echo Minify JavaScript
npx uglify-es html/js/query.js >html/js/query.min.js
CHECKSUM=$(cksum html/js/query.min.js | cut -d' ' -f 1)
mv html/js/query.min.js html/js/query.${CHECKSUM}.js
sed -i "s/query\.js/query.${CHECKSUM}.js/g" app/src/Renderer/Templates/about.phtml
rm html/js/query.js

echo Minify HTML Templates and CSS
npx html-minifier \
    --collapse-boolean-attributes \
    --collapse-whitespace \
    --decode-entities \
    --minify-css true \
    --minify-js true \
    --minify-urls \
    --remove-attribute-quotes \
    --remove-comments \
    --remove-empty-attributes \
    --remove-empty-elements \
    --remove-optional-tags \
    --remove-redundant-attributes \
    --remove-script-type-attributes \
    --remove-style-link-type-attributes \
    --remove-tag-whitespace \
    --sort-attributes \
    --sort-class-name \
    --use-short-doctype \
    --input-dir app/src/Renderer/Templates \
    --output-dir app/src/Renderer/Templates/min
mv app/src/Renderer/Templates/min/* app/src/Renderer/Templates/
