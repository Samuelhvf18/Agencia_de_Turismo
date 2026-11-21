<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Services\Reservas\SeguridadParticipantes;
use App\Support\ReglasEntrada;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Actualiza el perfil.
     *
     * @param  array<string, mixed>  $input
     */
    public function update(User $user, array $input): void
    {
        $input['email'] = mb_strtolower(trim((string) ($input['email'] ?? '')));
        Validator::make($input, [
            'name' => ['required', 'string', 'max:150', SeguridadParticipantes::NOMBRE],
            'email' => ['required', 'email:rfc', 'max:150', Rule::unique('users', 'cor')->ignore($user->id), function ($attribute, $value, $fail) use ($user) {
                if (User::whereRaw('LOWER(cor) = ?', [$value])->whereKeyNot($user->id)->exists()) {
                    $fail('Este correo electrónico ya está registrado.');
                }
            }],
            'photo' => ['nullable', ...array_diff(ReglasEntrada::imagen(2048), ['required'])],
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $anterior = $user->fot_prf;
            $nueva = $input['photo']->store('profile-photos', 'public');
            $user->forceFill(['fot_prf' => $nueva])->save();
            if ($anterior) {
                Storage::disk('public')->delete($anterior);
            }
        }

        $cambioCorreo = mb_strtolower($user->cor) !== mb_strtolower($input['email']);
        $user->forceFill([
            'cor_vrf_en' => $cambioCorreo ? null : $user->cor_vrf_en,
            'nom' => $input['name'],
            'cor' => $input['email'],
        ])->save();
        if ($cambioCorreo) {
            $user->sendEmailVerificationNotification();
        }
    }
}
