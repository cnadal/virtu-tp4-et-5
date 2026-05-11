FROM node:20-alpine
WORKDIR /app
COPY package*.json ./
RUN npm install

# Copier le dossier public (HTML)
COPY ./www/ ./www/

# Copier les scripts backend NodeJS reproduisant les scripts PHP
COPY server.js ./
COPY listeVoitures.js ./
COPY ajoutVoituresGarage.js ./

# Copier le dossier bd
RUN mkdir -p bd
COPY bd/creationBDD.js ./bd/
COPY bd/garageM.sql ./bd/

EXPOSE 3000
CMD ["npm", "start"]
