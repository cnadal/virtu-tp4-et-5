FROM node:latest AS web

WORKDIR /app

COPY web/package.json .

RUN npm install

COPY web/index.js .

CMD ["node", "."]