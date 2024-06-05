# syntax=docker/dockerfile:1.4
FROM node:20-alpine AS node_upstream

RUN mkdir -p /app
WORKDIR /app
COPY . .
RUN npm install
RUN npm run build
EXPOSE 3000
CMD ["npm", "start"]
