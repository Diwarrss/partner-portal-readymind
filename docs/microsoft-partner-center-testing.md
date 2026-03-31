# Probar datos reales desde Microsoft (Partner Center)

El portal **no usa tu correo de Microsoft para llamar a la API**. La cadena es:

1. **Tenant (región)** en Admin → Tenants: credenciales de una **app registration** con permisos de **Partner Center** (integración CSP), en el entorno **sandbox** o producción que corresponda.
2. **Cliente (portal)** en Admin → Customers: campo **Microsoft Tenant ID** = el **ID de cliente en Partner Center** (GUID del cliente CSP cuyas suscripciones quieres leer). Suele coincidir con el **Tenant ID (Entra ID)** de la organización del cliente en el Partner Center.

Sin esos dos datos, `syncLicensesForCustomer` no puede obtener token ni listar suscripciones.

## Pasos recomendados (sandbox)

1. En [Partner Center](https://partner.microsoft.com/dashboard) usa el entorno **Integrator sandbox** y obtén:
   - Application (client) ID  
   - Directory (tenant) ID de la cuenta donde está registrada la app  
   - Client secret  
2. En **Admin → Tenants → Editar** (ej. `MX`), pega esos tres valores y marca **Sandbox** si lo usas solo como referencia.
3. Crea o edita tu usuario de portal (ej. `dialvaro@readymind.ms`) y en **Microsoft Tenant ID** pon el GUID del **cliente** en Partner Center sandbox que tenga suscripciones de prueba.
4. Inicia sesión en `/login` como cliente:
   - Email: `dialvaro@readymind.ms`
   - Contraseña: `password` (tras seed) o la que definas en Admin.
   - **Centro**: `MX` (o el código del tenant que configuraste).
5. Abre **Portal → Licencias** (`/portal/licenses`): ahí se ejecuta la sincronización contra Microsoft y luego redirige al dashboard.

Si algo falla, revisa `storage/logs/laravel.log` (auth Partner Center o `get subscriptions`).

## Credenciales de demo (seed)

Tras `php artisan db:seed` (o `migrate:fresh --seed`):

| Rol      | Email                 | Contraseña | Notas                          |
|----------|----------------------|------------|--------------------------------|
| Admin    | `admin@readymind.ms` | `password` | Panel `/admin`                 |
| Cliente  | `dialvaro@readymind.ms` | `password` | Centro `MX`                 |
| Cliente  | `cliente@readymind.ms`  | `password` | Centro `MX`                 |

Si ya tenías `admin@readymind.com` en la base, el seeder crea un usuario nuevo con `admin@readymind.ms`; puedes borrar el viejo o actualizar el email a mano.

## Tenants

Desde **Admin → Tenants** puedes **crear y editar** regiones (código único, nombre, credenciales Partner Center). Solo usuarios admin **sin** `tenant_id` en `users` pueden crear o eliminar tenants; los demás solo editan el suyo si aplica.
