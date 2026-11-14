# Plan de trabajo — estado de entrega

Actualizado el **14/11/2026 (UTC)**. Rama [WAAAAAA](https://github.com/Samuelhvf18/Agencia_de_Turismo/tree/WAAAAAA). Esta revisión incluye desarrollo y pruebas; reemplaza el [plan del 13/11](docs/historico/PLAN-TRABAJO-2026-11-13.md). La [revisión inicial](docs/historico/PLAN-TRABAJO-2026-11-12.md) conserva las fuentes originales. Sus estimaciones no son horas pendientes ni trabajadas.

El proyecto dispone de una versión funcional de demostración, con pagos simulados. **No se acredita una operación de producción ni aprobación del PO/docente.** A continuación se separa lo implementado de las comprobaciones que necesitan servicios, datos, entorno o aceptación externa.

## Avance de esta entrega

- Notificaciones durables dentro de la transacción del pago; recuperación programada, bloqueo temporal de entrega, reintentos e identificador de evento único. SMTP se entrega al menos una vez: una caída después de la aceptación del servidor puede producir duplicados y exige conciliación.
- Shell offline que reabre sin red; manifiestos AES-GCM con clave local, caducidad de 12 horas y bloqueo entre pestañas. No almacena documentos ni condiciones médicas. Conflictos, cancelaciones, duplicados y cambio de cuenta comprobados.
- Verificación efectiva de correo y 2FA TOTP opcional con códigos de recuperación de un solo uso. Cambiar el correo vuelve a exigir verificación. Los secretos no se incluyen en auditoría.
- Archivo PNG validado antes de confirmar, emitir, reactivar o validar un boleto de alto riesgo. Lecturas personales de PDF auditadas; manifiesto y check-in revocados al quitar asignaciones.
- Agenda y revisión de propuestas transaccionales. Ensayos con procesos independientes para cupos, stock, asignaciones y pago contra cancelación. Un único juego activo de parámetros de colas, protegido también en PostgreSQL.
- Markov excluye el mes incompleto de la recalibración. Matriz persistida y conservación de integrantes en cohortes comprobadas. Colas conserva ventana, modelo y parámetros; EOQ usa una serie diaria con días en cero del mismo año de su fuente anual. Proyección móvil conserva meses sin ventas.
- Georreferenciación con validación de geometrías y avisos si fallan Leaflet o las teselas; puntos seguros frente a HTML. Búsqueda por nombre/categoría/fecha con cupos reales; la fecha ya participa en la búsqueda.
- Inglés ampliado a formularios, errores, JavaScript, pagos, correo y PDF; el idioma queda en la reserva para los trabajos de cola. Texto del prototipo aclara pagos y comprobantes simulados y no promete GPS en vivo.
- Respaldo de BD y adjuntos privados con manifiesto SHA-256, permisos privados y retención. Restauración de relaciones y firmas comprobada. Instalador local conserva `.env` y datos existentes.
- Imagen PHP con extensiones comprobables y estáticos Nginx del mismo build; se evita incluir firmas/archivos de QA y servir assets de un volumen antiguo. Consultar la validación concreta del build en el informe.

El rediseño administrativo y **Planificación inteligente** ya estaban implementados en `33abc90`; continúan disponibles. [Guía del panel](docs/REDISENO-ADMIN.md).

## Evidencia y reproducción

Consultar [informe de entrega](docs/ENTREGA-2026-11-14.md), [salida de la suite](docs/evidencias/suite-2026-11-14.txt), [comprobaciones de integración](docs/evidencias/aceptacion-2026-11-14.txt), [entorno QA](docs/CI-Y-PRUEBAS.md), [diccionario verificado](docs/DICCIONARIO-ACTUAL.md), [arquitectura](docs/ARQUITECTURA-ACTUAL.md) y [operación](docs/OPERACION.md).

Las pruebas usan datos artificiales y bases desechables. La medición 4G es una ejecución en Chromium; no equivale a una campaña sobre redes móviles reales. El ensayo de carga identifica sus condiciones y cualquier umbral incumplido.

## Estado de T01–T19 y trabajo residual

| ID | Implementación y verificación local | Qué falta para cerrar la aceptación |
|---|---|---|
| T01 · Entorno/CI | Dependencias, suite, build, [CI aprobado](https://github.com/Samuelhvf18/Agencia_de_Turismo/actions/runs/43554908984) para `8240366` y diccionario físico generado | Cotejo formal con D7/SQL externo; repetir instalación en el equipo de entrega |
| T02 · Pagos | Saldo protegido, webhook repetido/tardío tras cancelación y carrera pago–cancelación | Aceptar la simulación o aportar proveedor real, conciliación y devolución bancaria |
| T03 · Avisos | Outbox transaccional, recuperación, lease, backoff, fallos y SMTP capturado con dos PDF | Configurar correo real, revisar mensajes no entregables y acordar política de duplicados del proveedor |
| T04 · Permisos | Roles, titularidad, documentos/salud filtrados y revocación de guía/chofer; PDF auditado | Revisar la [matriz de accesos](docs/OPERACION.md) con responsables y repetirla sobre el despliegue |
| T05 · Auditoría | 39 tablas de negocio con trigger; SQL masivo, pivotes y lecturas sensibles | Privilegios de SQL de producción, conservación/volumen y aceptación de exclusiones técnicas |
| T06 · Historial | Consulta propia, separación de titulares, enlaces a estados y documentos | Recorrido de aceptación del titular en el entorno final; no se vinculan reservas invitadas automáticamente |
| T07 · URL públicas | Slugs únicos, nombres repetidos/acentos de tours, guía inactivo y campos públicos | Revisión editorial de categorías/guías y sus datos operativos |
| T08 · Markov | Meses completos, precedencia/absorción, matriz persistida y cohortes con datos de prueba | Contrastar con historial operativo; no inventar cambios históricos que nunca se registraron |
| T09 · Colas | Ventana común, escenario separado, estabilidad numérica, caso λ=5/μ=3,2/c=2/K=12 y contexto guardado | Aprobar equivalencia solicitud/pasajero/cupo y unidades económicas; ejecutar con datos reales |
| T10 · EOQ | Fuente anual y σ diaria con ceros/límites de año comprobados | Aprobar significado económico de usos frente a desgaste/reposición; no transformar alquileres en compras automáticas |
| T11 · n8n/SMTP/IA | Contratos autenticados, pruebas de fallos, SMTP de laboratorio y respaldo local | Instancia autorizada de n8n, SMTP/IA y tokens reales; activar flujos y acreditar ejecuciones/callbacks |
| T12 · Alcance | 2FA y correo verificado implementados; simulación/comprobante interno identificados | PO/docente acepta o cambia pagos/canal/comprobantes, unidades IO y políticas comerciales |
| T13 · Offline | Reapertura cifrada, clave incorrecta, caducidad, pestañas, cola, duplicados y cancelación comprobados | Prueba de jornada en teléfonos objetivo, cámara y pérdida de conectividad; validar límites de copia por 12 h |
| T14 · Proyección | Selector por tour, serie/modelo/fecha y meses en cero | Contrastar con registros operativos y explicar incertidumbre con poco historial |
| T15 · Documentación | Plan, matriz, diccionario, diagramas, operación y evidencias del repositorio | Conciliar documentos externos/Drive, informe académico y políticas; obtener aprobación por RF/RNF |
| T16 · Inglés | Interfaz principal, errores de reserva/pago, JS, correos y PDF; idioma persistido | Revisión lingüística y editorial integral, incluidas políticas legales y contenido de tours de la BD |
| T17 · Aceptación | Suite, carreras reales, fallos de mapas, 4G, firmas y móvil Chromium/Firefox | Safari/WebKit/Edge y teléfonos reales; recorrido con servicios de producción; carga representativa y degradación ≤50 % |
| T18 · Operación | Dump+ZIP+hashes restaurados, retención simulada y comandos de worker/scheduler | TLS/dominio, volúmenes y copias fuera del servidor; rendimiento bajo infraestructura final; disponibilidad mensual ≥99 % por medir |
| T19 · Panel e IO | Diseño unificado, explicaciones prácticas, escenarios y asignación manual confirmada | Sesión con administrador real y revisión con lector de pantalla/teclado/contraste |

## Georreferenciación

Está implementada: Leaflet/OpenStreetMap, puntos con latitud/longitud/altitud, recorrido GeoJSON, editor administrativo y perfil de elevación. La validación rechaza rutas vacías, tipos no admitidos, coordenadas fuera de rango y más de 5.000 puntos. Los fallos de CDN/teselas muestran alternativas. El perfil se degrada a información disponible si falta Chart.js o altitud.

La prueba 4G obtuvo **3.076 ms** para seis teselas, frente al objetivo de 4.000 ms, con caché vacía y perfil documentado. Queda validar coordenadas y recorridos con la agencia. **GPS en vivo no forma parte del alcance actual.**

## Para continuar en el entorno real

1. Configurar dominio/TLS, SMTP y n8n/IA mediante variables y gestores de credenciales; no subir secretos al repositorio. Los servicios externos no están acreditados en esta sesión.
2. Acordar pagos simulados o proveedor real, políticas comerciales, unidades de colas y modelo económico de equipo. 2FA ya puede activarse desde el perfil.
3. Desplegar la versión, aplicar migraciones con respaldo previo y ejecutar workers, scheduler y recuperación de avisos. Revisar duplicados activos de parámetros antes de la restricción nueva.
4. Repetir restauración, pruebas por rol, flujo turístico y mediciones con datos/carga de entrega; hacer la sesión de usabilidad.
5. Conciliar documentos externos y registrar aceptación. Medir disponibilidad durante el período real; no dar por anticipado el 99 % mensual.

**Criterio final:** cada requisito debe tener implementación, evidencia del entorno correspondiente y aceptación, o una excepción explícita aprobada. Los pendientes externos no se marcan completados por haber creado el código o pasado una prueba local.
