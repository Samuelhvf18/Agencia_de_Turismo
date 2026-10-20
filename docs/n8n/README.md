# Flujos de n8n

El sistema usa n8n para tres cosas:

1. **Planificador de IO II y tareas programadas**: `planificador-io.json`.
2. **Notificaciones** (confirmación, pago, recordatorios, cambios de salida, alertas): `notificaciones.json`.
3. **Chatbot turístico con IA**: `chatbot.json`.

## Puesta en marcha

1. Importa cada archivo en n8n (*Workflows → Import from file*).
2. Define estas variables de entorno en el contenedor de n8n:

   | Variable | Ejemplo | Uso |
   |---|---|---|
   | `PIPOCAS_URL` | `https://pipocas.example.com` | URL base de Laravel |
   | `PIPOCAS_N8N_TOKEN` | (cadena larga aleatoria) | Debe coincidir con `N8N_TOKEN` del `.env` de Laravel |

3. En el `.env` de Laravel:

   ```env
   N8N_TOKEN=la-misma-cadena-que-PIPOCAS_N8N_TOKEN
   N8N_WEBHOOK_NOTIFICACIONES=https://n8n.example.com/webhook/pipocas-notificaciones
   N8N_WEBHOOK_CHATBOT=https://n8n.example.com/webhook/pipocas-chatbot
   ```

4. Activa los flujos.

## Planificador (`planificador-io.json`)

Cada disparador llama a `POST {PIPOCAS_URL}/api/n8n/tareas/{tarea}` con la cabecera `X-Pipocas-Token`. Cada tarea es un comando de Artisan, así que también se puede ejecutar a mano.

| Horario (America/La_Paz) | Tarea | Comando | Requerimiento |
|---|---|---|---|
| Diario 23:55 | `metricas-colas` | `io:metricas-colas` | RF-41/42 |
| Día 1, 02:00 | `recalibrar-markov` | `io:recalibrar-markov --sincronizar` | RF-44/45 |
| Diario 06:00 | `propuestas-guias` | `io:propuestas --tipo=guia` | RF-22/24 |
| Diario 06:10 | `propuestas-vehiculos` | `io:propuestas --tipo=vehiculo` | RF-23/24 |
| Diario 07:00 | `alertas-eoq` | `io:alertas-eoq` | RF-25 a 27 |
| Día 1, 03:00 | `proyectar-demanda` | `io:proyectar-demanda` | RF-35 |
| Diario 18:00 | `recordatorios` | `reservas:recordatorios` | RF-38 |

Las propuestas de asignación **nunca se aplican solas** (RF-24): quedan pendientes en *Admin → Investigación Operativa → Asignación óptima*.

Sin n8n, las mismas tareas se pueden programar con el cron de Laravel descomentando las líneas de `routes/console.php`. La expiración de reservas impagas (`reservas:expirar`) siempre corre en el cron de Laravel.
