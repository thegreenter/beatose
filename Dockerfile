# syntax=docker/dockerfile:1

# Imagen base: FrankenPHP (Caddy + PHP 8.5 embebido)
FROM dunglas/frankenphp:1-php8.5 AS frankenphp_base

WORKDIR /app

# Extensiones requeridas en composer.json (dom, openssl, pdo_sqlite, zlib,
# fileinfo, iconv y ctype ya vienen en la imagen); soap la exige greenter/ws
# y zip la usa Composer.
RUN set -eux; \
	install-php-extensions \
		opcache \
		soap \
		xsl \
		zip \
	;

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PHP_INI_SCAN_DIR=":$PHP_INI_DIR/app.conf.d"

COPY --link frankenphp/conf.d/10-app.ini $PHP_INI_DIR/app.conf.d/
COPY --link --chmod=755 frankenphp/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
COPY --link frankenphp/Caddyfile /etc/frankenphp/Caddyfile
COPY --from=composer/composer:2-bin --link /composer /usr/local/bin/composer

ENTRYPOINT ["docker-entrypoint"]

CMD [ "frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile" ]

# Imagen de desarrollo: monta el código como volumen y recarga workers al editar.
FROM frankenphp_base AS frankenphp_dev

ENV APP_ENV=dev
ENV FRANKENPHP_WORKER_CONFIG="import dev_watch"

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Imagen de producción
FROM frankenphp_base AS frankenphp_prod

ENV APP_ENV=prod
ENV DATABASE_URL="sqlite:///%kernel.project_dir%/var/data/data.db"

# URL de un ZIP con los XSL de validación de SUNAT (ver composer/XsltDownloader.php).
ARG XSLT_URL=""

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY --link frankenphp/conf.d/20-app.prod.ini $PHP_INI_DIR/app.conf.d/

# Capa de dependencias (se cachea mientras composer.* no cambie)
COPY --link composer.* symfony.* ./
RUN set -eux; \
	composer install --no-cache --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress

COPY --link . ./

RUN set -eux; \
	mkdir -p var/cache var/log var/data var/cpe_files; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer dump-env prod; \
	XSLT_URL="$XSLT_URL" composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync;
