#!/bin/bash

set -e

version=$1

package=$(basename $(pwd))
if [[ ! $version ]]; then
	echo "Needs a version number as argument"
	exit 1
fi

echo "Releasing version ${version}"

echo "Setting version number in readme.txt"
sed -i "s/Stable tag: .*/Stable tag: ${version}/" readme.txt
sed -i "s/Version:           .*/Version:           ${version}/" ${package}.php
sed -i "s/const VERSION = '.*';/const VERSION = '${version}';/" ${package}.php

if ([[ $(git st | grep readme.txt) ]] || [[ $(git st | grep ${package}.php) ]]); then
	echo "Committing changes"
	git add readme.txt
	git add ${package}.php
	git commit -m"Update readme with new stable tag $version"
fi

echo "Tagging locally"
git tag $version

echo "Pushing tag to git"
git push --tags origin master

echo "Commiting version ${version} to Wordpress SVN"
svn up
svn commit -m"Releasing version ${version}"

echo "Creating version ${version} SVN tag"
svn up
svn copy http://plugins.svn.wordpress.org/${package}/trunk http://plugins.svn.wordpress.org/$package/tags/${version} -m"Creating new ${version} tag"

echo "Done"
