composer require laravel/socialite  -> para el tema de google verificacion
view/chatbot/index= se cambio codigo
se instalo npm install alpinejs 
y en resources/js/app.js se modifico 
revisen el .env.exampple
se ejecuto:php artisan make:controller ChatbotController
en services.php se añadio:'gemini' => ['key' => env('GEMINI_API_KEY'),'model' => env('GEMINI_MODEL', 'gemini-3.8-flash'),],  y 'recaptcha' => ['site_key' => env('RECAPTCHA_SITE_KEY'),'secret_key' => env('RECAPTCHA_SECRET_KEY'),],
en web se añadio: use App\Http\Controllers\ChatbotController; y Route::post('/chatbot/mensaje', [ChatbotController::class,'enviar',])->name('chatbot.mensaje'); y use App\Http\Middleware\RoleMiddleware;
SE ARREGLO CIERTAS COSAS DEL INICIO DE SESION
en jetstream.php: en 'features' => Features::termsAndPrivacyPolicy(), deja de estar comentado
se añadio policy.md y terms.md sus codigos
se añadio legal.blade.php para el terms y policy
se añadio en layout autenticacion.blade.php
en css se creo login.css y register.css
en js se creo login.js y register.js
se añadio en auth carpeta particion y adentro: login-form.blade.php y register-form.blade.php
se hurgo: ResetUserPassword.php
se añadio php artisan make:migration create_password_reset_tokens_table
en user.php se añadio  public function routeNotificationForMail($notification): y public function sendPasswordResetNotification($token): void
en forgot-password.blade.php se metio codigo
reset-password.blade.php se metio codigo
en app.js se añadio algo
CreateNewUser.php se añadio cosas 

se añadio app/Http/Middleware/RoleMiddleware.php para el redirijimiento de sesion para que no envie por default al panel de admin
se hizo un cambio en web Route::prefix('admin')->name('admin.')->group(function () { por  Route::prefix('admin')->name('admin.')->middleware(RoleMiddleware::class .':admin') ->group(function () {

 Se modifico   Route::middleware(['auth:sanctum',config('jetstream.auth_session'),'verified',])->group(function () { por Route::get('/dashboard', function () {$user = auth()->user();$rol = $user->rol?->nom;return match ($rol) {'admin' => redirect()->route('admin.dashboard'),'guia' => redirect()->route('guias.checkin'),'operaciones', 'chofer', 'turista' => view('dashboard-rol'),default => abort(403, 'Tu usuario no tiene un rol válido.'),};})->name('dashboard'); Route::get('/guias/checkin', function () {return view('guias.checkin');})->middleware(RoleMiddleware::class . ':guia')->name('guias.checkin');
 se creo resources/views/dashboard-rol.blade.php para un panel de redireccion sensilla para que no te mande a admin o error
 se modifico una parte de web de admin para que redirija bien el coso de sesion
 