

--- Registro:

Abre: `http://localhost/http%2023/cuenta.html`


Abre: `http://localhost/http%2023/login.html`





------Estructura Base de Datos------

Tu tabla `usuarios_alborada` está configurada así:

```sql
CREATE TABLE `usuarios_alborada` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Correo` varchar(100) NOT NULL UNIQUE,
  `Contraseña` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```



------Prueba de Conexión------

Para verificar que todo está funcionando:

Abre: `http://localhost/http%2023/test_conexion.php`
