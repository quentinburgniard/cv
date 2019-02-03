#!/bin/bash
eval "$(ssh-agent -s)"
chmod 600 .travis/deploy.pem
ssh-add .travis/deploy.pem
git remote add deploy ssh://$CI_USER@$CI_HOST:$CI_DIRECTORY
git push deploy