# Use PHP + Apache
FROM php:8.2-apache

# Install python (because you have .py scripts)
RUN apt-get update && apt-get install -y python3 python3-pip

# Enable PHP extensions for MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy ALL project files into Apache document root
COPY . /var/www/html/

# Move backend PHP files into main web directory
RUN cp -r /var/www/html/backend/* /var/www/html/ || true

# Expose HTTP port
EXPOSE 80

# Start Apache web server
CMD ["apache2-foreground"]
