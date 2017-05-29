#!/bin/bash
# repo name
REPOSITORY_ID='SAREhub/php_dockerutil'

if [ "$TRAVIS_REPO_SLUG" == "$REPOSITORY_ID" ] && [ $TRAVIS_BRANCH = 'master' ] && [ "$TRAVIS_PULL_REQUEST" == "false" ] && [ "$TRAVIS_PHP_VERSION" == "7.0" ]; then
    wget http://www.apigen.org/apigen.phar
    php apigen.phar generate -s src -d ../gh-pages --template-theme bootstrap --debug
    cd ../gh-pages
    git config --global user.email "travis@travis-ci.org"
    git config --global user.name "Travis"
    git init
    git remote add origin https://${GH_TOKEN}@github.com/"$REPOSITORY_ID".git > /dev/null
    git checkout -B gh-pages

    git add .
    git commit -m "APIGEN (Travis Build : $TRAVIS_BUILD_NUMBER  - Branch : $TRAVIS_BRANCH)"
    git push origin gh-pages -fq > /dev/null
fi