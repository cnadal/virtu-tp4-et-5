FROM node:18-bullseye

# Installation de PHP et des drivers BDD nécessaires
RUN apt-get update && apt-get install -y php php-pgsql php-mysql

WORKDIR /usr/src/app

COPY package*.json ./
RUN npm install
COPY . .

EXPOSE 3000
CMD [ "npm", "start"]