# ![beatOSE](https://raw.githubusercontent.com/thegreenter/beatose/master/public/beat-ose.png) beatOSE
[![Heroku](https://heroku-badge.herokuapp.com/?app=beatose)](https://beatose.herokuapp.com/)

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
- `PHP +7.4`
- PHP extensiones: `soap`, `sqlite`.

Instalar dependencias `composer install`.      
Crear base de datos de prueba en la ruta `./var/data.db`, con el siguiente comando:
```
php composer/DoctrineMigrations.php
```

Ejecutar
```bash
php -S 127.0.0.1:8000 -t public
```

La especificación del servicio la encontrarás en http://127.0.0.1:8000/ol-ti-itcpe/billService?wsdl

## Métodos de Servicio

- `sendBill` :white_check_mark:
- `sendSummary` :white_check_mark:
- `sendPack` :hourglass:
- `getStatus` :white_check_mark:
- `getStatusCdr` :white_check_mark:

Puedes cambiar las credenciales en el archivo `.env`
