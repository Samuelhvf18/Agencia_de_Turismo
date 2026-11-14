# Administración y planificación: revisión de interfaz

El panel usa ahora un mismo diseño para administración e Investigación Operativa:
menú agrupado, jerarquía de títulos, tarjetas de indicadores, tablas y formularios
coherentes. Los módulos de finanzas y seguridad mantienen su visibilidad por rol.
La hoja de estilo está limitada a `.admin-shell`; el portal público conserva su diseño.

La entrada de Investigación Operativa es **Planificación inteligente** (`/admin/io`):

| Herramienta | Pregunta del administrador | Operación |
|---|---|---|
| Demanda y capacidad | ¿Necesito más guías? | Comparar situación actual y escenario, sin cambiar parámetros ni asignaciones |
| Asignar recursos | ¿A quién asigno cada salida? | Generar propuestas, revisar motivos, elegir recurso y confirmar en un diálogo |
| Planificar equipo | ¿Qué equipo debo revisar? | Revisar stock, umbral y suficiencia de datos; filtrar equipos; registrar alertas |
| Retener clientes | ¿Mis clientes vuelven a viajar? | Ver etapas actuales y explorar la evolución estimada de un grupo hipotético de 100 |
| Ajustes del análisis | ¿Qué valores representan cada tour? | Editar configuración con unidades y explicaciones; conservar historial |

Los conceptos de EOQ, Markov y colas se explican en lenguaje cotidiano. Las fórmulas
quedan en secciones desplegables. Los datos de demostración tienen un aviso visible.
Las estimaciones de equipo no se presentan como pedidos de compra; el análisis cuenta
alquileres de equipo reutilizable. Las probabilidades de clientes no se presentan como
resultados individuales ni como garantía de ventas. La alternativa de menor costo
se distingue de la de menor espera.

## Correcciones del simulador

- Cambiar la demanda simulada conserva intacta la situación actual y su fuente.
- Cambiar solamente demanda también activa la comparación.
- La capacidad admite hasta 200, igual que la configuración; se rechaza una capacidad
  menor que los guías en el formulario operativo.
- El cálculo de probabilidades se normaliza en logaritmos para evitar desbordamientos
  en escenarios de demanda alta y capacidad grande.

## Verificación ejecutada

- Suite completa: **170 aprobadas, 4 omitidas, ninguna fallida**, 872 aserciones.
  Las omisiones preexistentes corresponden a 2FA deshabilitado y registro habilitado.
- `npm run build`: correcto, Vite 6.4.3.
- Chromium con datos de QA: 16 pantallas administrativas a 360 px sin desbordamiento
  del documento. Las tablas anchas se desplazan en su propio contenedor.
- Dashboard y las cinco herramientas de planificación también revisados a 768 px;
  capturas de escritorio a 1440 px. Sin errores de JavaScript en los recorridos.
- Comprobados: simulación y comparación, desplegables, filtro de equipos con resultado
  vacío, apertura/cancelación del diálogo, confirmación real de una asignación en la
  base desechable, apertura y cierre por Escape del menú móvil.
- No se usaron datos operativos ni se enviaron notificaciones externas en las pruebas.

No se ha realizado una sesión de usabilidad con un administrador real ni una revisión
exhaustiva con lector de pantalla o en Safari/Firefox. El diseño incluye etiquetas,
indicador de foco, enlace para saltar al contenido, tablas con desplazamiento y
confirmación mediante diálogo nativo.
