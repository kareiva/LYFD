# Build stage for Node
FROM node:20-alpine as builder

WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# PHP stage
FROM php:8.2-cli
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Set up working directory
WORKDIR /app

# Copy built assets
COPY --from=builder /app/dist .

# Expose port
EXPOSE 8080

# Start PHP built-in server
CMD ["php", "-S", "0.0.0.0:8080"] 