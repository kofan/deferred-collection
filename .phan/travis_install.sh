#!/usr/bin/env bash

git clone https://github.com/nikic/php-ast.git
cd php-ast
phpize
./configure
make
make install
cd ..
echo "extension=ast.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
