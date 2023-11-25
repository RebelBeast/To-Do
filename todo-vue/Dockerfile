# Stage 1: Build the Vue.js application
FROM node:18-alpine as build-stage
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: Setup Apache to serve the Vue.js application
FROM httpd:2.4
COPY --from=build-stage /app/dist/ /usr/local/apache2/htdocs/