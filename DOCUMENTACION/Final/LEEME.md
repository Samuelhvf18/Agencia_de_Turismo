# Documentación final — Sistema Web de Control Logístico y Venta de Tours Georreferenciados

**Fecha de consolidación:** 20/10/2026

Esta carpeta reúne la documentación más completa y actual del proyecto.

## Contenido

| Elemento | Qué es |
|---|---|
| `Informe_Final.docx` / `.pdf` | Informe final completo (160 págs, 55 figuras, 19 tablas, anexos 1–6), actualizado el 19/10/2026 con la ampliación de IO II. |
| `01_Requerimientos\` | Lista de Requerimientos RF/RNF: 45 RF + 20 RNF (Word y PDF). |
| `02_Scrum\` | Historias de Usuario, Product Backlog y Sprint Backlog: 52 historias, 113 puntos, 10 sprints (Excel editables + PDF). |
| `03_Base_de_Datos\` | Diccionario de Datos v5 y esquema PostgreSQL v6 (41 tablas y 7 vistas). Se conservan v4/v5 como historial. |
| `04_Diagramas\` | PDF consolidado de 59 páginas con todos los diagramas, PNG individuales y modelo editable de StarUML v3. |
| `05_Evaluacion_Procesual\` | Documento unificado para la evaluación procesual: perfil del proyecto + análisis y diseño (98 págs.), versión solo de análisis y diseño (79 págs.) y guía de entrega. |
| `Investigacion_Operativa\` | Informe del Segundo Avance de IO II (colas M/M/c/K y cadenas de Markov). |
| `Entrega hito 3\` | Entregables del hito: `Procesual\` y `Continua\` (casos de uso, secuencias, modelos de BD, diccionario y evaluaciones). |
| `LEEME.md` | Este índice. |

> Las notas internas (`README.md`, `AJUSTES-AL-INFORME.md` y `PLAN-DE-TRABAJO.md`) se guardaron fuera de esta carpeta, en `..\Notas_Internas\`, para no incluirlas en la entrega.

## Versiones vigentes

- **Informe:** 19/10/2026 (incluye teoría de colas y cadenas de Markov).
- **Requerimientos:** 45 RF y 20 RNF.
- **Scrum:** 52 historias y 113 puntos.
- **Diccionario de datos:** v5 (incluye la sección 8 de IO II y `propuestas_asignacion`).
- **Esquema SQL:** v6, 41 tablas y 7 vistas.
- **Diagramas:** PDF consolidado de 59 páginas (incluye CU-M07b, DE-M07, CL-BN_5 y MF de IO II).

## Nota sobre el modelo físico

El `03_Modelo_Fisico_PostgreSQL.png` de la entrega corresponde al esquema base v4 (31 tablas), tal como figura en el informe. El módulo de IO II se documenta aparte (`04_Modelo_Fisico_IO2_Colas_Markov.png`, esquema v5) y el esquema vigente es el v6 (`schema_postgresql_v6.sql`), que agrega `propuestas_asignacion`.
