# 📱 Integración Mercado Pago - Resumen Completo

## ✅ Lo que se ha implementado

### 1. **Configuración de Mercado Pago**
   - Archivo: `php/config/mercadopago.php`
   - Define constantes para Access Token, Public Key, URLs de retorno
   - Configura monto mínimo de $20 USD
   - Soporte para webhooks (IPN)

### 2. **Creación de Preferencias de Pago**
   - Archivo: `php/create_payment_preference.php`
   - Recibe datos de reserva desde frontend
   - Comunica con API de Mercado Pago
   - Crea registro en BD con estado "pendiente"
   - Retorna URL de pago a Mercado Pago

### 3. **Webhook para Notificaciones**
   - Archivo: `php/webhooks/mercadopago.php`
   - Recibe notificaciones de Mercado Pago sobre estado de pagos
   - Actualiza tablas `reservas` y `pagos` automáticamente
   - Confirma la reserva cuando pago es aprobado
   - Registra eventos en: `logs/mercadopago_webhooks.log`

### 4. **Páginas de Retorno**
   - `php/payment_success.php` - Muestra reserva confirmada
   - `php/payment_failure.php` - Muestra error y opción de reintentar
   - `php/payment_pending.php` - Muestra estado pendiente (redirige auto a inicio)

### 5. **Frontend - Integración en Habitaciones**
   - Modificado: `habitaciones.html`
   - El formulario de pago ahora:
     ✓ Recolecta datos de la reserva
     ✓ Envía a `create_payment_preference.php`
     ✓ Redirige a Mercado Pago automáticamente
     ✓ Retorna con confirmación de pago

### 6. **Base de Datos**
   - Tabla `reservas`: Almacena datos de reservas con estado de pago
   - Tabla `pagos`: Historial detallado de transacciones
   - Ambas se crean automáticamente si no existen

### 7. **Verificación**
   - Archivo: `php/verify_mercadopago.php`
   - Verifica configuración y conectividad
   - Acceso: `http://localhost/update_18_02_2026/php/verify_mercadopago.php`

---

## 🔧 Pasos para Configurar

### Paso 1: Obtener Credenciales
1. Ir a: https://www.mercadopago.com/developers/panel
2. Crear cuenta o iniciar sesión
3. Copiar `Access Token`

### Paso 2: Actualizar Archivo de Config
Editar `php/config/mercadopago.php`:

```php
define('MERCADOPAGO_ACCESS_TOKEN', 'APP_USR-xxxx...');  // ← Pega tu token aquí
define('MERCADOPAGO_PUBLIC_KEY', 'APP_USR-yyyy...');     // ← Y aquí
```

### Paso 3: Configurar Dominio
En el mismo archivo, actualizar URLs reales (si no estás en localhost):

```php
"success" => "https://tudominio.com/update_18_02_2026/php/payment_success.php",
"failure" => "https://tudominio.com/update_18_02_2026/php/payment_failure.php",
"pending" => "https://tudominio.com/update_18_02_2026/php/payment_pending.php",
```

### Paso 4: Configurar Webhook en Panel de Mercado Pago
1. Panel → Configuración → Integraciones → Webhooks
2. Añadir URL: `https://tudominio.com/update_18_02_2026/php/webhooks/mercadopago.php`
3. Seleccionar eventos: `payment.created`, `payment.updated`

### Paso 5: Verificar Instalación
```
http://localhost/update_18_02_2026/php/verify_mercadopago.php
```

---

## 🔄 Flujo Completo de Pago

```
┌─────────────────────────────────────────────────────────────┐
│ 1. Usuario selecciona habitación en habitaciones.html        │
│    → Llena: fecha entrada, salida, nombre, email, etc       │
└──────────────┬──────────────────────────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────────────────────────┐
│ 2. Modal de confirmación se abre                            │
│    → Muestra detalles de reserva y total a pagar            │
│    → Mínimo requerido: $20 USD                              │
└──────────────┬──────────────────────────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────────────────────────┐
│ 3. Usuario presiona "Procesar Pago"                         │
│    → FormData se envía a create_payment_preference.php      │
└──────────────┬──────────────────────────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────────────────────────┐
│ 4. PHP crea preferencia de pago                             │
│    → API Call a: https://api.mercadopago.com/checkout/...  │
│    → Mercado Pago genera ID y URL de pago                   │
│    → Se guardan datos en tabla 'reservas' (estado: pendiente)│
└──────────────┬──────────────────────────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────────────────────────┐
│ 5. Usuario es redirigido a Mercado Pago                     │
│    → Ingresa datos de tarjeta/billetera                     │
│    → Completa el pago                                        │
└──────────────┬──────────────────────────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────────────────────────┐
│ 6. Mercado Pago notifica webhook                            │
│    → POST a: /php/webhooks/mercadopago.php                  │
│    → Con: topic=payment, id=<payment_id>                    │
└──────────────┬──────────────────────────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────────────────────────┐
│ 7. Webhook actualiza BD                                     │
│    ✓ Si approved:   reservas.estado='confirmada',           │
│                     reservas.pago_confirmado=1              │
│    ✗ Si rejected:   reservas.estado='cancelada',            │
│                     reservas.pago_confirmado=0              │
└──────────────┬──────────────────────────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────────────────────────┐
│ 8. Usuario vuelve (auto-redireccionado)                     │
│    ✓ Exitoso:   payment_success.php (muestra detalles)      │
│    ✗ Fallido:   payment_failure.php (opción reintentar)     │
└─────────────────────────────────────────────────────────────┘
```

---

## 🧪 Pruebas

### En Ambiente de Sandbox (sin dinero real):

**Tarjetas de Prueba:**
- Visa: `4111 1111 1111 1111`
- Mastercard: `5555 5555 5555 4444`
- Exp: Cualquier fecha futura (ej: 12/25)
- CVV: Cualquier 3 dígitos (ej: 123)

**Para que pague:**
1. Usar tarjeta de arriba
2. Ingresar email cualquiera
3. Presionar "Procesar Pago"
4. En Mercado Pago, presionar "Pagar"
5. Escribir CVV (cualquiera) y presionar "Continuar"
6. Volver a sitio con confirmación

**Ver eventos en logs:**
```
cat logs/mercadopago_webhooks.log
```

---

## 📊 Tablas de Base de Datos

### Tabla: reservas
```sql
SELECT * FROM reservas;
```

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT | ID único de reserva |
| id_habitacion | VARCHAR | ID de la habitación |
| fecha_entrada | DATE | Check-in |
| fecha_salida | DATE | Check-out |
| nombre_completo | VARCHAR | Nombre del usuario |
| adultos | INT | Cantidad de adultos |
| ninos | INT | Cantidad de niños |
| email | VARCHAR | Email de contacto |
| **estado** | VARCHAR | `pendiente` → `confirmada` → `cancelada` |
| pago_confirmado | BOOLEAN | 1 = pagado, 0 = no pagado |
| id_mercadopago | VARCHAR | ID externo de Mercado Pago |
| monto_pagado | DECIMAL | Cantidad pagada |
| fecha_pago | TIMESTAMP | Cuándo se pagó |

### Tabla: pagos
```sql
SELECT * FROM pagos;
```

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT | ID único de pago |
| id_reserva | INT | FK a reservas.id |
| id_mercadopago | VARCHAR | ID de transacción MP |
| monto | DECIMAL | Monto en USD |
| moneda | VARCHAR | Moneda (USD/ARS) |
| **estado** | VARCHAR | `pendiente` → `completado` → `rechazado` |
| json_respuesta | LONGTEXT | Respuesta completa de MP |

---

## 🐛 Debugging

Si algo no funciona:

### 1. Verificar Token
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
  https://api.mercadopago.com/v1/payments/search
```

### 2. Ver logs del webhook
```bash
tail -f logs/mercadopago_webhooks.log
```

### 3. Verificar BD
```sql
SELECT * FROM reservas ORDER BY fecha_registro DESC LIMIT 1;
SELECT * FROM pagos ORDER BY fecha_creacion DESC LIMIT 1;
```

### 4. Panel de Mercado Pago
- Ve a: Panel → Transacciones
- Busca por: ID de preferencia o email del usuario
- Ahí verás estado del pago

---

## 💾 Archivos Creados

```
update_18_02_2026/
├── php/
│   ├── config/
│   │   ├── mercadopago.php           ← EDITAR: Poner credenciales
│   ├── create_payment_preference.php  ← Crear preferencia
│   ├── payment_success.php            ← Página de éxito
│   ├── payment_failure.php            ← Página de error
│   ├── payment_pending.php            ← Página de pendiente
│   ├── verify_mercadopago.php         ← Verificar setup
│   └── webhooks/
│       └── mercadopago.php            ← Recibir notificaciones
├── logs/
│   └── mercadopago_webhooks.log       ← Eventos (creado automático)
├── habitaciones.html                  ← MODIFICADO: Integración MP
└── MERCADOPAGO_SETUP.md               ← Esta guía
```

---

## ✨ Características

✅ Pago mínimo de $20 USD requerido
✅ Integración en tiempo real con Mercado Pago
✅ Webhooks para confirmación automática
✅ Logging de eventos
✅ Manejo de errores robusto
✅ BD actualiza automáticamente
✅ Soporte para sandbox testing
✅ URLs dinámicas (localhost o producción)

---

¡Listo! El sistema está completamente implementado. Solo necesitas configurar tus credenciales de Mercado Pago. 🎉

