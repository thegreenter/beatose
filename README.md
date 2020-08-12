# BeatOSE

Una implementación de SUNAT Soap Server para recepcionar comprobantes electrónicos.

Propósito
- **Mock Server**, para realizar pruebas con diferentes respuestas que 
actualment el servidor BETA de SUNAT no ofrece.

Ejecutar
```
php -S 127.0.0.1:8000 -t public
```

La especificación del servicio la encontrarás en http://127.0.0.1:8000/billService?wsdl