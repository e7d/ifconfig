#!/bin/bash

echo Minify JavaScript
JS_FILES=$(find html/js -type f -name '*.js')
for JS_FILE in $JS_FILES; do
    JS_MIN_FILE=$(echo $JS_FILE | sed 's/\.js$/.min.js/')
    npx uglify-js $JS_FILE >$JS_MIN_FILE
    CHECKSUM=$(cksum $JS_FILE.min | cut -d' ' -f 1)
    JS_CHECKSUM_FILE=$(echo $JS_FILE | sed "s/\.js/.${CHECKSUM}.js/")
    mv $JS_FILE.min $JS_CHECKSUM_FILE
    sed -i "s/$(basename $JS_FILE)/$(basename $JS_CHECKSUM_FILE)/g" app/src/Renderer/Templates/about.phtml
    sed -i "s/$(basename $JS_FILE)/$(basename $JS_CHECKSUM_FILE)/g" app/src/Renderer/Templates/ipv6-test.phtml
done

echo Minify CSS
CSS_FILES=$(find html/css -type f -name '*.css')
for CSS_FILE in $CSS_FILES; do
    CSS_FILE_DIR=$(dirname $CSS_FILE)
    CSS_MIN_FILE=$(echo $CSS_FILE | sed 's/\.css$/.min.css/')
    npx css-minify -f $CSS_FILE -o $CSS_FILE_DIR
    CHECKSUM=$(cksum $CSS_MIN_FILE | cut -d' ' -f 1)
    CSS_CHECKSUM_FILE=$(echo $CSS_MIN_FILE | sed "s/\.min\.css/.${CHECKSUM}.min.css/")
    mv $CSS_MIN_FILE $CSS_CHECKSUM_FILE
    sed -i "s/$(basename $CSS_FILE)/$(basename $CSS_CHECKSUM_FILE)/g" app/src/Renderer/Templates/about.phtml
    sed -i "s/$(basename $CSS_FILE)/$(basename $CSS_CHECKSUM_FILE)/g" app/src/Renderer/Templates/ipv6-test.phtml
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
