#!/bin/bash

# optional tweak version
tweak="$1"

# check version
version=`php gen/index.php version`
if [ ! "k$tweak" = "k" ]; then
    version="$version.$tweak"
fi
echo $version
echo read x
read x
git show v$version || {
    git commit -m "$version" -a
    git tag -a "v$version" -m "$version"
}
