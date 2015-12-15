#!/bin/bash

# quick script to pull from main daff repository

set -e

if [ ! -e "scripts/fetch.sh" ]; then
    echo "Please run from root of repository"
    exit 1
fi

# assume we are at the same level as main daff repository
src=$PWD/../daff/ntest_php_dir/
dest=gen

if [ ! -e $src/lib/harness/Main.class.php ]; then
    echo "Cannot find generated tests, stopping"
    exit 1
fi

for php in `cd $src/lib/harness; find . -iname "*.php"`; do
    name=${php//.class/}
    if [ ! "k$name" = "k./Main.php" ] ; then
	cp -v $src/lib/harness/$php test/gen/$name
    fi
done

# no sql support in daff-php yet
rm -f test/gen/SqlTest.php

