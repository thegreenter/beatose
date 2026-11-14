#!/bin/sh
set -e

if [ "$1" = 'frankenphp' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
	if [ ! -f vendor/autoload_runtime.php ]; then
		composer install --prefer-dist --no-progress --no-interaction
	fi

	# SQLite crea el archivo al conectar; solo aplica las migraciones pendientes.
	mkdir -p var/data var/cpe_files
	php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --all-or-nothing
fi

exec docker-php-entrypoint "$@"
