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
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('usuarios', 'cor')->ignore($user->id),
            ],
            'photo' => [
                'nullable',
                'mimes:jpg,jpeg,png',
                'max:1024',
            ],
        ])->validateWithBag('updateProfileInformation');
        if (isset($input['photo'])) {
            $user->forceFill([
                'fot_prf' => $input['photo']->store('profile-photos', 'public'),
            ])->save();
        }
        $user->forceFill([
            'nom' => $input['name'],
            'cor' => $input['email'],
        ])->save();
    }
}