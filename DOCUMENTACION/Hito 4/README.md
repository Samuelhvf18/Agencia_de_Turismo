# Hito 4 — Diagramas del sistema

**Sistema Web de Control Logístico, Asignación de Recursos y Venta de Tours Georreferenciados para Agencias de Viajes y Turismo**

Compilado el 20/11/2026. Esta carpeta reúne todos los diagramas elaborados del proyecto, organizados por tipo.

## Estructura

| Carpeta | Contenido | Cantidad |
|---|---|---:|
| `01_Casos_de_Uso` | Diagramas de casos de uso (versión más actual) + PDF original | 11 PNG + 1 PDF |
| `02_Actividades` | Diagramas de actividades por módulo | 10 PNG |
| `03_Secuencia` | Diagramas de secuencia por módulo | 10 PNG |
| `04_Paquetes` | Diagramas de paquetes por sprint + PDF consolidado | 10 PNG + 1 PDF |
| `05_Clases` | Diagrama de clases de bajo nivel (IO II: colas y Markov) | 1 PNG |
| `06_Estados` | Diagrama de estados del ciclo de vida del cliente (Markov) | 1 PNG |
| `07_Base_de_Datos` | Modelos conceptual, lógico, físico PostgreSQL, físico IO II y ERD | 5 PNG |
| `08_Otros` | Burndown del release (113 pts) | 1 PNG |
| `09_PDF_Consolidados` | PDF unificado UML del sistema y PDF de secuencia | 2 PDF |
| `10_StarUML` | Fuentes editables de StarUML | 2 `.mdj` |

## 01 — Casos de uso

Fuente: `Agencia_Turismo_11_Casos_de_Uso.pdf` (11 páginas, versión más reciente). Los PNG se extrajeron de ese PDF a resolución nativa.

- `CU-M01_cuentas_y_seguridad`
- `CU-M02_catalogo_de_tours`
- `CU-M03_personal_y_recursos`
- `CU-M04_operaciones_y_salidas`
- `CU-M05_motor_de_reservas`
- `CU-M06_facturacion_y_pagos`
- `CU-M07_investigacion_operativa_ii`
- `CU-M07b_teoria_colas_cadenas_markov`
- `CU-M08_chatbot_turistico`
- `CU-M09_resenas_y_dashboards`
- `CU-M10_notificaciones_y_auditoria`

## 02 — Actividades

Extraídos de `09_PDF_Consolidados\Diagramas_UML_Sistema_Tours.pdf` (páginas 12–21). No existe fuente editable en StarUML para estos diagramas.

- `DA-M01_iniciar_sesion`
- `DA-M02_registrar_tour`
- `DA-M03_registrar_personal`
- `DA-M04_programar_salida`
- `DA-M05_reservar_tour`
- `DA-M06_pagar_reserva`
- `DA-M07_asignacion_optima`
- `DA-M08_chatbot`
- `DA-M09_resenas_dashboards`
- `DA-M10_auditoria_notificaciones`

## 03 — Secuencia

- `DS-M01_iniciar_sesion`
- `DS-M02_gestionar_catalogo`
- `DS-M03_gestionar_personal`
- `DS-M04_programar_salida`
- `DS-M05_reservar_tour`
- `DS-M06_pagar_reserva`
- `DS-M07_asignacion_optima`
- `DS-M08_chatbot`
- `DS-M09_resenas_dashboards`
- `DS-M10_notificaciones_auditoria`

## 04 — Paquetes

Un diagrama por sprint (`SP01`–`SP10`), con paquetes raíz `Sprint N / Módulo`, paquetes de HU y dependencias externas. Ver el PDF consolidado para la vista de una página por sprint.

## 07 — Base de Datos

- `01_Modelo_Conceptual.png`
- `02_Modelo_Logico.png`
- `03_Modelo_Fisico_PostgreSQL.png`
- `04_Modelo_Fisico_IO2_Colas_Markov.png`
- `EXTRA_ERD_PostgreSQL.png`

## 10 — Fuentes StarUML

- `Diagramas_Sistema_Tours_v3.mdj`: casos de uso, secuencia, clases, ERD y estados.
- `Diagramas_Paquetes_Sprints.mdj`: los 10 diagramas de paquetes por sprint.

**Nota:** el proyecto `Diagramas_Sistema_Tours_v3.mdj` no incluye diagramas de actividad; estos solo existen como imagen en el PDF consolidado (y en `02_Actividades` de esta carpeta).
