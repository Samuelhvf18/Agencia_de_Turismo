# Operaciones y mejoras generales del sistema

Se aplican las dos observaciones del documento del rol de Operaciones y las recomendaciones del docente sobre validaciones, Livewire, listas desplegables, chatbot e imágenes. Esta entrega incluye las correcciones anteriores del turista y conserva el microservicio Spring Boot de correo y tickets.

## Permisos de Operaciones

| Acción | Administrador | Operaciones |
| --- | --- | --- |
| Consultar y filtrar guías y choferes | Sí | Sí |
| Registrar guías o choferes | Sí | No |
| Editar personal existente y revisar su disponibilidad | Sí | Sí |
| Registrar un vehículo | Sí | No |
| Modificar placa, marca, modelo, tipo, combustible o estado de un vehículo | Sí | No |
| Modificar asientos | Sí | Sí, respetando el cupo de las salidas futuras |

Los formularios de alta solo se muestran al administrador. El servidor comprueba los permisos aunque se intente enviar una petición manual. Para Operaciones, los atributos restringidos del vehículo se rechazan con una validación explícita. La actualización de asientos comparte el bloqueo de agenda del asignador para evitar decisiones simultáneas incompatibles.

## Recomendaciones implementadas

| Recomendación | Aplicación |
| --- | --- |
| Nombres sin números | Nombres personales, contactos, participantes, representantes, nombres del catálogo, categorías, inventario y puntos de ruta aceptan letras Unicode, tildes y espacios. Los documentos, placas y modelos conservan su formato propio. |
| Teléfonos solo numéricos | Los campos nacionales solo aceptan dígitos. El país y su prefijo se eligen por separado. Personal y registro reutilizan el mismo campo de teléfono; reservas, emergencias y cuenta conservan sus selectores. El servidor rechaza letras y signos en el número nacional. |
| Contraseñas en ambas capas | Registro, restablecimiento, cambio de contraseña y altas de personal comparten un mínimo de 8 caracteres, una letra y un número, con máximo de 128. El navegador informa el requisito que falta y verifica la confirmación cuando existe. El servidor valida de nuevo. El inicio de sesión permite las claves existentes. |
| Livewire | Catálogo con búsqueda y filtros, listado de personal con paginación, flota, galería y asistente turístico tienen componentes propios. El perfil utiliza los componentes de Jetstream. Las acciones administrativas verifican permisos también en las peticiones de Livewire. |
| Más opciones | Nacionalidades: 249 países ISO 3166 con nombres en español. Personal: 26 idiomas, incluidos Quechua, Aymara y Guaraní. La migración añade opciones sin cambiar los identificadores existentes. |
| Chatbot con fotos y botones | Reconoce nombres de destinos además de peticiones genéricas. Muestra imágenes, precio, duración, próxima salida y botón al tour. Conserva IDs en el historial y reconstruye las tarjetas desde el catálogo publicado. La IA no puede proporcionar HTML, imágenes ni enlaces arbitrarios para esas tarjetas. |
| Gestión de imágenes | Galería reactiva para cargar, ordenar, describir y eliminar fotos. La primera es portada. El catálogo, las categorías, el detalle, las recomendaciones y el chat muestran sus imágenes. Se habilita la foto de perfil. |
| Validación de imágenes | JPG, PNG y WebP; contenido real y MIME; máximo 4 MB por foto de tour y 2 MB de perfil; dimensiones entre 100 × 100 y 6000 × 6000. Máximo 10 imágenes por carga y 30 por tour. Se rechaza SVG y archivos de texto disfrazados. El navegador comprueba formato, peso y cantidad; el servidor comprueba también dimensiones y contenido. |
| Correos | Validación RFC, límite de longitud, normalización de espacios externos y mayúsculas, y comprobación de duplicados sin distinguir mayúsculas. Cambiar el correo de un usuario o empleado invalida su verificación anterior. |

Los dos nombres de demostración que contenían «&» pasan a «Valle de la Luna y Teleféricos» y «Tiwanaku y Puma Punku». Sus rutas y códigos se conservan.

## Mejoras adicionales

Se sustituyen las seis fotos de demostración por fotografías de los destinos correspondientes, servidas localmente. Las fuentes y licencias están en `public/images/tours/creditos.json` y en la página pública `/creditos-imagenes`.

Un tour publicado conserva al menos una portada. Para eliminar su única imagen hay que añadir otra o desactivar el tour. La galería comprueba la pertenencia de cada foto, evita IDs repetidos al reordenar y limpia los archivos que se hubieran guardado si la transacción falla. El perfil almacena la nueva foto antes de eliminar la anterior. Las imágenes ausentes cuentan con una imagen de respaldo local y se bloquean rutas que intenten salir de los directorios permitidos.

Los íconos se sirven desde la dependencia instalada de Font Awesome, sin depender de su CDN. Las horas del historial del chatbot se muestran en `America/La_Paz`, configurable con `TURISMO_TIMEZONE`. El chatbot pide un presupuesto en bolivianos cuando recibe dólares, para evitar inventar un tipo de cambio. Su historial se limita a los últimos 100 mensajes y resuelve las tarjetas en una consulta compartida.

## Actualizar la instalación

Desde la rama con esta entrega:

```bash
composer install
npm ci
php artisan migrate
php artisan storage:link
php artisan optimize:clear
npm run build
```

La migración agrega `mensajes_chatbot.tours_ids`, amplía países e idiomas y actualiza los dos nombres de demostración. No requiere borrar ni recrear la base de datos. Si `storage:link` informa que el enlace ya existe, conservarlo.

El microservicio mínimo ya está implementado en `services/notificaciones`: Spring Boot procesa correo y adjuntos de tickets. Su configuración y arranque se documentan en `OBSERVACIONES-TURISTA-NOVIEMBRE.md`. Para entregas reales deben estar activos el servicio, el worker de colas y SMTP con sus credenciales. Las pruebas de esta entrega no envían correos a destinatarios reales.

## Comprobaciones

La suite cubre permisos por rol, intentos de modificar campos restringidos, cupos incompatibles, nombres, números, correos duplicados, contraseñas, filtros de Livewire, cargas inválidas, orden y pertenencia de fotografías, conservación de la portada, foto de perfil y tarjetas seguras del chatbot. Se revisan además los formularios y las imágenes en Chromium de escritorio y móvil.

Referencias técnicas: https://livewire.laravel.com/docs/3.x/security y https://livewire.laravel.com/docs/3.x/uploads
