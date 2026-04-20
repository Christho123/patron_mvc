# SecureAuth

## Titulo ideal para PPT
**SecureAuth: Sistema de autenticacion multifactor con OTP y reconocimiento facial**

## Descripcion general
SecureAuth es un sistema web desarrollado en PHP con arquitectura MVC para registrar usuarios, autenticar acceso con contrasena, validar un codigo OTP por correo y reforzar el ingreso con reconocimiento facial. Tambien incluye un dashboard de analisis de actividad y una vista de administracion de usuarios.

## Objetivo del sistema
Centralizar el registro y acceso de usuarios con un esquema de autenticacion reforzada que combine credenciales tradicionales, verificacion temporal por correo y validacion biometrica facial, con registro de eventos para control y seguimiento.

## Alcance
- Registro de usuarios con validacion de contrasenas.
- Inicio de sesion con usuario y contrasena.
- Envio y verificacion de OTP por correo.
- Registro y validacion de rostro mediante la camara.
- Acceso al dashboard despues de superar los factores de seguridad.
- Listado de usuarios y analisis basico de actividad.
- Registro de intentos y eventos en bitacora.

## Funcionalidades clave
- Registro de cuenta con correo y usuario unicos.Registro de cuenta con correo y usuario unicos.
- Validacion de contrasena con `password_hash` y `password_verify`.
- Generacion de OTP de 6 digitos con vigencia de 5 minutos.
- Envio de OTP mediante correo SMTP.
- Verificacion biometrica facial con `face-api.js`.
- Acceso al sistema solo despues de completar la validacion correspondiente.
- Dashboard con indicadores y grafico de actividad.
- Tabla de usuarios con estado: activo, pendiente o expirado.
- Registro de logs de autenticacion y registro.
- Deteccion de duplicados de registro por correo o usuario.

## Tecnologias utilizadas
### Backend
- PHP
- PDO
- MySQL
- PHPMailer

### Frontend
- HTML5
- CSS3
- JavaScript
- Fetch API
- WebRTC / MediaDevices para acceso a camara

### Librerias y servicios
- face-api.js para reconocimiento facial
- Chart.js para graficas del dashboard
- SweetAlert2 para mensajes interactivos
- Font Awesome para iconografia
- Google Fonts para tipografia

### Entorno
- XAMPP
- Apache

## Arquitectura del proyecto
- `Controllers`: contiene la logica de autenticacion, verificacion facial y dashboard.
- `Models`: maneja el acceso a datos de usuarios, rostros, logs y duplicados.
- `Views`: presenta las pantallas de login, registro, OTP, escaneo facial y dashboard.
- `Assets`: almacena CSS y JavaScript del sistema.
- `Config`: concentra la conexion a base de datos.
- `libs`: contiene PHPMailer.

## Metodologia de desarrollo
Metodologia sugerida para la presentacion:
- Analisis de requerimientos.
- Diseno de arquitectura MVC.
- Desarrollo iterativo por modulos.
- Pruebas funcionales por cada flujo.
- Ajustes y validacion final.

## Fases del proyecto
1. Levantamiento de requerimientos.
2. Analisis del flujo de autenticacion.
3. Diseno de pantallas y estructura MVC.
4. Implementacion del registro y login.
5. Implementacion del OTP por correo.
6. Implementacion del reconocimiento facial.
7. Implementacion del dashboard y logs.
8. Pruebas y correcciones.

## Analisis del sistema
El sistema resuelve el problema de acceso inseguro agregando capas de validacion. Primero comprueba credenciales, luego valida un OTP temporal enviado al correo y, segun el flujo, completa el acceso con reconocimiento facial. Esto reduce el riesgo de acceso no autorizado y permite registrar trazabilidad de eventos.

## Diseno
### Diseno funcional
- Login con usuario y contrasena.
- Registro de cuenta.
- Verificacion OTP.
- Escaneo facial.
- Dashboard administrativo.

### Diseno logico
- `usuarios`: almacenamiento de cuentas, OTP y estados.
- `user_faces`: almacenamiento de vectores faciales.
- `user_logs`: registro de acciones del sistema.
- `duplicate_attempts`: registro de intentos de registro duplicado.

### Capturas sugeridas para PPT
- Pantalla de login.
- Pantalla de registro.
- Pantalla OTP.
- Pantalla de escaneo facial.
- Dashboard de analisis.
- Tabla de usuarios.

## Niveles de seguridad
### Seguridad implementada
- Hash de contrasenas con `password_hash`.
- Verificacion de contrasenas con `password_verify`.
- OTP temporal con expiracion de 5 minutos.
- Sesiones con cookies `HttpOnly` y `SameSite=Lax`.
- Uso de consultas preparadas con PDO.
- Registro de acciones en logs.
- Validacion de duplicados al registrar.

### Seguridad biometrica
- Reconocimiento facial con `face-api.js`.
- Comparacion por distancia entre vectores faciales.
- Segundo factor de autenticacion basado en rostro.

### Seguridad recomendada para una siguiente version
- Mover credenciales SMTP y de base de datos a variables de entorno.
- Agregar proteccion CSRF en formularios.
- Limitar intentos de login por IP o usuario.
- Restringir el acceso a dashboard mediante middleware de sesion.
- Centralizar la configuracion en un archivo de entorno.

## Base de datos
### Tabla `usuarios`
Campos observados en el codigo:
- `id`
- `usuario`
- `email`
- `password`
- `verified`
- `estado`
- `otp_code`
- `otp_expiracion`
- `created_at`

### Tabla `user_faces`
Campos observados en el codigo:
- `user_id`
- `face_data`

### Tabla `user_logs`
Campos observados en el codigo:
- `user_id`
- `action`
- `created_at`

### Tabla `duplicate_attempts`
Campos observados en el codigo:
- `email`
- `ip_address`

## Flujo principal
1. El usuario se registra.
2. El sistema valida duplicados.
3. Se guarda la contrasena con hash.
4. Se genera y envia un OTP.
5. El usuario ingresa el OTP.
6. Si corresponde, se activa el escaneo facial.
7. El sistema valida el rostro.
8. El usuario entra al dashboard.

## Pruebas
- `prueba_base_datos.php`: valida la conexion y la existencia de tablas.
- `prueba_seguridad.php`: valida hash de contrasenas, OTP y calculo de distancia facial.
- `prueba_logs.php`: revisa registros y estadisticas de logs.

## Mantenimiento
- Ver `MANTENIMIENTO_SISTEMA.md` para lineamientos de operacion, respaldo, actualizacion y soporte.

## Observacion importante
El codigo actual incluye credenciales de correo y base de datos en archivos de configuracion. Para una version de produccion, lo ideal es mover esos datos a variables de entorno y restringir el acceso al dashboard con una verificacion de sesion mas estricta.

