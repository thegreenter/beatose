# ![beatOSE](https://raw.githubusercontent.com/thegreenter/beatose/master/public/beat-ose.png) beatOSE
![PHP Composer](https://github.com/thegreenter/beatose/workflows/PHP%20Composer/badge.svg)

Una implementación de SUNAT Soap Server para recepcionar y validar comprobantes electrónicos.

**Alternativa a SUNAT BETA**
- Realizar pruebas con diferentes código de respuestas que el servicio de SUNAT no ofrece.
- Soporte de comprobantes extensos (_si envías un CPE con 300 items a SUNAT Beta, se cae_).
- Endpoint unificado para todos los comprobantes.
- Verificación de Credenciales.
- Rechazo de comprobantes.
- Consulta de CDR (_SUNAT no posee un servicio BETA para consultar CDR_).
- Almacenamiento de comprobantes enviados.

LIVE (Pruebas)

|      :rocket: |                                      |
|--------------:|--------------------------------------|
|URL            | https://beatose.herokuapp.com/       |    
|User           | `20123456789MODDATOS`                |
|Password       | `moddatos`                           |

## Build
Requerimientos
- `PHP 8.5+`
- PHP extensiones: `soap`, `pdo_sqlite`, `xsl`, `openssl`, `zlib`, `fileinfo`.

Instalar dependencias `composer install`.      
Crear base de datos de prueba en la ruta `./var/data.db`, con el siguiente comando:
```
php composer/DoctrineMigrations.php
```

Ejecutar pruebas
```bash
vendor/bin/phpunit
```

Ejecutar
```bash
php -S 127.0.0.1:8000 public/index.php
```

Los resúmenes (`sendSummary`) se procesan en segundo plano, como en SUNAT: `getStatus` responde `98` (en proceso)
hasta que el consumidor de la cola valide el XML y genere el CDR. En otra terminal:
```bash
php bin/console messenger:consume async -vv
```
Si prefieres procesarlos en la misma petición, usa `MESSENGER_TRANSPORT_DSN=sync://` en `.env.local`.

| `getStatus` → `statusCode` | Significado                                                 |
|----------------------------|-------------------------------------------------------------|
| `98`                       | En proceso, sin CDR                                         |
| `0`                        | Procesado; CDR aceptado (o con observaciones) en `content`  |
| `99`                       | Procesado con errores; CDR de rechazo en `content`          |
| `0100`–`1999`              | Excepción al procesar (p. ej. `0306` XML inválido), sin CDR |

### Docker + FrankenPHP (modo worker)

La imagen usa [FrankenPHP](https://frankenphp.dev) en **modo worker**: el kernel de Symfony arranca una sola vez
y queda en memoria atendiendo las peticiones, gracias a la integración nativa de `symfony/runtime`.

```bash
docker compose up --build
```

El servicio queda en http://localhost:8000/ol-ti-itcpe/billService?wsdl. Variables útiles (en el entorno o en un `.env` junto a `compose.yaml`):

| Variable                   | Descripción                                                                        |
|----------------------------|------------------------------------------------------------------------------------|
| `XSLT_URL`                 | URL del ZIP con los XSL de validación de SUNAT (se descarga al construir la imagen) |
| `SOAP_USER`, `SOAP_PASS`   | Credenciales SOAP                                                                  |
| `SIGN_PASS`                | Contraseña del certificado `keys/cert.pfx` con el que se firma el CDR              |
| `HTTP_PORT`                | Puerto publicado (por defecto `8000`)                                              |
| `SERVER_NAME`              | `:80` (HTTP) por defecto; un dominio activa HTTPS automático                       |
| `FRANKENPHP_WORKER_CONFIG` | Directivas extra del worker, p. ej. `num 4`                                        |
| `MESSENGER_TRANSPORT_DSN`  | Cola de resúmenes (por defecto `doctrine://default`)                               |

El servicio `worker` consume la cola de resúmenes. La base SQLite (`var/data`) y los XML recibidos y generados
(`var/cpe_files`) se guardan en volúmenes compartidos por ambos servicios.

Para desarrollo, con el código montado y los workers reiniciándose al editar archivos:

```bash
docker build --target frankenphp_dev -t beatose:dev .
docker run --rm -p 8000:80 -v "$PWD":/app beatose:dev
```

> En modo worker los servicios de Symfony viven entre peticiones: no guardes estado de una petición en
> propiedades de servicios compartidos (ver `AuthSoapService`, que se crea de nuevo en cada petición SOAP).

La especificación del servicio la encontrarás en http://127.0.0.1:8000/ol-ti-itcpe/billService?wsdl

Las **credenciales SOAP** pueden ser modificadas en el archivo `.env`:

```shell script
SOAP_USER=20000001XXXXX
SOAP_PASS=xxxx
```

## Métodos de Servicio

| SOAP Method    | Status            |
|----------------|-------------------|
|`sendBill`      |:white_check_mark: |
|`sendSummary`   |:white_check_mark: |
|`sendPack`      |:hourglass:        |
|`getStatus`     |:white_check_mark: |
|`getStatusCdr`  |:white_check_mark: |
