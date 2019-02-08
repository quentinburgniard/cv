#!/bin/bash
# https://docs.travis-ci.com/user/deployment/custom/#git
eval "$(ssh-agent -s)"
chmod 600 .travis/deploy.pem
ssh-add .travis/deploy.pem
ssh -t $CI_USER@$CI_HOST "git reset --hard ${CI_DIRECTORY}"
ssh -t $CI_USER@$CI_HOST "git pull ${CI_DIRECTORY}"
