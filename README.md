# HAXX COORP - Sistema Web de Finanzas (Laravel + MySQL)

Sistema web responsive (móvil + escritorio) para control de ingresos, gastos, presupuestos y exportación PDF/CSV.

## Funcionalidades incluidas

- Autenticación principal por **usuario + contraseña**.
- Verificación de correo para cuentas locales.
- Inicio con **Google** (OAuth).
- Flujo seguro Google: si es primer ingreso, obliga a completar `nombre de usuario + contraseña`.
- Onboarding de 3 pantallas para usuario nuevo.
- Dashboard mensual con balance, ingresos, gastos y categorías.
- Módulo de movimientos con calendario nativo del navegador (`input type=date`).
- Módulo de presupuestos mensuales.
- Ajustes con exportación **PDF** y **CSV**.
- Branding HAXX COORP con iconos en UI y PDF.

## Requisitos

- PHP 8.2+
- Composer 2+
- Node 20+
- MySQL 8+

## Instalación local

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve
```

## Variables clave (.env)

```env
APP_NAME="HAXX COORP"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

DB_CONNECTION=mysql
DB_HOST=tu-host-mysql
DB_PORT=3306
DB_DATABASE=tu_db
DB_USERNAME=tu_user
DB_PASSWORD=tu_pass

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=soporte@tu-dominio.com
MAIL_PASSWORD=tu_password
MAIL_FROM_ADDRESS=soporte@tu-dominio.com
MAIL_FROM_NAME="HAXX COORP"

GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
GOOGLE_REDIRECT_URI=${APP_URL}/auth/google/callback
```

## Configuración Google OAuth

1. En Google Cloud Console crea credencial OAuth Web.
2. En **Authorized redirect URI** agrega:
   - `https://tu-dominio.com/auth/google/callback`
3. Copia `Client ID` y `Client Secret` al `.env`.

## Producción en Hostinger

1. Crea base de datos MySQL en Hostinger.
2. Sube proyecto (Git/FTP/SSH) incluyendo `vendor` y `public/build`.
3. Asegura que el **document root** apunte a la carpeta `public/`.
4. Configura `.env` con datos reales de dominio, MySQL y SMTP.
5. Ejecuta en servidor:
   - `php artisan migrate --force`
   - `php artisan config:cache`
   - `php artisan route:cache`
   - `php artisan view:cache`
6. Verifica permisos de escritura en `storage/` y `bootstrap/cache/`.

## Uso en iPhone (como app desde marcador)

1. Abre el sitio en Safari.
2. Compartir -> **Añadir a pantalla de inicio**.
3. Se abrirá como web-app full screen con diseño adaptado a móvil.

## Estructura principal

- `routes/web.php`: rutas del sistema.
- `app/Http/Controllers/AuthController.php`: login/registro/google/credenciales.
- `app/Http/Controllers/DashboardController.php`: resumen financiero.
- `app/Http/Controllers/TransactionController.php`: movimientos.
- `app/Http/Controllers/BudgetController.php`: presupuestos.
- `app/Http/Controllers/SettingsController.php`: exportaciones.
- `resources/views/`: vistas Blade.
- `resources/css/app.css`: diseño futurista responsive.

## Comandos útiles

```bash
php artisan route:list
php artisan test
npm run build
```
