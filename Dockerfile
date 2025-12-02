FROM php:8.2-apache

# Встановлюємо розширення
RUN docker-php-ext-install pdo pdo_mysql

# Вмикаємо mod_rewrite
RUN a2enmod rewrite

# Копіюємо файли в контейнер
COPY . /var/www/html/

# --- МАГІЯ: Налаштування Apache ---
# 1. Змінюємо DocumentRoot, щоб він точно вказував на html
ENV APACHE_DOCUMENT_ROOT /var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 2. Дозволяємо .htaccess
RUN echo '<Directory /var/www/html/>' >> /etc/apache2/apache2.conf
RUN echo '    AllowOverride All' >> /etc/apache2/apache2.conf
RUN echo '    Require all granted' >> /etc/apache2/apache2.conf
RUN echo '</Directory>' >> /etc/apache2/apache2.conf

# 3. Вказуємо, що index.php - головний
RUN echo "DirectoryIndex index.php index.html" >> /etc/apache2/apache2.conf

# 4. Видаємо права (це вирішує Access Error)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html