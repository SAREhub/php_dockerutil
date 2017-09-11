#!/usr/bin/env bash

OPTS=`getopt -l repo-host,repo,tag,dockerfile,context: -- -l "$@"`
eval set -- "$OPTS"

REPOSITORY_HOST='localhost:5000'
REPOSITORY=''
TAG='test'
DOCKERFILE='Dockerfile'
CONTEXT='.'

while true ; do
    case "$1" in
        --repo-host) REPOSITORY_HOST=$2; shift 2;;
        --repo) REPOSITORY=$2; shift 2;;
        --tag) TAG=$2; shift 2;;
        --dockerfile) DOCKERFILE=$2; shift 2;;
        --context) CONTEXT=$2; shift 2;;
        --) shift; break;;
    esac
done

echo "REPOSITORY_HOST: $REPOSITORY_HOST"
echo "REPOSITORY: $REPOSITORY"
echo "TAG: $TAG"
echo "DOCKERFILE: $DOCKERFILE"
echo "CONTEXT: $CONTEXT"

IMAGE_TAG="${REPOSITORY_HOST}/${REPOSITORY}:$TAG"

docker build --file $DOCKERFILE --tag $IMAGE_TAG --compress $CONTEXT
docker push $IMAGE_TAG