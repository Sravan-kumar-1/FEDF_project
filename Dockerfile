# Use PHP with Apache
FROM php:8.2-apache

# Install system packages and Python if needed
RUN apt-get update && apt-get install -y python3 python3-pip

# Install PHP extensions (customize based on your needs)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Optional: Install Python dependencies (if you have a requirements.txt)
COPY requirements.txt /tmp/
RUN pip3 install -r /tmp/requirements.txt || true

# Copy all project files into Apache document root
WORKDIR /var/www/html
COPY . .

# Expose port 80 (Render maps to 10000 automatically)
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
