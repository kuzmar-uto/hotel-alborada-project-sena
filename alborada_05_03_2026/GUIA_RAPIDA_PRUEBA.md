# 🧪 GUÍA RÁPIDA DE PRUEBA

## ⚡ Setup en 5 minutos

### Paso 1: Credenciales (1 min)
Edita `php/config/mercadopago.php`:
```php
define('MERCADOPAGO_ACCESS_TOKEN', 'APP_USR-123456789'); // Tu token
define('MERCADOPAGO_PUBLIC_KEY', 'APP_USR-abc123');      // Tu public key
```

Obtén tus credenciales en: https://www.mercadopago.com/developers/panel

### Paso 2: Verificar la Integración (1 min)
```
Abre en navegador:
http://localhost/update_18_02_2026/php/verify_mercadopago.php
```

Deberías ver:
```
✓ Access Token configurado
✓ Conexión con Mercado Pago exitosa (HTTP 400)
✓ Tabla 'reservas' lista
✓ Tabla 'pagos' lista
✓ Permisos de escritura en logs OK
```

### Paso 3: Prueba el Formulario (2 min)
1. Ve a: http://localhost/update_18_02_2026/habitaciones.php
2. Haz clic en **"Reservar ahora"** de cualquier habitación
3. Completa el formulario:
   - Fecha entrada: (hoy)
   - Fecha salida: (mañana)
   - Nombre: "Test Usuario"
   - Adultos: 1
   - Email: "test@example.com"
4. Haz clic en **"Ir a pago"**

### Paso 4: Verifica la Redirección (1 min)
- Deberías ser redirigido a MercadoPago Checkout
- Si usas credenciales Sandbox, irá a Sandbox
- Si usas credenciales reales, irá a Producción

---

## 🎯 CHECKLIST DE VERIFICACIÓN

### Colores Unificados
- [ ] Botón "Reservar ahora" es azul #3d8da5
- [ ] Botón "Ir a pago" es azul #3d8da5
- [ ] Hover de botones oscurece a #2d6f82
- [ ] Badge de tipo de habitación es azul #3d8da5

### Mensaje de Adelanto
- [ ] Modal muestra mensaje "Para confirmar tu reserva"
- [ ] El monto aparece en color azul oscuro

### Flujo de Pago
- [ ] Formulario se valida (campos requeridos)
- [ ] Botón muestra "Procesando..."
- [ ] Se redirige a MercadoPago
- [ ] Aparece preferencia de pago

---

## 🧩 ESTRUCTURA FINAL

```
update_18_02_2026/
├── habitaciones.php ........................ Página con colores unificados + formulario MercadoPago
├── php/
│   ├── create_payment_preference.php ...... Crea preferencia en MercadoPago
│   ├── payment_success.php ............... Página post-pago exitoso
│   ├── payment_failure.php ............... Página post-pago fallido  
│   ├── payment_pending.php ............... Página post-pago pendiente
│   ├── config/
│   │   └── mercadopago.php ............... ⭐ EDITAR CON TUS CREDENCIALES
│   └── webhooks/
│       └── mercadopago.php ............... (Opcional) Webhooks
├── php/verify_mercadopago.php ........... 🔍 Verificador de integración
├── CONFIGURACION_FINAL.md ................. Instrucciones completas
└── RESUMEN_CAMBIOS.md .................... Resumen técnico
```

---

## 📊 DATOS DE PRUEBA (SANDBOX)

Para probar sin dinero real:

**Tarjeta de Débito/Crédito (aprobada):**
```
Número:    4111 1111 1111 1111
Vencimiento: 11/25
CVV:       123
```

**Tarjeta rechazada (para probar error):**
```
Número:    4000 0000 0000 0002
Vencimiento: 11/25
CVV:       123
```

---

## 🐛 SI ALGO NO FUNCIONA

### Error: "Faltan campos requeridos"
```javascript
// Abre consola (F12) y busca errores
// Verifica que todos los inputs tengan valores
```

### Error: "Error en la respuesta de Mercado Pago"
```
Solución:
1. Verifica que el Access Token sea correcto
2. Asegúrate de que sea del mismo región (AR o US)
3. Revisa logs de PHP en XAMPP
```

### No se redirige a MercadoPago
```
Solución:
1. Abre consola (F12)
2. Busca algún error en Network
3. Verifica que `create_payment_preference.php` retorne init_point/sandbox_init_point
```

### Base de datos con errores
```bash
# Verifica que MySQL está corriendo
# En XAMPP: Start Apache y MySQL
# Las tablas se crean automáticamente en la primera llamada
```

---

## 💡 TIPS

1. **Desarrollo**: Usa credenciales de Sandbox
2. **Producción**: Reemplaza con credenciales reales
3. **Testing**: Todos los datos de formulario son guardados en la tabla `reservas`
4. **Confirmación**: Los pagos aprobados se guardan en tabla `pagos`

---

## ✨ RESULTADO FINAL

Cuando todo esté funcionando:

1. ✅ Usuario entra a habitaciones.php
2. ✅ Selecciona habitación
3. ✅ Ve modal con mensaje de adelanto
4. ✅ Completa formulario de reserva
5. ✅ Haz clic "Ir a pago"
6. ✅ Se redirige a MercadoPago
7. ✅ Realiza pago (o prueba con tarjetas de prueba)
8. ✅ Redirige a success/failure/pending según resultado
9. ✅ Se guardan registros en BD

**¡Listo!** 🚀
