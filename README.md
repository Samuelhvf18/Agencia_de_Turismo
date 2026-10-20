# Pipocas · Sistema web de control logístico y venta de tours georreferenciados

Sistema para agencias de viajes y turismo de La Paz (Bolivia): catálogo georreferenciado, motor de reservas con pago en línea y boletos QR, logística de salidas, guías, choferes, vehículos y equipo, modelos de Investigación Operativa II, chatbot con IA y tableros de decisión.

**Stack:** Laravel 12 (MVC + capa de servicios), PostgreSQL 16 (JSONB, vistas y CHECK), Livewire/Alpine, Tailwind, Leaflet, Chart.js, n8n, Redis y Pest.

## Módulos

| Módulo | Qué incluye |
|---|---|
| M01 Cuentas y seguridad | Login por correo, registro de turistas, roles (turista, guía, chofer, operaciones, admin) con middleware `rol:`, alertas de seguridad y HTTPS |
| M02 Catálogo | Filtros combinados, mapa con trazado GeoJSON, perfil de elevación, reseñas verificadas y CRUD de tours, galería y trazado |
| M03 Personal y recursos | Guías (idiomas, horas), choferes (licencia), flota con estados, equipo por talla y por tour |
| M04 Operaciones | Programación y asignación con validaciones, reprogramación y cancelación con aviso, check-in QR sin conexión |
| M05 Reservas | Wizard de 5 pasos, cupo transaccional, invitados, ficha médica con firma, boletos QR en PDF, consulta y cancelación |
| M06 Pagos | Pasarela simulada con webhook HMAC, pagos parciales, comprobante PDF con IVA, anulación por estado y efectivo |
| M07 IO II | Colas M/M/c/K, cadena de Markov, método húngaro (guías), asignación de vehículos, propuestas revisables y EOQ |
| M08 Chatbot | Agente de IA en n8n con herramientas de Laravel, respaldo local y derivación a una persona |
| M09 Reseñas y dashboards | Dashboard, analítica con las 7 vistas SQL y proyección de demanda |
| M10 Notificaciones y auditoría | Bitácora con antes/después, lecturas sensibles, notificaciones vía n8n o correo |

El detalle por requisito está en [`docs/ESTADO-IMPLEMENTACION.md`](docs/ESTADO-IMPLEMENTACION.md).

## Puesta en marcha

Ver [`docs/DESPLIEGUE.md`](docs/DESPLIEGUE.md) (desarrollo local, Docker, respaldo) y [`docs/n8n/README.md`](docs/n8n/README.md) (flujos de n8n).

```bash
composer install && npm install && npm run build
cp .env.example .env && php artisan key:generate
php artisan migrate:fresh --seed && php artisan storage:link
php artisan serve
php artisan test   # 160 pruebas (requiere la base agencia_turismo_test)
```
