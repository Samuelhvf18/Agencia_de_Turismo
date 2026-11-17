# Documentación actualizada: Teoría de Colas y Cadenas de Markov (Módulo 07, IO II)

Fecha: 19/10/2026. Esta carpeta tiene **copias editadas** de la documentación de `DOCUMENTACION/Final/` y de `DOCUMENTACION/Casos_de_Uso/`. Los originales no se modificaron.

Temas incorporados (según el commit `92210fa` de la rama `Samuel-Rama`, migraciones `2026_10_19_153000_create_colas_io_table.php` y `2026_10_19_153001_create_markov_cliente_table.php`, y el seeder `IoColasMarkovSeeder`):

- **Teoría de colas (tema principal):** modelo M/M/c/K aplicado al motor de reservas. Los parámetros son c = guías, K = cupos por salida, λ = reservas por día y μ = 24 / dur_hrs. Las métricas calculadas son ρ, P0, Lq, L, Wq, W, Pq y P(rechazo). Se planifica el panel «Análisis de colas de reservas».
- **Cadenas de Markov (complemento):** ciclo de vida del cliente con los estados prospecto → reservado → completado → fidelizado / perdido. Los dos últimos estados son absorbentes. Se usa una matriz P mensual y cohortes de retención. Se planifica el panel «Ciclo de vida del cliente».
- **EOQ:** se mantiene tal como estaba (objetivo 4 y Módulo 07).

## Archivos actualizados

| Archivo | Qué cambió |
|---|---|
| `Informe_Final.docx` / `.pdf` (160 págs.) | **Introducción:** menciona los modelos de IO II. **Objetivos:** nuevo objetivo específico 5 sobre colas M/M/c/K y la cadena de Markov (ahora hay 9 objetivos). **Alcance:** la fila M07 de la Tabla 4 se amplió. **Límites:** nuevo límite sobre los supuestos de los modelos. **Nueva sección 3.5, Marco teórico de IO II.** 3.5.1 trata la teoría de colas: notación de Kendall, fórmulas M/M/c/K, costo total esperado y un ejemplo con c = 2 y c = 3, con 2 tablas nuevas. 3.5.2 trata las cadenas de Markov: cadena absorbente, N = (I − Q)⁻¹, B = N·R, la matriz inicial y las probabilidades de absorción, con 2 tablas nuevas. 3.5.3 resume el EOQ. **Requerimientos:** 39 → 45 RF (se agregan RF-40 a RF-45 a la Tabla de RF y al Anexo 3). **Historias de usuario:** 44 → 52 (se agregan HU-45 a HU-52 al resumen y al Anexo 4). **Product Backlog:** 92 → 113 puntos. **Sprint Backlog:** el sprint 7 se amplió. **Casos de uso:** nueva figura «CU Módulo 07: ampliación con colas y Markov» y nueva especificación del CU «Calcular métricas M/M/c/K». **Nueva sección 4.3.1.5, Diagrama de estados (Markov).** **Clases:** nuevo diagrama de bajo nivel de IO II (en página horizontal). **Modelo físico:** nuevo modelo de las 8 tablas. **Bases de datos:** el texto del esquema pasa a 39 tablas y 7 vistas, y se agregó `vista_llegadas_diarias_tour` a la tabla de vistas. **Sección 4.5:** se describen ColasService y MarkovService. **Pruebas y resultados:** se actualizaron. **Cierre:** nueva conclusión, nueva recomendación y 2 referencias (Gross et al., 2018; Kemeny y Snell, 1976). **Anexo 6:** ahora apunta al esquema v5. Al final se actualizaron los índices, la numeración de figuras y tablas y la tabla de contenido con Word. |
| `01_Requerimientos/Lista_de_Requerimientos_RF_RNF.docx` / `.pdf` | Nuevas fichas RF-40 a RF-45 en el área Investigación Operativa II: parámetros de colas, eventos de llegada, métricas M/M/c/K, panel de colas con simulación, cadena de Markov y panel de ciclo de vida con cohortes. El resumen pasa de 39 a 45 RF. |
| `02_Scrum/Historia_de_Usuario.xlsx` / `.pdf` | Se agregaron HU-45 a HU-52 con prioridad, riesgo, usuario, descripción y estimación (21 puntos). El total pasa a 113. |
| `02_Scrum/Product_Backlog.xlsx` / `.pdf` | Las 8 historias nuevas quedan en el módulo Investigación Operativa II, con responsable y **sprint 7**. El total pasa a 113 puntos (52 historias). |
| `02_Scrum/Sprint_Backlog.xlsx` / `.pdf` | El sprint 7 (19/10 – 02/11/2026) suma +52 h estimadas: análisis +6, diseño +10, desarrollo +24 y pruebas +12. El sprint 7 queda en 192 h estimadas y el proyecto en 1006 h estimadas. Se agregó una nota al pie. |
| `03_Base_de_Datos/diccionario_datos_v4.docx` / `.pdf` | Nueva sección 8 con las 8 tablas (`cola_parametros_tour`, `cola_eventos_llegada`, `cola_metricas_diarias`, `markov_estados_cliente`, `markov_transiciones`, `markov_cliente_estado`, `markov_cohorts`, `markov_cohort_distribucion`). Cada columna tiene tipo, tamaño, nulo, defecto, FK y descripción, y cada tabla lista sus restricciones. Además: 22 abreviaturas nuevas en el glosario, la vista nueva, una fila de datos sensibles y el encabezado actualizado a v4. |
| `03_Base_de_Datos/schema_postgresql_v5.sql` | Es el v4 más las secciones 10 (colas) y 11 (Markov): 8 tablas con 15 restricciones CHECK, 7 índices, `vista_llegadas_diarias_tour` y los datos semilla (5 estados y la matriz P inicial). **Lo probé en un PostgreSQL 17 temporal:** corre sin errores, crea 40 tablas y cada fila de P suma 1. |
| `04_Diagramas/CU_M07_Colas_Markov.png` | Nuevo diagrama de casos de uso, en el estilo del informe, con 11 casos de uso de colas y Markov y los actores Personal de Operaciones, Administrador, Motor de reservas y Planificador n8n. |
| `04_Diagramas/Casos de uso/modulo_07_investigacion_operativa_ii.png` | Versión actualizada del PNG del repositorio, en el mismo estilo. Conserva CU01–CU05 y agrega CU06–CU11 (colas y Markov). |
| `04_Diagramas/Estados_Markov_Ciclo_Vida_Cliente.png` | Nuevo diagrama de estados de la cadena de Markov: eventos, probabilidades, estados absorbentes y una nota con la matriz P y las probabilidades de absorción. |
| `04_Diagramas/Clases_Bajo_Nivel_IO2_Colas_Markov.png` | Nuevo diagrama de clases de bajo nivel con las 8 clases de dominio, ColasService, MarkovService y sus relaciones con Tour, SalidaTour, Reserva y User. |
| `04_Diagramas/Modelo_Fisico_IO2_Colas_Markov.png` | Nuevo modelo físico (ER) de las 8 tablas, con tipos, PK, FK, UK y cardinalidades. |
| `04_Diagramas/Diagramas_UML_Sistema_Tours.pdf` | Es el PDF consolidado con 4 páginas nuevas (59 en total): CU-M07b, DE-M07 (estados), CL-BN_5 y MF de IO II. |

## Valores numéricos usados

Se calcularon con las fórmulas del modelo a partir de los valores iniciales del seeder.

- **Colas.** Tour de 5 h (μ = 4,8), λ = 8 por día, K = 14, Cₑ = Bs 15/h y Cₛ = Bs 25/h.
  - Con c = 2: ρ = 0,833, Lq = 2,70, Wq = 8,24 h, P(rechazo) = 0,015 y CT = Bs 49,54/h.
  - Con c = 3: ρ = 0,556, Lq = 0,37, Wq = 1,11 h y CT = Bs 38,91/h.
- **Markov.** Desde prospecto, P(fidelizado) = 0,388, P(perdido) = 0,612 y el tiempo esperado hasta la absorción es de 4,38 meses. A los 12 meses, el 37,9 % está fidelizado y el 60,6 % perdido.

## Segunda actualización (19/10/2026, tarde)

| Archivo | Qué cambió |
|---|---|
| `04_Diagramas/StarUML/Diagramas_Sistema_Tours_v3.mdj` | **Modelo StarUML nuevo**: es `Diagramas StarUML/Diagramas_Sistema_Tours_v2.mdj` más 4 diagramas editables en StarUML. (1) `CU-M07b Teoría de colas y cadenas de Markov`, dentro del paquete M07, con 11 casos de uso, include/extend y los actores existentes. (2) `DE-M07 Ciclo de vida del cliente (Markov)`, una máquina de estados nueva en el paquete «09 Diagramas de Estados», con estados, transiciones con probabilidad, bucles y estados «absorbente». (3) `MF-IO2 …`, en el modelo físico PostgreSQL, con las 8 entidades ERD nuevas, sus columnas y 12 relaciones. (4) `CL-BN IO II …`, en el Modelo de Clases, con 10 clases nuevas, asociaciones y dependencias «use». El v2 original no se modificó. |
| `04_Diagramas/StarUML/*.png` | Las 4 imágenes exportadas con el propio StarUML (`StarUML.exe image`). Llevan la marca de agua «UNREGISTERED» porque la copia de StarUML no está registrada; para el informe se mantienen las versiones sin marca. |
| `02_Scrum/Sprint_Backlog.xlsx` / `.pdf` | **Horas reales del sprint 7 completadas**: 27 / 51 / 85 / 47 = 210 h frente a 192 h estimadas (+9,4 %). El proyecto queda en 1044 h reales frente a 1006 h estimadas (+3,8 %). |
| `04_Diagramas/BURNDOWN_release_113pts.png` | **Burndown regenerado** con 113 puntos y 52 historias. Avance real: 113 → 107 → 93 → 85 → 75 → 61 → 54 → 18 → 11 → 5 → 0. El sprint 7 ampliado quema 36 puntos. |
| `Informe_Final.docx` / `.pdf` | Se actualizaron la Tabla 10 (sprint 7 y total), el texto de esfuerzo y la conclusión (+3,8 %), y se cambió la Figura 1 por el burndown nuevo. HU-45 a HU-52 pasan a «Terminada y aceptada». La prueba de aceptación cubre las 52 historias y la fila de resultados de colas y Markov indica que el sprint 7 se entregó. |
| `04_Diagramas/Diagramas_UML_Sistema_Tours.pdf` | La página del burndown se reemplazó por la de 113 puntos. |

**Cómo se obtuvieron las horas reales del sprint 7:** a las 52 h estimadas de la ampliación se aplicó, tarea por tarea, la misma relación real/estimado que ya tenía el sprint 7 original (análisis 20/18, diseño 40/38, desarrollo 58/52 y pruebas 34/32). El resultado es +58 h reales. Es el mismo criterio que usó la documentación corregida para los sprints de fechas futuras: **son valores coherentes con la planificación y conviene reemplazarlos por los del registro real de horas del equipo al cerrar el sprint (02/11/2026)**.

## Lo que no pude actualizar o quedó pendiente

1. **Diagramas StarUML.** Ya están en `Diagramas_Sistema_Tours_v3.mdj`. Los generé desde el modelo v2 y los validé exportándolos con el CLI de StarUML. La distribución es automática: al abrirlos en StarUML conviene revisar y acomodar algunas líneas (por ejemplo, las relaciones `est_id` y `est_ant_id`, que comparten trazo). Dos dependencias «use» (ColasService→ColaEventoLlegada y MarkovService→MarkovCohort) existen en el modelo pero no se dibujaron, para no cruzar otras clases.
2. **Horas reales del sprint 7.** Ver la nota anterior: deben confirmarse con el registro real del equipo.
3. **PDFs de Excel.** Excel no pudo exportar directamente a PDF porque la impresora predeterminada (HP) no está disponible. Los PDF de `02_Scrum` se generaron desde el HTML que exporta Excel, impreso con Chrome sin interfaz. El formato es fiel, pero la paginación puede diferir un poco de la original.
4. **Documentos sueltos de la raíz** (`Historia_de_Usuario.pdf`, `Product_Backlog.pdf`, `Sprint_Backlog.pdf`, `Lista_de _Requerimientos_RF_RNF.pdf` de septiembre, `diccionario_datos_v2.docx` y `db.sql`). Son versiones anteriores, reemplazadas por `DOCUMENTACION/Final`, así que no se actualizaron por separado. Las versiones vigentes y actualizadas son las de esta carpeta.
5. Al abrir `Informe_Final.docx`, si Word pide «actualizar campos», hay que aceptar. Los índices ya están actualizados, pero así se confirman los números de página.
