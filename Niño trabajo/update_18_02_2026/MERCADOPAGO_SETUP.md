# Guía de Integración - Mercado Pago

## Configuración Requerida

### 1. Obtener Credenciales de Mercado Pago

1. Ir a: https://www.mercadopago.com/developers/panel
2. Crear una cuenta si no tienes una
3. Copiar tus credenciales:
   - **Access Token**: Se utiliza para comunicarse con la API de Mercado Pago desde el servidor
   - **Public Key**: Se utiliza en el frontend (no lo incluimos ahora, pero lo necesitarás después)

### 2. Actualizar Archivo de Configuración

Edita el archivo `php/config/mercadopago.php` y reemplaza:

```php
// Reemplaza esto:
define('MERCADOPAGO_ACCESS_TOKEN', 'YOUR_ACCESS_TOKEN_HERE');
define('MERCADOPAGO_PUBLIC_KEY', 'YOUR_PUBLIC_KEY_HERE');

// Con tus credenciales reales, por ejemplo:
define('MERCADOPAGO_ACCESS_TOKEN', 'APP_USR-1234567890...');
define('MERCADOPAGO_PUBLIC_KEY', 'APP_USR-12345...');
```

### 3. Configurar URLs de Retorno (Webhooks)

En el archivo `php/config/mercadopago.php`, actualiza la URL base:

```php
define('MERCADOPAGO_WEBHOOK_URL', 'https://tu-dominio-real.com/update_18_02_2026/php/webhooks/mercadopago.php');
```

Luego, en tu panel de Mercado Pago:
1. Ve a: Panel > Configuración > Integraciones > Webhooks
2. Añade la URL: `https://tu-dominio.com/update_18_02_2026/php/webhooks/mercadopago.php`
3. Selecciona los eventos:
   - `payment.created`
   - `payment.updated`
   - `charge.completed`

### 4. Configurar Dominio en URLs de Retorno

En el mismo archivo `mercadopago.php`, reemplaza 'tu-dominio.com' en:

```php
"back_urls" => array(
    "success" => "https://tu-dominio-real.com/update_18_02_2026/php/payment_success.php",
    "failure" => "https://tu-dominio-real.com/update_18_02_2026/php/payment_failure.php",
    "pending" => "https://tu-dominio-real.com/update_18_02_2026/php/payment_pending.php"
),
```

## Flujo de Pago Completo

1. **Usuario llena datos de reserva** → `habitaciones.html` o `habitaciones.php`
2. **Presiona "Confirmar Pago"** → Se abre modal de confirmación
3. **Presiona "Procesar Pago"** → Solicitud a `php/create_payment_preference.php`
4. **PHP crea preferencia de pago** → Mercado Pago genera URL de pago
5. **Usuario es redirigido a Mercado Pago** → Completa el pago
6. **Mercado Pago notifica el webhook** → `php/webhooks/mercadopago.php` actualiza BD
7. **Usuario es redirigido a página de éxito/fallo** → `payment_success.php` o `payment_failure.php`

## Base de Datos

El sistema crea automáticamente dos tablas si no existen:

### Tabla: `reservas`
```
- id: INT (PK)
- id_habitacion: VARCHAR
- fecha_entrada: DATE
- fecha_salida: DATE
- nombre_completo: VARCHAR
- adultos: INT
- ninos: INT
- email: VARCHAR
- estado: VARCHAR (pendiente|confirmada|cancelada)
- pago_confirmado: BOOLEAN
- id_mercadopago: VARCHAR (referencia externa)
- monto_pagado: DECIMAL
- fecha_pago: TIMESTAMP
- fecha_registro: TIMESTAMP
```

### Tabla: `pagos`
```
- id: INT (PK)
- id_reserva: INT (FK)
- id_mercadopago: VARCHAR (único)
- monto: DECIMAL
- moneda: VARCHAR (USD|ARS)
- estado: VARCHAR (pendiente|completado|rechazado|procesando)
- metodo_pago: VARCHAR
- referencia_externa: VARCHAR
- json_respuesta: LONGTEXT
- fecha_creacion: TIMESTAMP
- fecha_actualizacion: TIMESTAMP
```

## Archivos Creados

1. **php/config/mercadopago.php** - Configuración
2. **php/create_payment_preference.php** - Crea preferencia de pago
3. **php/webhooks/mercadopago.php** - Recibe notificaciones
4. **php/payment_success.php** - Página de éxito
5. **php/payment_failure.php** - Página de error
6. **php/payment_pending.php** - Página de pago pendiente

## Pruebas en Sandbox

Mercado Pago proporciona un entorno de prueba (sandbox) para probar pagos sin dinero real.

En el archivo `create_payment_preference.php`, el sistema automáticamente usa:
- `sandbox_init_point` en desarrollo
- `init_point` en producción

### Tarjetas de Prueba (Sandbox):
- **Visa**: 4111 1111 1111 1111
- **Mastercard**: 5555 5555 5555 4444
- **Fecha de vencimiento**: Cualquier fecha futura (ej: 12/25)
- **CVV**: Cualquier número de 3 dígitos (ej: 123)

## Montos Mínimos

El monto mínimo requerido para confirmación es **$20 USD** (configurable en `mercadopago.php`).

## Logs

Los webhooks registran eventos en:
`logs/mercadopago_webhooks.log`

Útil para debugging si algo no funciona correctamente.

## Soporte

Para problemas con la integración:
1. Revisa `logs/mercadopago_webhooks.log`
2. Verifica que el Access Token esté correcto
3. Asegúrate que las URLs de webhook estén configuradas en el panel
4. Consulta documentación: https://www.mercadopago.com/developers/es/docs

