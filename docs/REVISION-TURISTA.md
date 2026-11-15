# Revisión del rol de turista

Implementación de las 14 observaciones de «Revisión rol de Turista.pdf».

Base: `WAAAAAA`, commit `b123d4d0529d961b2f38cd03282f2f98ba7b28f8`. La rama solicitada `WAAAAAAA` no existía: esta entrega continúa esa base en `WAAAAAAA`.

## Cambios por observación

| N.º | Cambio | Activación o límite |
| --- | --- | --- |
| 1 | Consentimiento persistente por categoría; rechazar bloquea scripts opcionales; configurar permite decisiones separadas; retirar permiso recarga y desactiva recursos. Los mapas se cargan después de consentir contenido externo. | Preferencia local por 180 días. No hay proveedor de analítica instalado. Cookies de sesión y seguridad siguen siendo esenciales. |
| 2 | Footer legal abre los cuatro modales existentes. | Términos, privacidad, cookies y reembolsos. |
| 3 | Cada categoría del footer abre el catálogo filtrado y desplaza a los tours. | Categorías obtenidas de la base de datos. |
| 4 | Redes oficiales configurables mediante variables `SOCIAL_*_URL`; enlaces HTTPS con destino y apertura segura. | Por decisión del usuario, configurar después. Las redes sin URL permanecen ocultas. WhatsApp: `https://wa.me/591NUMERO`. |
| 5 | Formulario funcional solo para invitados, consentimiento obligatorio, confirmación por correo y baja mediante enlaces firmados. Servicio Spring Boot conectado para correo y alertas. | No se envían promociones a correos sin confirmar o dados de baja. No se creó una campaña ni se enviaron correos reales durante las pruebas. |
| 6 | Corazón de favoritos eliminado del catálogo activo. | No hay una promesa de disponibilidad permanente del tour. |
| 7 | Nombres y apellidos: letras Unicode y espacios; correo normalizado a minúsculas y único; mostrar contraseña y lista de requisitos visible. | Contraseña: mínimo 8 caracteres, una letra, un número y confirmación. Validación también en servidor. |
| 8 | Recuperación con código OTP de seis dígitos; contraseñas y sesiones anteriores revocadas al restablecer. | SMTP necesita credenciales de `pipocasprogra@gmail.com` en el entorno. |
| 9 | Google OAuth con Socialite; claves de acceso con Laravel Passkeys, alta y eliminación desde el perfil. | Google necesita cliente OAuth. Claves de acceso requieren HTTPS o localhost y un autenticador compatible. |
| 10 | Verificación de cuenta mediante OTP en la misma página, interfaz coherente con Pipocas y reenvío controlado. | Leer el código desde cualquier dispositivo evita abrir `localhost` en el teléfono. |
| 11 | Identidad del contacto autenticado inmodificable; el servidor toma nombre y correo de la cuenta aunque se falsifique el formulario. Selector de prefijos internacionales y número nacional sin letras. | 206 prefijos internacionales; formato internacional de 7 a 15 dígitos. |
| 12 | Nacimiento exacto en lugar de edad manual; documentos y campos con reglas específicas; fechas futuras o inválidas rechazadas. | Edad calculada en servidor para la fecha de salida. Los históricos no reciben una fecha inventada. |
| 13 | Política de menores, adulto acompañante, declaración del representante y verificación documental por operaciones; bloqueo en reserva, emisión de boleto y check-in. | Detalle abajo. |
| 14 | Migraciones para la base actual y reparación compatible de `reservas.idioma`; comprobación del flujo completo de confirmación. | **Ejecutar `php artisan migrate --force`**: descargar código no actualiza la base local. Ya había una migración de idioma en la base de la rama; el error del PDF indica una base pendiente de migrar. |

## Observación 13: menores y actividades de riesgo

### Política de la agencia

La prohibición de menores en alto riesgo es una **política conservadora del sistema**, no una afirmación de que toda actividad turística esté legalmente prohibida a menores de 18 años. Una autorización parental no elimina esa política.

Un tour es de alto riesgo si operaciones lo marca expresamente, su dificultad es difícil/experto o su altitud máxima es de al menos 4500 m. La clasificación existente se conserva y se añade la marca explícita para riesgos propios de la actividad. Operaciones puede fijar una edad mínima mayor y deshabilitar menores también en tours de menor riesgo. Desmarcar la casilla de alto riesgo no elimina los criterios automáticos.

La edad corresponde a la **fecha de salida**, no al momento de llenar el formulario. Quien cumple 18 años antes de salir puede participar; quien cumple 18 al día siguiente sigue siendo menor para esa salida. La edad enviada manualmente por el navegador se ignora.

Para menores admitidos en otros tours se requiere:

1. Fecha de nacimiento y documento del menor.
2. Un acompañante distinto, de al menos 18 años, incluido en la misma reserva.
3. Nombre y documento del representante, modalidad de acompañamiento y declaración expresa.
4. Revisión por administración/operaciones de documentos originales, vínculo, permisos aplicables y acompañante, desde la pantalla de la reserva.

La declaración web no es un permiso de viaje emitido por la autoridad ni una evaluación médica. Para viajes nacionales interdepartamentales la documentación depende de si acompañan ambos padres, uno, tutor o un tercero. La pantalla explica estas diferencias. Los originales se verifican de forma presencial: no se publican ni se solicitan fotos de documentos en el formulario.

### Controles del sistema

- `SeguridadParticipantes` concentra cálculo de edad y política; se ejecuta en el paso de participantes y nuevamente al confirmar dentro de la transacción.
- Se conserva fecha de nacimiento, acompañante y autorización con versión y momento de declaración. Los datos internos de aprobación proceden exclusivamente del servidor.
- El pago puede completarse, pero **el boleto del menor queda retenido** hasta verificar la documentación. Los boletos de adultos elegibles pueden emitirse normalmente.
- La verificación documental almacena personal responsable y una huella de la salida, reglas del tour, menor y acompañante. Cambiar fecha/hora, reglas o identidad obliga a verificar otra vez.
- El menor embarca solo después de registrar el boleto del adulto acompañante.
- El check-in vuelve a comprobar la elegibilidad incluso si el boleto se emitió antes de un cambio de riesgo.
- Menores y alto riesgo requieren conexión para check-in. El modo sin conexión no los marca como embarcados; las restricciones del servidor se aplican también al sincronizar.
- Editar participantes descarta equipo y fichas médicas previas para evitar asignar una firma a otra persona por el índice de la lista.
- Las fichas médicas firmadas continúan siendo obligatorias para adultos en tours de alto riesgo.
- La edad declarada de registros históricos se conserva; el sistema no inventa nacimientos a partir de una edad entera.

### Fuentes oficiales consultadas

- [Gobierno Autónomo Municipal de La Paz: requisitos de viaje con menores](https://lapaz.bo/defensoria-terminal/).
- [Tribunal Supremo de Justicia: compendio normativo de niñez y adolescencia](https://tsj.bo/wp-content/uploads/2023/08/compendio-normativo-ninyas-ninyos-y-adolescentes.pdf), viajes nacionales y verificación documental, artículos 58 y 59 del reglamento incluido.
- [Ministerio de Gobierno: Decreto Supremo 2995](https://web.mingobierno.gob.bo/wp-content/uploads/2025/09/DS-2995.pdf), seguridad en turismo de riesgo y aventura.

Las reglas concretas del operador, seguros y actividad deben revisarse antes de ofrecer tours reales; el sistema permite endurecer las edades y bloquear menores.

## Instalar esta actualización

En la instalación que usa la rama:

```bash
git fetch origin
git switch WAAAAAAA
git pull --ff-only origin WAAAAAAA
composer install
npm ci
php artisan migrate --force
php artisan optimize:clear
npm run build
php artisan queue:restart
```

No usar `migrate:fresh` en una base con datos que se quieren conservar. La migración agrega columnas y tablas; no borra reservas ni inventa fechas de nacimiento.

### Gmail

Configurar fuera del repositorio, en `.env` o en el gestor de secretos del despliegue:

```dotenv
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=pipocasprogra@gmail.com
MAIL_PASSWORD=CONTRASENA_DE_APLICACION
MAIL_FROM_ADDRESS=pipocasprogra@gmail.com
MAIL_FROM_NAME=Pipocas
```

Activar verificación en dos pasos y crear una [contraseña de aplicación de Google](https://support.google.com/mail/answer/185833). No introducir la contraseña habitual de Gmail en el código ni subir `.env` a Git. El transporte `log` de desarrollo registra correos en disco y no demuestra entrega real.

Los OTP se generan con aleatoriedad criptográfica, se guardan con hash, vencen en 10 minutos y permiten cinco intentos. Reenviar exige esperar un minuto, rota el código anterior y hay limitación de solicitudes por IP. El código se consume de forma transaccional con la operación protegida. Recuperar una contraseña no elimina el segundo factor existente.

### Google y claves de acceso

Crear un cliente OAuth de tipo aplicación web en Google Cloud. Registrar la URI de retorno exacta y configurar:

```dotenv
APP_URL=https://DOMINIO_REAL
GOOGLE_CLIENT_ID=ID_DEL_CLIENTE
GOOGLE_CLIENT_SECRET=SECRETO_DEL_CLIENTE
GOOGLE_REDIRECT_URI=https://DOMINIO_REAL/auth/google/callback
PASSKEYS_RELYING_PARTY_ID=DOMINIO_REAL
PASSKEYS_USER_HANDLE_SECRET=SECRETO_ESTABLE
```

En desarrollo, Google permite el callback HTTP de localhost con el puerto exacto. Para acceder desde el celular, `APP_URL` debe apuntar a un servidor accesible desde ese dispositivo; `localhost` en el teléfono apunta al propio teléfono.

- Google solo admite correos que el proveedor reporta como verificados; Socialite comprueba `state` con la sesión.
- Un correo coincidente con una cuenta local **no la vincula automáticamente**. El titular debe iniciar sesión y vincular Google desde su perfil con confirmación de contraseña.
- Se bloquean cuentas desactivadas y se conserva la segunda fase TOTP de cuentas Google que la tengan activa.
- Las claves de acceso usan las ceremonias y verificaciones criptográficas de Laravel Passkeys; no una comparación casera de códigos. Alta/eliminación requieren autenticación y confirmación de contraseña. El login exige cuenta activa y correo verificado.
- Una clave pertenece al dominio donde se creó. Cambiar dominio o perder el autenticador requiere un método de acceso alternativo. Mantener estable el secreto de identificadores si se rota `APP_KEY`.
- Las pruebas automatizadas verifican rutas, autenticación y rechazo de credenciales falsas. La experiencia con Google real, Windows Hello y un celular real requiere las cuentas y dispositivos de despliegue.

## Servicio Spring Boot

Código en `services/notificaciones`. Java 21, Spring Boot 3.5.16, SMTP y recibos persistentes en H2. No almacena destinatarios, contenido de correos ni OTP: conserva clave de idempotencia, HMAC del mensaje y fecha de envío.

```bash
mvn -B -f services/notificaciones/pom.xml verify
# Desde services/notificaciones, con variables de entorno configuradas:
java -jar target/notificaciones-1.0.0.jar
```

Variables: `NOTIFICACIONES_TOKEN` (secreto aleatorio de al menos 32 caracteres), `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`; opcionalmente `NOTIFICACIONES_DB_URL` y `NOTIFICACIONES_DB_PASSWORD`. Por defecto escucha solo en 127.0.0.1:8081.

En Laravel:

```dotenv
NOTIFICACIONES_URL=http://127.0.0.1:8081
NOTIFICACIONES_TOKEN=MISMO_SECRETO_DEL_SERVICIO
```

Con Docker: establecer `NOTIFICACIONES_URL=http://notificaciones:8081`, configurar el secreto y SMTP, y arrancar `docker compose --profile correo-spring up --build -d`. El servicio no publica un puerto hacia el exterior; sus recibos permanecen en un volumen. No reutilizar el token de desarrollo en producción ni rotarlo sin considerar los HMAC de recibos existentes.

El servicio exige `Authorization: Bearer …` e `Idempotency-Key` y valida destinatario/asunto. Laravel lo usa para OTP, confirmación de suscripciones y avisos de texto de `NotificacionJob`. Los correos con boletos PDF conservan su envío SMTP de Laravel. Sin URL del servicio se conserva el transporte de Laravel; no hay dependencia obligatoria para reservar.

El servicio confirma solo después de que SMTP acepta el mensaje; un fallo devuelve 503 y permite reintentar. Un reintento ya completado no duplica el envío. Como con cualquier envío SMTP, una caída entre aceptación del servidor y confirmación de la transacción puede producir un duplicado: no se promete entrega exactamente una vez ni llegada a la bandeja del destinatario. Los eventos del sistema conservan el mecanismo de recuperación existente.

Las suscripciones se guardan en `suscripciones_novedades`. Para campañas futuras seleccionar exclusivamente `confirmada_en IS NOT NULL AND baja_en IS NULL` y añadir un enlace firmado a `novedades.baja`. Confirmación y baja requieren un POST explícito para evitar cambios por escáneres automáticos de enlaces. No se añadió un envío masivo automático.

## Validación

- Suite PHP sobre PostgreSQL real, con pruebas nuevas para menores, contacto autenticado, campos, OTP, suscripciones, Google y rutas de claves de acceso.
- Pruebas de DOM para consentimiento y ejecución efectiva de recursos opcionales.
- Pruebas Spring MVC con SMTP simulado: autenticación del servicio, idempotencia, conflicto de contenido, error/reintento de SMTP y validación de correo.
- Build Vite y compilación de vistas Blade.
- El navegador Chromium no pudo iniciarse en este entorno por restricción de sockets del sistema; no se afirma una revisión visual completa ni una prueba con autenticadores físicos.
