<?php

namespace App\Models\Concerns;

trait ResuelveRutaPorClaveLegible
{
    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        // Conserva enlaces por ID.
        $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($field === null && $id !== false) {
            $columnaId = $this->qualifyColumn($this->getKeyName());
            $columnaLegible = $this->qualifyColumn($this->getRouteKeyName());

            return $query->where(fn ($rutas) => $rutas->where($columnaLegible, $value)->orWhere($columnaId, $id))
                ->orderByRaw('CASE WHEN '.$columnaLegible.' = ? THEN 0 ELSE 1 END', [(string) $value]);
        }

        return parent::resolveRouteBindingQuery($query, $value, $field);
    }
}
