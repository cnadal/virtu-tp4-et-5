FROM node:20-alpine

# Install PHP and PHP-CGI + SQLite3 extension
RUN apk add --no-cache php php-cgi php-sqlite3

WORKDIR /usr/src/app

COPY package*.json ./

RUN npm install

COPY . .

EXPOSE 3000

CMD ["npm", "start"]