# Correcciones del flujo del turista

Se implementan las seis observaciones de «Cosas que hay que arreglar». La base es WAAAAAAA, commit 2ba0c2f. Las mejoras anteriores de menores, cierre de sesión y ajustes de IO están incluidas en esa base.

## Cambios

| Observación | Comportamiento implementado |
| --- | --- |
| Contacto de emergencia | Nombre con letras, tildes y espacios; selector de país; número nacional con dígitos. Se valida también el prefijo y el número internacional completo en el servidor. Una ficha opcional vacía sigue siendo opcional. |
| Tickets y correo | Se corrige la descarga individual con una colección de Support y se mantiene la descarga conjunta. Al crear la reserva se envía su código. Tras completar el pago, los tickets habilitados y el comprobante viajan por Spring Boot si está configurado. |
| Inicio de sesión y registro | Se conserva la última página pública válida de la sesión, incluidos sus parámetros. El registro conserva el destino hasta verificar el correo. Sin navegación previa, el turista vuelve a la landing. El administrador y personal conservan sus destinos por rol. |
| Para tu interés | Recomendaciones en la landing y en Mis reservas según categorías consultadas en la sesión y reservas propias. La matriz mensual vigente y el estado del cliente ajustan la estrategia. Solo se recomiendan tours publicados con foto y una salida futura con cupos. No se repiten tours con reservas futuras del mismo turista. |
| Panel y datos | Mis reservas concentra viajes, pagos y tickets, con acceso al catálogo y a Mi cuenta. Mi cuenta permite consultar nombre, correo, documento y teléfono y editar únicamente el teléfono del usuario conectado. |
| Recuperación | Interfaz con los colores de Pipocas y dos pasos: correo, después código y nueva contraseña. Permite reenviar el código, entrar con un código ya recibido y mostrar cada contraseña. Mantiene la expiración y la protección por intentos existentes. |

## Interpretación de Markov

La matriz describe cambios de estado de grupos de clientes; no identifica por sí sola qué tour le gusta a una persona. Las categorías vistas aportan 1 punto de afinidad; una reserva, 3, y una reserva completada, 5. Cada punto pesa 10 en la clasificación. Se favorecen experiencias nuevas; para clientes fidelizados, ese incentivo es mayor.

Si el estado es perdido o la probabilidad mensual vigente de pasar a perdido es al menos 0,30, se aplica una estrategia de recuperación: mensaje para retomar planes y mayor peso a opciones de menor dificultad. Esto es una regla del recomendador, no una predicción médica, un descuento ni una garantía de conversión. La matriz no modifica precios, cupos ni disponibilidad. Las matrices con probabilidades fuera de rango o filas que no suman 1 no activan el ajuste por riesgo. Si no existen preferencias, se muestran opciones disponibles para empezar a explorar.

Las visitas se limitan a los últimos 20 tours distintos de la sesión. Las reservas y los estados de Markov se consultan exclusivamente para el usuario autenticado. Los administradores no reciben recomendaciones personales.

## Activar Spring Boot

El microservicio existente está en `services/notificaciones`, con Java 21 y Spring Boot 3.5.16. Se amplía `POST /api/notificaciones` con `adjuntos`, una lista opcional de PDFs en base64. Se admiten hasta tres archivos, 5 MB por archivo y 10 MB en total. El servicio valida el nombre, el formato base64 y la cabecera PDF antes de enviarlos como adjuntos MIME.

En `.env` de Laravel, configurar:

```dotenv
NOTIFICACIONES_URL=http://127.0.0.1:8081
NOTIFICACIONES_TOKEN=SECRETO_ALEATORIO_COMPARTIDO_DE_AL_MENOS_32_CARACTERES
```

En el proceso Java, configurar el mismo `NOTIFICACIONES_TOKEN` y las variables SMTP `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`. Para Gmail se utiliza una contraseña de aplicación. Estas credenciales deben permanecer fuera de Git.

Con Java y Maven instalados:

```bash
mvn -B -f services/notificaciones/pom.xml verify
cd services/notificaciones
java -jar target/notificaciones-1.0.0.jar
```

En otra terminal, desde la raíz del proyecto:

```bash
php artisan config:clear
php artisan queue:work --tries=3 --backoff=30
```

El planificador existente también debe ejecutarse para recuperar notificaciones fallidas:

```bash
php artisan schedule:work
```

En Docker, usar `NOTIFICACIONES_URL=http://notificaciones:8081` y arrancar con `docker compose --profile correo-spring up --build -d`. El servicio es interno y conserva los recibos H2 en el volumen existente.

Si `NOTIFICACIONES_URL` está vacío, se conserva el transporte de correo de Laravel. No hace falta una nueva migración para estas correcciones. Actualizar el código, instalar las dependencias existentes y ejecutar `npm ci && npm run build`.

## Entrega y reintentos

Laravel registra el aviso con una clave estable, lo despacha después de confirmar la transacción y conserva el estado de entrega. Spring valida el token y deduplica por `Idempotency-Key` con recibos persistentes.

Los PDFs incluyen metadatos variables al regenerarse. Para que un timeout no produzca un mensaje distinto con la misma clave, Laravel prepara una sola vez el correo con adjuntos y lo conserva cifrado con `APP_KEY` en el registro de notificación. Los reintentos usan ese mismo contenido. No rotar `APP_KEY` sin conservar la posibilidad de descifrar mensajes pendientes. Los recibos de Spring guardan una huella HMAC, sin almacenar el código OTP ni el contenido del mensaje.

SMTP puede aceptar un mensaje justo antes de que una caída impida guardar el recibo: la entrega sigue siendo al menos una vez y en ese caso excepcional puede repetirse un correo. Los fallos quedan disponibles para revisión/reintento en Auditoría → Notificaciones.

## Validación

Se comprueban los formularios, los destinos después de autenticar/verificar, la autorización de descargas, PDFs individuales y conjuntos, el aviso inicial con código, el envío de PDFs a Spring, la reutilización del payload en reintentos, el aislamiento de datos de cuenta y el uso de la matriz en recomendaciones. La integración SMTP se prueba con un remitente simulado; la recepción en una bandeja real requiere configurar las credenciales de la instalación.

Referencia del transporte: https://docs.spring.io/spring-boot/3.5/reference/io/email.html
