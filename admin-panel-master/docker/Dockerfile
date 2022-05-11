FROM alpine:3.14

RUN apk --no-cache add \
  ca-certificates \
  curl \
  bash \
  nginx \
  jpegoptim \
  optipng \
  pngquant \
  gifsicle \
  libwebp \
  php7 \
  php7-ctype \
  php7-curl \
  php7-dom \
  php7-exif \
  php7-fileinfo \
  php7-fpm \
  php7-gd \
  php7-pecl-imagick \
  php7-iconv \
  php7-intl \
  php7-sodium \
  php7-json \
  php7-mbstring \
  php7-mysqli \
  php7-opcache \
  php7-openssl \
  php7-pdo \
  php7-pdo_mysql \
  php7-pecl-redis \
  php7-phar \
  php7-session \
  php7-simplexml \
  php7-tokenizer \
  php7-xml \
  php7-xmlreader \
  php7-zip \
  php7-zlib \
  supervisor \
  && rm /etc/nginx/http.d/default.conf

# Copy configs
COPY docker/conf/entrypoint.sh /usr/local/bin/
COPY docker/conf/nginx.conf /etc/nginx/nginx.conf
COPY docker/conf/fpm-pool.conf /etc/php7/php-fpm.d/www.conf
COPY docker/conf/php.ini /etc/php7/conf.d/custom.ini
COPY docker/conf/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN mkdir -p /app/public

# Set permissions to run as nobody user
RUN chown -R nobody.nobody /app && \
  chown -R nobody.nobody /run && \
  chown -R nobody.nobody /var/lib/nginx && \
  chown -R nobody.nobody /var/log/nginx
USER nobody

# Add application code
WORKDIR /app
COPY --chown=nobody . .
COPY --chown=nobody vendor/ vendor/

EXPOSE 8080

ENTRYPOINT [ "/usr/local/bin/entrypoint.sh" ]
