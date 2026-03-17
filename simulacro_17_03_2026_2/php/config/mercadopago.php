<?php
// Configuración de Mercado Pago
// IMPORTANTE: Reemplaza estos valores con tus credenciales reales

// Access Token de Mercado Pago (obtenlo de: https://www.mercadopago.com/developers/panel)
define('MERCADOPAGO_ACCESS_TOKEN', 'APP_USR-4791949607949113-030408-9f8496748c47fa9a3cf3f78ee5cbd6db-3243325746');

// Public Key de Mercado Pago (necesaria para el frontend)
define('MERCADOPAGO_PUBLIC_KEY', 'YOUR_PUBLIC_KEY_HERE');

// URL base de Mercado Pago API
define('MERCADOPAGO_API_URL', 'https://api.mercadopago.com');

// Monto mínimo requerido para confirmar reserva (en USD)
define('MINIMUM_PAYMENT_AMOUNT', 15);

// Moneda (USD o ARS, según tu preferencia)
define('PAYMENT_CURRENCY', 'USD');

// Descripción del pago que verá el usuario
define('PAYMENT_DESCRIPTION', 'Depósito inicial - Alborada Hotel');

// Notificaciones webhooks (IPN)
define('MERCADOPAGO_WEBHOOK_URL', 'https://tu-dominio.com/php/webhooks/mercadopago.php');

?>
