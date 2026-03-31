# ReadyMind Partner Portal

Aplicación web para **ReadyMind**: landing pública, **panel de administración** (tenants, clientes, contacto, licencias, integración **Microsoft Partner Center**) y **portal de clientes** (licencias, solicitudes de productos). Incluye **API** con tokens (Sanctum) para clientes.

| Área | Ruta | Notas |
|------|------|--------|
| Pública | `/` | Landing |
| Login | `/login` | Admin (`users`) o cliente (`customers` + código de **centro/tenant**) |
| Admin | `/admin` | Tenants, clientes, contacto, exportación, perfil |
| Clientes | `/portal/*` | Tras login de cliente |
| API | Ver `docs/partner-center-api.md` | v1: tokens, suscripciones, contacto |

Documentación detallada (estado del proyecto, stack, pasos largos): **[`docs/RESUMEN-PROYECTO.md`](docs/RESUMEN-PROYECTO.md)**.

---

## Requisitos

- **PHP** ≥ 8.3 (extensiones típicas de Laravel)
- **Composer** 2.x
- **Node.js** — recomendado: versión en [`.nvmrc`](.nvmrc) (`nvm use`); suele funcionar Node 20 LTS
- **MySQL** (u otro motor compatible configurado en `.env`)

---

## Puesta en marcha

```bash
cp .env.example .env
composer install
php artisan key:generate
# Editar .env: APP_URL, DB_*, MAIL_*, etc.

php artisan migrate
php artisan db:seed   # opcional: datos de demo

npm install
npm run build         # producción / primera vez
php artisan serve     # http://127.0.0.1:8000
```

**Desarrollo frontend en caliente:** en otra terminal, `npm run dev` y usa el mismo `APP_URL` que indique Vite/Laravel.

**Colas / correo (opcional):** `php artisan queue:work`. Con `MAIL_MAILER=log` los envíos van al log.

---

## Cuentas de demo (tras `db:seed`)

| Rol | Email | Contraseña | Notas |
|-----|--------|------------|--------|
| Admin | `admin@readymind.ms` | `password` | → `/admin` |
| Cliente | `cliente@readymind.ms` o `dialvaro@readymind.ms` | `password` | Indicar centro **MX** (u otro tenant sembrado) → `/portal` |

---

## Stack (resumen)

- **Backend:** Laravel 12, Inertia, Spatie Permission, Sanctum, Ziggy  
- **Frontend:** Vue 3, Vite, Tailwind, reka-ui / radix-vue  
- **Datos:** Eloquent, migraciones y seeders en `database/`

Servicios relevantes: `app/Services/` (p. ej. Partner Center, licencias).

---

## Más documentación

| Documento | Contenido |
|-----------|-----------|
| [`docs/RESUMEN-PROYECTO.md`](docs/RESUMEN-PROYECTO.md) | Resumen amplio, estructura del repo, guía paso a paso |
| [`docs/partner-center-api.md`](docs/partner-center-api.md) | Contrato API v1 |
| [`docs/microsoft-partner-center-testing.md`](docs/microsoft-partner-center-testing.md) | Pruebas y credenciales Microsoft / sandbox |

---

## Licencia

MIT (framework Laravel y convención del proyecto).
