# Mantenimiento de SecureAuth

## Proposito
Este documento resume como mantener el sistema estable, seguro y facil de actualizar sin afectar el flujo de autenticacion.

## Archivos criticos
- `Config/Database.php`: conexion a MySQL.
- `Controllers/AuthController.php`: login, registro, OTP y logs.
- `Controllers/FaceController.php`: registro y verificacion facial.
- `Models/User/User.php`: consultas principales de usuarios.
- `Models/User/Log.php`: estadisticas y bitacora.
- `Views/`: formularios y dashboard.
- `Assets/js/`: logica cliente de OTP, camara, rostro y graficas.

## Tareas de mantenimiento periodicas
- Revisar que la conexion a base de datos siga funcionando.
- Verificar que el envio de correo SMTP responda correctamente.
- Confirmar que los modelos de `face-api.js` sigan disponibles.
- Revisar la bitacora de `user_logs` para detectar comportamientos anormales.
- Limpiar registros vencidos de OTP y usuarios pendientes.
- Validar que el dashboard cargue graficas y listado de usuarios sin errores.

## Respaldo y recuperacion
- Realizar copia de seguridad de la base de datos antes de cambios mayores.
- Exportar las tablas `usuarios`, `user_faces`, `user_logs` y `duplicate_attempts`.
- Conservar una copia del directorio `Assets` y `Views` cuando se actualicen pantallas.
- Probar la restauracion en un entorno local antes de subir cambios a produccion.

## Configuracion
- Evitar dejar credenciales reales dentro del codigo.
- Mover datos sensibles a variables de entorno o archivos de configuracion fuera del repositorio.
- Confirmar que la zona horaria sea consistente con la del servidor.
- Mantener la configuracion de sesiones con tiempo de vida definido.

## Seguridad de operacion
- Revisar que los formularios tengan validacion del lado cliente y servidor.
- Confirmar que se mantengan las consultas preparadas.
- Agregar proteccion CSRF si se amplian los formularios.
- Limitar intentos repetidos de autenticacion.
- Revisar permisos de archivos en el servidor web.

## Actualizacion de dependencias
- Verificar compatibilidad de PHPMailer con la version de PHP instalada.
- Confirmar funcionamiento de `face-api.js`, Chart.js y SweetAlert2 despues de cambios de version.
- Probar la interfaz en navegadores actualizados.

## Soporte funcional
- Si falla el login, revisar la tabla `usuarios`, la contrasena hash y el estado del OTP.
- Si falla el OTP, revisar SMTP, red y vigencia de 5 minutos.
- Si falla el reconocimiento facial, revisar camara, permisos del navegador y disponibilidad de modelos.
- Si falla el dashboard, revisar acceso a `user_logs` y la carga de `Chart.js`.

## Pruebas recomendadas despues de mantenimiento
- Probar registro de usuario nuevo.
- Probar login con contrasena correcta e incorrecta.
- Probar verificacion OTP vencido y valido.
- Probar verificacion facial con rostro reconocido y no reconocido.
- Probar acceso al dashboard y carga de usuarios.

## Limpieza de datos
- Revisar usuarios pendientes vencidos.
- Revisar intentos duplicados.
- Revisar logs antiguos si la tabla crece demasiado.
- Archivar copias de auditoria cuando sea necesario.

## Mejoras sugeridas para futuras versiones
- Externalizar configuracion sensible.
- Agregar middleware de autenticacion para rutas protegidas.
- Registrar errores en un sistema centralizado.
- Implementar pruebas automatizadas.
- Agregar backup programado de base de datos.

