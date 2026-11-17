/* Formulario guiado de IO. El servidor valida y convierte las horas al guardar. */
(function (root) {
    root.ioAjustes = function (config) {
        const valores = { ...config.valores, motivo: config.motivo || '' };
        return {
            config, ...valores, revisado: false,
            numero(valor, decimales = 2) {
                return valor !== '' && Number.isFinite(Number(valor))
                    ? Number(valor).toLocaleString('es-BO', { maximumFractionDigits: decimales }) : '—';
            },
            get error() {
                const numero = (v) => v !== '' && v !== null && Number.isFinite(Number(v));
                if (!numero(this.horas) || this.horas <= 0 || this.horas > 240000) return 'Indica un tiempo mayor que cero para realizar el tour.';
                if (!numero(this.guias) || !Number.isInteger(Number(this.guias)) || this.guias < 1 || this.guias > 60) return 'La cantidad de guías debe ser un número entero entre 1 y 60.';
                if (!numero(this.capacidad) || !Number.isInteger(Number(this.capacidad)) || Number(this.capacidad) < Number(this.guias) || this.capacidad > 200) return 'Abre los ajustes opcionales: el límite de solicitudes debe ser un entero igual o mayor que la cantidad de guías, y no superar 200.';
                if (!numero(this.demanda) || this.demanda < 0 || this.demanda > 9999) return 'Revisa las solicitudes diarias de referencia en los ajustes opcionales: deben estar entre 0 y 9999.';
                for (const costo of [this.espera, this.ocioso]) {
                    if (!numero(costo) || costo < 0 || costo > 999999) return 'Revisa los costos opcionales: deben estar entre Bs 0 y Bs 999999.';
                }
                return '';
            },
            get cambios() {
                const etiquetas = { horas: 'Tiempo por tour (h)', guias: 'Guías', capacidad: 'Límite de solicitudes', demanda: 'Referencia diaria', espera: 'Costo de espera (Bs/h)', ocioso: 'Guía sin actividad (Bs/h)' };
                return Object.entries(etiquetas).filter(([clave]) => Number(this[clave] || 0) !== Number(config.actual[clave]))
                    .map(([clave, etiqueta]) => etiqueta + ': ' + this.numero(config.actual[clave], 4) + ' → ' + this.numero(this[clave] || 0, 4));
            },
            usarDuracion() { this.horas = config.duracion; this.revisado = false; },
            restablecer() { Object.assign(this, config.actual, { motivo: '', revisado: false }); },
            enviar(evento) { if (this.error || !this.revisado) evento.preventDefault(); },
        };
    };
})(globalThis);
