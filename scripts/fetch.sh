#!/bin/bash

# quick script to pull from main daff repository

set -e

if [ ! -e "scripts/fetch.sh" ]; then
    echo "Please run from root of repository"
    exit 1
fi

# assume we are at the same level as main daff repository
src=$PWD/../daff/php_bin/
dest=gen

if [ ! -e $src/lib/coopy/Coopy.class.php ]; then
    echo "Cannot find source daff, stopping"
    exit 1
fi

for php in `cd $src; find . -iname "*.php"`; do
    mkdir -p `dirname $dest/$php`
    if [ ! -e $dest/$php ]; then
	echo "Add $php"
	cp $src/$php $dest/$php
	git add $dest/$php
    else
	cp $src/$php $dest/$php
    fi
done

for php in `cd $dest; find . -iname "*.php"`; do
    if [ ! -e $src/$php ]; then
	echo "Remove $php"
	git rm $dest/$php
    fi
done

./scripts/tame.sh
