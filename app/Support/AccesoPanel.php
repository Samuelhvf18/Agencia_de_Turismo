<?php

namespace App\Support;

use App\Models\User;

class AccesoPanel
{
    public static function exigir(array $roles = [User::ROL_ADMIN, User::ROL_OPERACIONES]): User
    {
        $u = auth()->user();
        abort_unless($u && $u->act && ! $u->eli_en && $u->hasVerifiedEmail() && $u->tieneRol($roles), 403);

        return $u;
    }
}
