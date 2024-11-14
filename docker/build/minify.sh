#!/bin/bash

echo Minify JavaScript
JS_FILES=$(find html/js -type f -name '*.js')
for JS_FILE in $JS_FILES; do
    npx uglify-js $JS_FILE >$JS_FILE.min
    CHECKSUM=$(cksum $JS_FILE.min | cut -d' ' -f 1)
    mv $JS_FILE.min $JS_FILE.${CHECKSUM}.js
    sed -i "s/$(basename $JS_FILE)/$(basename $JS_FILE).${CHECKSUM}.js/g" app/src/Renderer/Templates/about.phtml
    sed -i "s/$(basename $JS_FILE)/$(basename $JS_FILE).${CHECKSUM}.js/g" app/src/Renderer/Templates/ipv6-test.phtml
done

echo Minify CSS
CSS_FILES=$(find html/css -type f -name '*.css')
for CSS_FILE in $CSS_FILES; do
    npx clean-css $CSS_FILE >$CSS_FILE.min
    CHECKSUM=$(cksum $CSS_FILE.min | cut -d' ' -f 1)
    mv $CSS_FILE.min $CSS_FILE.${CHECKSUM}.css
    sed -i "s/$(basename $CSS_FILE)/$(basename $CSS_FILE).${CHECKSUM}.css/g" app/src/Renderer/Templates/about.phtml
    sed -i "s/$(basename $CSS_FILE)/$(basename $CSS_FILE).${CHECKSUM}.css/g" app/src/Renderer/Templates/ipv6-test.phtml
done

echo Minify HTML Templates
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
