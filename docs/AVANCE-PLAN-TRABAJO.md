# Avance y pendientes del plan de trabajo

Actualización: **13 de noviembre de 2026, Bolivia**. Código de referencia: **33abc90**, posterior a cb12158. El [plan principal actualizado](../Plan_Trabajo_Agencia_Turismo.md) es la fuente vigente de subtareas, responsables y criterios de cierre. La [revisión original](historico/PLAN-TRABAJO-2026-11-12.md) es histórica.

## Avance comprobado

| Área | Implementación y evidencia | Pendiente para cerrar |
|---|---|---|
| Entorno (T01) | PHP 8.4/PostgreSQL 17, dependencias bloqueadas, suite/build y workflow CI | Resultado CI del commit final y contraste formal de esquema |
| Pagos (T02) | Un intento activo, revalidación de saldo/estado, bloqueos coherentes y carrera real entre dos procesos de QA | Carrera pago–cancelación y conciliación si se exige proveedor real |
| Notificaciones (T03) | Webhook repetido no repite el aviso en los casos probados; trabajo único y registro de boletos reutilizado | Evento durable entre confirmación y despacho; fallos/reintentos SMTP/worker |
| Acceso y auditoría (T04/T05) | Reportes solo admin, filtro del chofer, triggers que cubren cambios masivos; prueba de fallo cerrado | Inventario completo de accesos y atribución, exclusiones acordadas, privilegios y volumen |
| Historial y páginas públicas (T06/T07) | Historial paginado propio; rutas por slug de categorías y guías; pruebas negativas | Recorrido visual, privacidad integral y casos específicos de slugs |
| IO (T08/T09/T10) | Reglas de Markov corregidas, colas con 30 días y simulación separada, EOQ desde vista anual | Cohortes/matriz persistida con datos reales, unidades de colas, significado económico de D y desviación diaria |
| Servicios y alcance (T11/T12) | Respaldo local y pruebas con HTTP/Mail simulados; comportamiento actual documentado | n8n/SMTP/IA reales y decisiones PO/docente sobre pagos/comprobantes/2FA/verificación de correo |
| Offline e idiomas (T13/T16) | Aislamiento y minimización locales; traducción pública principal y conservación de idioma probada | Reapertura sin red desde cero, conflictos/cambio de cuenta, textos/errores secundarios |
| Analítica (T14) | Selector/desglose de proyección por tour, versión y fecha | Contraste con tabla, meses sin ventas y estado sin historial |
| Documentos (T15) | Matriz del repositorio y plan actualizados; fuentes originales conservadas | Trazabilidad completa, UML/PDF/Drive/diccionario y aprobación externa |
| QA/operación (T17/T18) | Suite, revisión de interfaz y restauración local pequeña | Carga/p95, mapas 4G/fallos, navegadores, concurrencia restante, archivos y operación real |
| Administración comprensible (T19) | Diseño unificado y Planificación inteligente con comparaciones, explicaciones y confirmación de asignaciones | Validación con administrador real, lector de pantalla y otros navegadores |

## Resultados disponibles

- [Suite de preparación de 33abc90](evidencias/suite-33abc90.txt): **170 aprobadas, 4 omitidas, 0 fallidas; 872 aserciones**. Tres omisiones son de 2FA desactivado y una del caso de registro deshabilitado, que no aplica con registro habilitado. Esto no acredita 2FA.
- `npm run build`: correcto, Vite 6.4.3, 58 módulos. [Entorno reproducible](CI-Y-PRUEBAS.md).
- [Pagos concurrentes](evidencias/pagos-concurrencia-cb12158.txt): un intento creado y otro rechazado; dos callbacks heredados dejan **Bs 100 de Bs 100** contabilizados, con conexiones independientes.
- [Interfaz Chromium](evidencias/interfaz-33abc90.txt): 16 pantallas a 360 px sin desbordamiento del documento; revisión de planificación a 768/1440 px, controles y confirmación de asignación en QA, sin errores JS en los recorridos. [Detalle del rediseño](REDISENO-ADMIN.md).
- Restauración PostgreSQL 17 en otra base: 6 tours, 223 auditorías, 18 migraciones; sin errores. La muestra no tenía reservas ni acredita recuperación de archivos/volumen operativo.
- CI remoto continúa **por acreditar**: el workflow existe, pero la consulta disponible de estados devolvió una lista vacía y la de ejecuciones filtradas a PR no informó resultados. No se interpreta como éxito ni como ausencia de ejecuciones de push.

Las evidencias anteriores provienen del desarrollo ya ejecutado. Esta actualización es documental y no vuelve a ejecutar pruebas. La [salida de 168 pruebas](evidencias/suite-2026-11-13.txt) corresponde a la etapa anterior, cb12158; no es el resultado más reciente.

## Georreferenciación

Está implementada para **rutas de tours**: mapa Leaflet/OpenStreetMap, latitud/longitud/altitud, puntos, GeoJSON, editor y perfil de elevación. Queda en T17 validar datos operativos, geometrías, red lenta y caída de teselas/CDN. **No se agrega GPS en vivo** como pendiente obligatorio.

## Orden de continuación

1. Decidir alcance (T12) y acreditar CI/esquema (T01).
2. Cerrar recuperación de notificaciones, escenarios restantes de pago, acceso y auditoría (T02–T05).
3. Completar offline/inglés (T13/T16) y validación de modelos IO (T08–T10/T14).
4. Demostrar integraciones y aceptación/usabilidad/rendimiento (T11/T17/T19; revisión T06/T07).
5. Cerrar recuperación/operación y documentación coherente (T18/T15).

No se declara el proyecto listo para producción por estas pruebas locales. Cada pendiente tiene su criterio de aceptación en el plan principal; los documentos de Drive y las decisiones del PO/docente no se han modificado ni supuesto aprobados.
