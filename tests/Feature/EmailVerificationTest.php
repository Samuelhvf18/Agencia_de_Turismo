<?php

use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

test('el correo no verificado bloquea el historial y el enlace firmado verifica la columna propia', function () {
    Notification::fake();
    $u = User::factory()->unverified()->create();
    $this->actingAs($u)->get(route('reservas.historial'))->assertRedirect(route('verification.notice'));
    $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(30), ['id' => $u->id, 'hash' => sha1($u->cor)]);
    $this->get($url)->assertRedirect();
    expect($u->fresh()->hasVerifiedEmail())->toBeTrue();
    $this->get(route('reservas.historial'))->assertOk();
});

test('cambiar el correo invalida la verificación y envía un nuevo enlace', function () {
    Notification::fake();
    $u = User::factory()->create();
    app(UpdateUserProfileInformation::class)->update($u, ['name' => $u->nom, 'email' => 'nuevo@example.test']);
    expect($u->fresh()->hasVerifiedEmail())->toBeFalse();
    Notification::assertSentTo($u, VerifyEmail::class);
});
