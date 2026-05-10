# Caja Chica (Laravel 11 + Sail + Livewire 3)

Sistema tipo **Caja Chica** con:

- Autenticación: **Laravel Breeze (Blade + Tailwind)**
- UI reactiva sin recargar: **Livewire 3**
- Base de datos: **MySQL** (Docker vía **Laravel Sail**)
- Reportes PDF: **barryvdh/laravel-dompdf**

Incluye control de:

- Ingresos (aportes) por aportante
- Gastos (egresos) asociados siempre a un aportante (de dónde sale el dinero)
- Saldos calculados por movimientos (total y por aportante)
- Reporte PDF por rango de fechas

## Datos iniciales (seed)

Al ejecutar `migrate --seed` se crean automáticamente:

- Aportantes:
	- `Reina Marino Marca`
	- `Fermin Apolaca Marca`
- Categorías:
	- Internet
	- Luz
	- Pasajes

## Comandos (todo con Sail)

### Levantar contenedores

```bash
./vendor/bin/sail up -d
```

### Instalar dependencias

```bash
./vendor/bin/sail composer install
./vendor/bin/sail npm install
```

### Compilar assets (Vite)

```bash
./vendor/bin/sail npm run dev
```

### Migrar y sembrar datos

```bash
./vendor/bin/sail artisan migrate --seed
```

Si necesitas reiniciar la base de datos por completo:

```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

### Storage (comprobantes)

Para que los archivos subidos (comprobantes) sean accesibles desde el navegador:

```bash
./vendor/bin/sail artisan storage:link
```

## Crear usuario y entrar al sistema

Ruta principal:

- Ir a `http://localhost/`
	- Si NO estás autenticado: redirige a `login`
	- Si YA estás autenticado (por ejemplo, marcaste **Remember me** al iniciar sesión): redirige a `dashboard`

1) Con Sail arriba (`sail up -d`) y Vite corriendo (`sail npm run dev`).

2) Crear usuario desde el navegador:

- Ir a `http://localhost/register`

3) Iniciar sesión:

- Ir a `http://localhost/login`

Una vez dentro verás el menú: Dashboard, Ingresos, Gastos, Categorías, Aportantes, Reportes.

## Seguridad básica anti‑DDoS (rate limiting)

La app aplica **rate limiting** a todas las solicitudes del grupo `web` (incluye endpoints internos de Livewire) usando el limiter `web`.

- Límite invitados (por IP): 120 req/min
- Límite autenticados (por usuario y por IP): 600 req/min
- Generación de PDF: 10 intentos / 10 min por usuario/IP

Para mejor rendimiento del rate limiting (y cache en general), usa Redis en tu `.env`:

```env
CACHE_STORE=redis
```
