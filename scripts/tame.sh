#!/bin/bash

# make sure haxe does not do any funny stuff

set -e

if [ ! -e "scripts/tame.sh" ]; then
    echo "Please run from root of repository"
    exit 1
fi

# make sure haxe does not hijack error handling
sed -i "s|^error_reporting|//error_reporting|" gen/lib/php/Boot.class.php
sed -i "s|^set_error_handler|//set_error_handler|" gen/lib/php/Boot.class.php
sed -i "s|^set_exception_handler|//set_exception_handler|" gen/lib/php/Boot.class.php

# do not try to write a cache file to a random location
sed -i "s|\tfile_put_contents|\t//file_put_contents|" gen/lib/php/Boot.class.php
