Documentación técnica: Login y autenticación de Pipocas
1. Configuración de la conexión a la base de datos
Archivo: .env
Se configuró la conexión de Laravel con PostgreSQL.
Se revisaron los parámetros de conexión: nombre de la base de datos, usuario, contraseña, host y puerto.
Se corrigieron errores de conexión para trabajar con la base de datos que existe 
2. Adaptación del modelo de usuario
Archivo: app/Models/User.php
Se adaptó el modelo User a la tabla existente usuarios.
Se configuró la clave primaria y los nombres personalizados de las columnas.
Se adaptó la autenticación para utilizar clv como campo de contraseña y cor como correo electrónico.
Se configuraron los campos personalizados de creación y modificación, como cre_en y mod_en.
Se eliminó la implementación de TwoFactorAuthenticatable porque la estructura actual de la bd no contempla los campos necesarios para la autenticación de dos factores.
Se configuró la relación con el modelo Role mediante rol_id.
3. Configuración de la autenticación
Archivos:
config/auth.php
config/fortify.php
app/Providers/FortifyServiceProvider.php
Se revisó la configuración del proveedor de autenticación para utilizar el modelo User adaptado a usuarios.
Se ajustó Fortify para trabajar con el campo personalizado cor.
Se implementó la autenticación mediante correo electrónico o nombre de usuario (usr).
Se revisaron las funcionalidades de autenticación para evitar dependencias con campos que no existen en la estructura actual.
Se ajustó el proceso de inicio de sesión para comprobar la contraseña almacenada en clv.
4. Registro de usuarios
Archivo: app/Actions/Fortify/CreateNewUser.php
Vista: resources/views/auth/register.blade.php
Se adaptó el registro para guardar los datos en la tabla usuarios.
Se ajustaron los nombres de los campos del formulario a los utilizados en la base de datos.
Se incorporaron los campos de nombre, apellido paterno, apellido materno, nombre de usuario, correo y demás datos personales previstos.
Se adaptó el almacenamiento de la contraseña al campo clv.
Se contemplaron los campos adicionales:
pat: apellido paterno.
mat: apellido materno.
usr: nombre de usuario.
5. Inicio de sesión
Vista: resources/views/auth/login.blade.php
Se adaptó el formulario a la autenticación personalizada.
Se ajustó el campo de acceso para utilizar el nombre esperado por Fortify (login).
Se permite autenticar mediante correo electrónico o nombre de usuario, según la configuración implementada.
Se ajustaron los nombres de los campos para que coincidan con el proceso de autenticación.
6. Recuperación y restablecimiento de contraseña
Archivos:
resources/views/auth/forgot-password.blade.php
resources/views/auth/reset-password.blade.php
app/Actions/Fortify/ResetUserPassword.php
Se adaptó el proceso de recuperación de contraseña al campo de correo personalizado cor.
Se corrigieron las vistas de solicitud y restablecimiento.
Se ajustó la actualización de la contraseña para guardarla en clv.
Se mantuvo el mecanismo de hash de Laravel para no almacenar contraseñas en texto plano.
7. Reglas y actualización de contraseñas
Archivos:
app/Actions/Fortify/PasswordValidationRules.php
app/Actions/Fortify/UpdateUserPassword.php
Se revisaron las reglas de validación de contraseñas.
Se adaptó la actualización de contraseñas al campo clv.
Se mantuvo la compatibilidad con el modelo User personalizado.
8. Actualización del perfil
Archivo: app/Actions/Fortify/UpdateUserProfileInformation.php
Se adaptó la actualización del perfil a los nombres de columnas de usuarios.
Se revisó el manejo del correo electrónico y de los datos personales.
Se buscó mantener la compatibilidad con la estructura existente de PostgreSQL.
9. Modelo de roles
Archivo: app/Models/Role.php
Se creó el modelo Role para representar los roles del sistema.
Se estableció la relación con los usuarios mediante rol_id.
Los roles identificados en la base de datos son:
1: turista.
2: guía.
3: chofer.
4: operaciones.
5: administrador.
10. Consideraciones adicionales de la base de datos
Se incorporaron o utilizaron campos adicionales para completar el registro de usuarios, entre ellos pat, mat y usr en usaurios.
Se revisó la compatibilidad entre Laravel, Fortify y la estructura existente de PostgreSQL.
Se identificó la necesidad de revisar remember_token, ya que algunas funciones de autenticación de Laravel pueden intentar utilizarlo. Debe añadirse a la base de datos o si se configuró el modelo para no utilizarlo.
11. Información importante para el dashboard
El modelo utilizado por la autenticación es App\Models\User.
El correo se almacena en cor.
La contraseña se almacena con hash en clv.
El rol del usuario se identifica mediante rol_id.
La relación con el rol se maneja mediante el modelo Role.
La cuenta administrativa identificada durante las pruebas tiene rol_id = 5 segun la base de datos.

Antes de implementar el dashboard: comprobar que las rutas y los permisos distingan correctamente a los administradores de los demás usuarios. Tener rol_id = 5 no protege automáticamente las rutas; se debe implementar y verificar el control de acceso correspondiente.

Nota: la autenticación fue adaptada a la estructura existente de la base de datos. Antes de ejecutar migraciones o modificar tablas, revisar los cambios existentes para evitar duplicaciones, conflictos o pérdida de datos.