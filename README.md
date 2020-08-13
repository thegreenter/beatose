# ![beatOSE](https://raw.githubusercontent.com/thegreenter/beatose/master/public/beat-ose.png) beatOSE
[![Heroku](https://heroku-badge.herokuapp.com/?app=beatose)](https://beatose.herokuapp.com/)

Una implementación de SUNAT Soap Server para recepcionar comprobantes electrónicos.

Propósito
- **Mock Server**, para realizar pruebas con diferentes respuestas que 
actualmente el servidor BETA de SUNAT no ofrece.

**Live**

https://beatose.herokuapp.com/ol-ti-itcpe/billService?wsdl

## Build
Ejecutar
```bash
php -S 127.0.0.1:8000 -t public
```

La especificación del servicio la encontrarás en http://127.0.0.1:8000/ol-ti-itcpe/billService?wsdl

## Métodos de Servicio
- `sendBill`
- `sendSummary`
- `sendPack`
- `getStatus`
- `getStatusCdr`

**Credenciales**

Puedes cambiar las credenciales en el archivo `.env`, por defecto son:
- Usuario: `20123456789MODDATOS`
- Contraseña: `moddatos`
