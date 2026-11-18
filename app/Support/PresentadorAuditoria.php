<?php

namespace App\Support;

use App\Models\BitacoraAuditoria;
use Illuminate\Support\Str;

class PresentadorAuditoria
{
    public const ACCIONES = [
        'CREATE' => 'Creación',
        'UPDATE' => 'Modificación',
        'DELETE' => 'Eliminación',
        'READ' => 'Consulta',
    ];

    private const RECURSOS = [
        'users' => 'Cuentas de usuario',
        'roles' => 'Roles',
        'perfiles_guia' => 'Perfiles de guías',
        'perfiles_chofer' => 'Perfiles de choferes',
        'idiomas_guia' => 'Idiomas de guías',
        'categorias_tour' => 'Categorías de tours',
        'tours' => 'Tours',
        'fotos_tour' => 'Fotos de tours',
        'puntos_ruta' => 'Puntos de ruta',
        'salidas_tour' => 'Salidas de tours',
        'vehiculos' => 'Vehículos',
        'bitacora_viajes_vehiculo' => 'Viajes de vehículos',
        'tipos_equipo' => 'Tipos de equipo',
        'items_equipo' => 'Inventario de equipos',
        'equipo_por_tour' => 'Equipo por tour',
        'alquileres_equipo_reserva' => 'Alquileres de equipo',
        'reservas' => 'Reservas',
        'participantes_reserva' => 'Pasajeros de reservas',
        'fichas_medicas' => 'Fichas médicas',
        'boletos' => 'Boletos',
        'comprobantes_pago' => 'Comprobantes de pago',
        'pagos' => 'Pagos',
        'resenas' => 'Reseñas',
        'conversaciones_chatbot' => 'Conversaciones del asistente',
        'mensajes_chatbot' => 'Mensajes del asistente',
        'registros_notificacion' => 'Notificaciones',
    ];

    private const CAMPOS = [
        'id' => 'Número de registro',
        'act' => 'Activo',
        'est' => 'Estado',
        'nom' => 'Nombre',
        'cor' => 'Correo electrónico',
        'rol_id' => 'Rol (ID)',
        'cod' => 'Código',
        'tur_id' => 'Tour (ID)',
        'cap_max' => 'Capacidad máxima',
        'fec_sal' => 'Fecha de salida',
        'hra_sal' => 'Hora programada',
        'hra_ini_rea' => 'Hora real de inicio',
        'hra_fin_rea' => 'Hora real de fin',
        'prf_gui_id' => 'Guía asignado (ID)',
        'prf_cho_id' => 'Chofer asignado (ID)',
        'veh_id' => 'Vehículo (ID)',
        'sal_tur_id' => 'Salida del tour (ID)',
        'nom_ctc' => 'Nombre de contacto',
        'cor_ctc' => 'Correo de contacto',
        'tel_ctc' => 'Teléfono de contacto',
        'cnt_ptc' => 'Cantidad de pasajeros',
        'pre_tot' => 'Precio total',
        'est_pag' => 'Estado del pago',
        'cre_en' => 'Fecha de creación',
        'mod_en' => 'Última actualización',
        'created_at' => 'Fecha de creación',
        'updated_at' => 'Última actualización',
    ];

    public static function recurso(string $tabla): string
    {
        return self::RECURSOS[$tabla] ?? Str::ucfirst(str_replace('_', ' ', $tabla));
    }

    public static function campo(string $campo): string
    {
        return self::CAMPOS[$campo] ?? Str::ucfirst(str_replace('_', ' ', $campo));
    }

    /** PostgreSQL guarda la fila completa y la bitácora anterior puede contener solo los cambios. */
    public static function cambios(BitacoraAuditoria $registro): array
    {
        $antes = $registro->val_ant ?? [];
        $despues = $registro->val_nvo ?? [];
        $campos = array_keys($despues + $antes);
        sort($campos);

        $cambios = [];
        foreach ($campos as $campo) {
            $anterior = $antes[$campo] ?? null;
            $nuevo = $despues[$campo] ?? null;
            if ($registro->acc === 'UPDATE' && $anterior === $nuevo) {
                continue;
            }
            $cambios[$campo] = ['antes' => $anterior, 'despues' => $nuevo];
        }

        return $cambios;
    }

    public static function valor(mixed $valor): string
    {
        return match (true) {
            $valor === null || $valor === '' => 'Sin valor',
            is_bool($valor) => $valor ? 'Sí' : 'No',
            is_array($valor) => json_encode($valor, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE),
            default => (string) $valor,
        };
    }
}
