# ![beatOSE](https://raw.githubusercontent.com/thegreenter/beatose/master/public/beat-ose.png) beatOSE
[![Heroku](https://heroku-badge.herokuapp.com/?app=beatose)](https://beatose.herokuapp.com/)

Una implementación de SUNAT Soap Server para recepcionar comprobantes electrónicos.

Propósito
- **Mock Server**, para realizar pruebas con diferentes respuestas que 
actualmente el servicio de SUNAT no ofrece.
- Alternativa al servicio BETA de SUNAT.

LIVE (Pruebas)

|      :rocket: |                                      |
|--------------:|--------------------------------------|
|URL            | https://beatose.herokuapp.com/       |    


## Build
Ejecutar (require `PHP +7.4`)
```bash
php -S 127.0.0.1:8000 -t public
```

La especificación del servicio la encontrarás en http://127.0.0.1:8000/ol-ti-itcpe/billService?wsdl

## Métodos de Servicio
- `sendBill` :white_check_mark:
- `sendSummary` :white_check_mark:
- `sendPack` :hourglass:
- `getStatus` :hourglass:
- `getStatusCdr` :hourglass:

**Credenciales**

Puedes cambiar las credenciales en el archivo `.env`, por defecto son:
- Usuario: `20123456789MODDATOS`
- Contraseña: `moddatos`
