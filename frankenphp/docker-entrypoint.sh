#!/bin/sh
set -e

if [ "$1" = 'frankenphp' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
	if [ ! -f vendor/autoload_runtime.php ]; then
		composer install --prefer-dist --no-progress --no-interaction
	fi

	mkdir -p var/data var/cpe_files

	if [ "$1" = 'frankenphp' ]; then
		# SQLite crea el archivo al conectar; aplica migraciones y crea la tabla de la cola.
		php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --all-or-nothing
		php bin/console messenger:setup-transports --no-interaction
	else
		case "$*" in
			*messenger:consume*)
				# El consumidor espera a que el servidor haya preparado la base de datos.
				attempts=60
				until php bin/console doctrine:migrations:up-to-date --quiet 2>/dev/null; do
					attempts=$((attempts - 1))
					if [ "$attempts" -le 0 ]; then
						echo 'La base de datos no está lista (¿arrancó el servicio php?).' >&2
						exit 1
					fi
					sleep 1
				done
				;;
		esac
	fi
fi

exec docker-php-entrypoint "$@"
