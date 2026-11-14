<?php

use App\Models\User;
use Laravel\Fortify\Features;
use Laravel\Jetstream\Http\Livewire\TwoFactorAuthenticationForm;
use Livewire\Livewire;
use PragmaRX\Google2FA\Google2FA;

test('two factor authentication can be enabled', function () {
    $this->actingAs($user = User::factory()->create()->fresh());

    $this->withSession(['auth.password_confirmed_at' => time()]);

    Livewire::test(TwoFactorAuthenticationForm::class)
        ->call('enableTwoFactorAuthentication');

    $user = $user->fresh();

    expect($user->two_factor_secret)->not->toBeNull();
    expect($user->recoveryCodes())->toHaveCount(8);
})->skip(function () {
    return ! Features::canManageTwoFactorAuthentication();
}, 'Two factor authentication is not enabled.');

test('recovery codes can be regenerated', function () {
    $this->actingAs($user = User::factory()->create()->fresh());

    $this->withSession(['auth.password_confirmed_at' => time()]);

    $component = Livewire::test(TwoFactorAuthenticationForm::class)
        ->call('enableTwoFactorAuthentication')
        ->call('regenerateRecoveryCodes');

    $user = $user->fresh();

    $component->call('regenerateRecoveryCodes');

    expect($user->recoveryCodes())->toHaveCount(8);
    expect(array_diff($user->recoveryCodes(), $user->fresh()->recoveryCodes()))->toHaveCount(8);
})->skip(function () {
    return ! Features::canManageTwoFactorAuthentication();
}, 'Two factor authentication is not enabled.');

test('two factor authentication can be disabled', function () {
    $this->actingAs($user = User::factory()->create()->fresh());

    $this->withSession(['auth.password_confirmed_at' => time()]);

    $component = Livewire::test(TwoFactorAuthenticationForm::class)
        ->call('enableTwoFactorAuthentication');

    $this->assertNotNull($user->fresh()->two_factor_secret);

    $component->call('disableTwoFactorAuthentication');

    expect($user->fresh()->two_factor_secret)->toBeNull();
})->skip(function () {
    return ! Features::canManageTwoFactorAuthentication();
}, 'Two factor authentication is not enabled.');

test('el login con 2FA rechaza un código incorrecto y acepta TOTP', function () {
    $otp = new Google2FA;
    $secret = $otp->generateSecretKey();
    $user = User::factory()->create(['two_factor_secret' => encrypt($secret), 'two_factor_confirmed_at' => now(),
        'two_factor_recovery_codes' => encrypt(json_encode(['recovery-code-once']))]);
    $this->post('/login', ['login' => $user->cor, 'password' => 'password'])->assertRedirect('/two-factor-challenge');
    $this->assertGuest();
    $this->post('/two-factor-challenge', ['code' => 'incorrecto'])->assertSessionHasErrors('code');
    $this->assertGuest();
    $this->post('/two-factor-challenge', ['code' => $otp->getCurrentOtp($secret)])->assertRedirect();
    $this->assertAuthenticatedAs($user);
});

test('el código de recuperación permite entrar una sola vez', function () {
    $user = User::factory()->create(['two_factor_secret' => encrypt((new Google2FA)->generateSecretKey()),
        'two_factor_confirmed_at' => now(), 'two_factor_recovery_codes' => encrypt(json_encode(['recovery-code-once']))]);
    $this->post('/login', ['login' => $user->cor, 'password' => 'password'])->assertRedirect('/two-factor-challenge');
    $this->post('/two-factor-challenge', ['recovery_code' => 'recovery-code-once'])->assertRedirect();
    $this->assertAuthenticatedAs($user);
    expect($user->fresh()->recoveryCodes())->not->toContain('recovery-code-once');
    $this->post('/logout');
    $this->post('/login', ['login' => $user->cor, 'password' => 'password']);
    $this->post('/two-factor-challenge', ['recovery_code' => 'recovery-code-once'])->assertSessionHasErrors('recovery_code');
    $this->assertGuest();
});
