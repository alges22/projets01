mkdir -p ~/.ssh && echo "$SSH_PRIVATE_KEY" | tr -d '\r' > ~/.ssh/id_rsa && chmod 600 ~/.ssh/id_rsa
apt-get update
apt-get install git openssh-client -y
ssh-keyscan -H "$DOKKU_HOST" >> ~/.ssh/known_hosts
git push dokku@$DOKKU_HOST:$DOKKU_PROJECT_NAME deploy:master
