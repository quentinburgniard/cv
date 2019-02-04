#!/bin/bash
# https://docs.travis-ci.com/user/deployment/custom/#git
eval "$(ssh-agent -s)"
chmod 600 .travis/deploy.pem
ssh-add .travis/deploy.pem

# https://github.com/dwyl/learn-devops/issues/33
git fetch --unshallow 

git remote add deploy ssh://$CI_USER@$CI_HOST/~/$CI_DIRECTORY
git push deploy master:deploy
