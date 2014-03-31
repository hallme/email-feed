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

if [[ $(git diff | grep readme.txt) ]]; then
	echo "Committing changes"
	git add readme.txt
	git commit -m"Update readme with new stable tag $version"
fi

echo "Tagging locally"
git tag $version

echo "Pushing tag"
git push --tags origin master

echo "Commiting to Wordpress SVN the latest version"
svn commit -m"Releasing version ${version}"

echo "Creating SVN tag for latest version"
svn copy http://plugins.svn.wordpress.org/${package}/trunk http://plugins.svn.wordpress.org/$package/tags/${version}

echo "Done"
