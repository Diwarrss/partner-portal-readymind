# ReadyMind Partner Portal — Resumen del proyecto

Documento de referencia: qué es la aplicación, en qué estado está, stack técnico y cómo ponerla en marcha **paso a paso**.

---

## 1. Qué es y para qué sirve

**ReadyMind Partner Portal** es una aplicación web para:

| Ámbito | Descripción |
|--------|-------------|
| **Landing pública** | Página de inicio en `/` con identidad ReadyMind y enlaces a acceso. |
| **Admin** | Panel en `/admin` para administradores: tenants (regiones/centros), clientes, solicitudes de contacto, exportación de licencias, perfil. Integración con credenciales **Microsoft Partner Center** por tenant. |
| **Portal de clientes** | Área en `/portal/*` para clientes autenticados: licencias sincronizadas desde Partner Center, ajuste de cantidades cuando aplica, formulario para pedir productos nuevos. |
| **API** | Endpoints documentados en `docs/partner-center-api.md` (tokens de cliente, suscripciones, contacto). |

**Login unificado** en `/login`: primero intenta usuario **admin** (tabla `users`, guard `web`), luego **cliente** (tabla `customers`, guard `customer`). Los clientes deben indicar el **código de centro (tenant)** en el formulario.

---

## 2. En qué quedamos (estado actual)

- **UI**: Landing, login, registro, recuperación de contraseña, portal de clientes y admin alineados visualmente (indigo/violeta, componentes tipo shadcn/reka-ui).
- **Admin**: CRUD de **tenants** (crear/editar/eliminar si no hay clientes), gestión de **clientes**, listado de solicitudes de contacto, perfil de admin en `/admin/profile`.
- **Correos**: plantillas HTML con layout común (`resources/views/emails/layout.blade.php`) para credenciales de cliente y notificación de formulario de contacto.
- **Dominio de ejemplo en seeders**: `@readymind.ms` (admin y clientes de demo).
- **Documentación Partner Center**: `docs/microsoft-partner-center-testing.md` (cómo enlazar Microsoft y probar sincronización de licencias).

**Pendiente / depende de entorno real**

- Credenciales **Partner Center** (client id, secret, tenant id de Azure) por región en cada **Tenant** en base de datos.
- **Microsoft Tenant ID** del cliente CSP en cada **Customer** para que la sincronización de licencias devuelva datos.

---

## 3. Tecnologías

### Backend

| Tecnología | Uso |
|------------|-----|
| **PHP** | `^8.3` según `composer.json` (compatible con 8.3 y superiores, p. ej. 8.4 en local). |
| **Laravel** | `^12` — rutas, Eloquent, auth, colas, mail, etc. |
| **Inertia.js (Laravel)** | `inertiajs/inertia-laravel` — respuestas híbridas sin API REST obligatoria para el SPA. |
| **Spatie Permission** | Roles y permisos (`admin`, permisos de tenants/customers/product-requests). |
| **Laravel Sanctum** | Tokens API para clientes (`docs/partner-center-api.md`). |
| **Ziggy** | Rutas Laravel expuestas al frontend (`route()` en Vue). |

### Frontend

| Tecnología | Uso |
|------------|-----|
| **Vue 3** | Componentes y páginas en `resources/js/pages/`. |
| **Vite** | Build y HMR (`vite.config.js`, entrada `resources/js/app.js`). |
| **Tailwind CSS** | Estilos (v3/v4 según paquetes del proyecto). |
| **reka-ui / radix-vue** | Primitivos para diálogos, menús, etc. |
| **lucide-vue-next** | Iconos. |
| **TypeScript** | En parte del código (páginas admin/portal modernas). |

### Base de datos y otros

- **MySQL** (configurable vía `.env`; el proyecto usa convención Laravel).
- **Sesiones** en base de datos (`SESSION_DRIVER=database` típico).
- **Cola** y **caché** pueden usar `database` según `.env`.

---

## 4. Versiones recomendadas (referencia)

| Entorno | Versión |
|---------|---------|
| **PHP** | **8.3 o superior** (`composer.json`: `"php": "^8.3"`). |
| **Node.js** | Definido en **`.nvmrc`**: **`v22.22.0`** (recomendado: `nvm use` o instalar esa versión). También suele funcionar **Node 20 LTS**; tras clonar, ejecuta `npm install` y `npm run build` para comprobar. |
| **Composer** | 2.x actual. |

> **Ejemplo en un entorno de desarrollo:** PHP **8.4.x** (CLI) y Node **v20.x** o **v22.x** pueden coexistir con el proyecto siempre que Composer y npm no marquen error. El límite inferior oficial de PHP lo marca `composer.json` (`^8.3`).

---

## 5. Cómo ejecutar el proyecto (paso a paso)

### Paso 1 — Requisitos

1. PHP **≥ 8.3** con extensiones habituales de Laravel (`openssl`, `pdo`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, etc.).
2. **Composer** instalado.
3. **Node.js** (idealmente la versión del `.nvmrc`) y **npm**.
4. **MySQL** (o motor compatible) creado y usuario con permisos.

### Paso 2 — Código y dependencias PHP

```bash
cd /ruta/al/readymind-portal
cp .env.example .env
composer install
php artisan key:generate
```

Edita **`.env`**: `APP_URL`, `DB_*`, `MAIL_*`, etc.

### Paso 3 — Base de datos

```bash
php artisan migrate
php artisan db:seed
```

Seeders principales:

- **Tenants** (MX, USA, PR, sandbox marcado).
- **Clientes** de ejemplo (`cliente@readymind.ms`, `dialvaro@readymind.ms`, contraseña `password`, tenant MX).
- **Admin** `admin@readymind.ms` / `password` con rol `admin`.

### Paso 4 — Dependencias frontend y assets

```bash
npm install
npm run build
```

En desarrollo, en otra terminal:

```bash
npm run dev
```

### Paso 5 — Servidor de aplicación

```bash
php artisan serve
```

Por defecto: **http://127.0.0.1:8000**

Abre el navegador con **Vite en marcha** (`npm run dev`) si trabajas en caliente; si solo usas `npm run build`, basta con `php artisan serve` y los assets en `public/build`.

### Paso 6 — Primer acceso

1. **http://localhost:8000/** — landing.
2. **http://localhost:8000/login** — inicio de sesión.
3. Admin: `admin@readymind.ms` / `password` → redirección a **`/admin`**.
4. Cliente: correo de seed + `password` + **centro** (ej. `MX`) → **`/portal/dashboard`**.

### Paso 7 — Colas y correo (opcional)

Si envías mails o usas colas:

```bash
php artisan queue:work
```

Con `MAIL_MAILER=log` los correos se registran en log en lugar de salir por SMTP.

---

## 6. Estructura útil del repositorio

```
app/Http/Controllers/   # Admin, Auth, Portal, API, Profile
app/Models/             # User, Customer, Tenant, LicenseSubscription, etc.
app/Services/           # LicenseService, PartnerCenterClient
database/migrations/
database/seeders/
docs/                   # Este resumen, Partner Center, API
resources/js/pages/     # Vue + Inertia (Welcome, Auth, Admin, Portal)
resources/js/layouts/   # AdminLayout, PortalLayout, GuestLayout
resources/views/emails/ # Plantillas Blade
routes/web.php
routes/auth.php
```

---

## 7. Documentación adicional

| Archivo | Contenido |
|---------|-----------|
| `docs/microsoft-partner-center-testing.md` | Pruebas con Microsoft / sandbox y credenciales. |
| `docs/partner-center-api.md` | Contrato API v1. |

---

*Última actualización del documento: coherente con el estado del repositorio ReadyMind Portal.*
