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

## Notificaciones (`notificaciones.json`)

Laravel registra cada aviso en `registros_notificacion` y `NotificacionJob` lo envía por `POST` a `N8N_WEBHOOK_NOTIFICACIONES` con la cabecera `X-Pipocas-Token`. El flujo valida el token y, según `canal`, envía un correo (credencial SMTP de n8n y variable `PIPOCAS_MAIL_FROM`) o un WhatsApp (API de WhatsApp Cloud con `WHATSAPP_PHONE_ID` y `WHATSAPP_TOKEN`). Responde 200 y Laravel marca el registro como `enviado`; ante un error lo marca `fallido` y se puede reintentar desde *Auditoría → Notificaciones*.

Cuerpo que recibe n8n:

```json
{
  "id": 15, "tipo": "recordatorio_salida", "asunto": "Tu tour es mañana", "canal": "email",
  "datos": {"destino": "cliente@correo.com", "nombre": "Ana Quispe", "mensaje": "..."},
  "reserva": {"cod": "RES-7K3QXM", "contacto": "Ana Quispe", "correo": "...", "telefono": "...",
              "tour": "...", "fecha": "2026-10-25", "hora": "08:00", "total": 260, "estado": "confirmada", "url": "..."}
}
```

Tipos: `reserva_confirmada`, `pago_recibido`, `comprobante_emitido`, `recordatorio_salida`, `salida_reprogramada`, `salida_cancelada`, `alerta_reposicion`, `alerta_seguridad` y `derivacion_chatbot`.

Sin `N8N_WEBHOOK_NOTIFICACIONES`, Laravel envía el correo directamente (`AvisoMail`). Los boletos y el comprobante en PDF siempre los adjunta Laravel (`EnviarBoletosReserva`).

## Chatbot (`chatbot.json`)

`ChatbotService` guarda cada mensaje en `conversaciones_chatbot` y `mensajes_chatbot` y lo envía a `N8N_WEBHOOK_CHATBOT` con `{conversacion_id, mensaje, idioma, historial, usuario}`. El flujo valida el token y pasa el mensaje a un agente de IA (nodo AI Agent con el modelo de Anthropic `claude-sonnet-5-5` y memoria por conversación) que usa cuatro herramientas de Laravel:

| Herramienta | Endpoint | Uso |
|---|---|---|
| `buscar_tours` | `GET /api/n8n/chatbot/tours` | Tours activos por horas, dificultad, presupuesto y categoría (RF-29) |
| `preguntas_frecuentes` | `GET /api/n8n/chatbot/faq` | Respuestas oficiales (RF-30) |
| `consultar_reserva` | `POST /api/n8n/chatbot/reserva` | Estado de una reserva en solo lectura, con código y correo |
| `derivar_a_humano` | `POST /api/n8n/chatbot/derivar` | Marca `der_hum` y avisa a operaciones (RF-31) |

La respuesta debe ser `{"respuesta": "...", "intencion": "...", "derivar": false}`. El agente responde en el idioma del cliente (`idioma`: `es` o `en`).

Configura en n8n la credencial **Anthropic** (API key) y las variables `PIPOCAS_URL` y `PIPOCAS_N8N_TOKEN`. Si n8n no está configurado, tarda más de 20 segundos o falla, Laravel responde con el asistente local por reglas (`AsistenteLocal`), que usa las mismas herramientas (RNF-20). Las conversaciones derivadas se atienden en *Admin → Chatbot (atención humana)*.
