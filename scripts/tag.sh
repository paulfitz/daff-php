#!/bin/bash

# check version
version=`php gen/index.php version`
echo $version
echo read x
read x
git show v$version || {
    git commit -m "$version" -a
    git tag -a "v$version" -m "$version"
}
