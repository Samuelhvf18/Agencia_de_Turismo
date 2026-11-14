<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, mixed>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:150', "regex:/^[\pL\s'.-]+$/u"],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'cor')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->deleteProfilePhoto();
            $user->forceFill([
                'fot_prf' => $input['photo']->store('profile-photos', 'public'),
            ])->save();
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
