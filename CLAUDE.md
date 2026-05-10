# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Stack

- **Laravel 11** with **Livewire 3** for reactive UI without a JS framework
- **Laravel Breeze** (Blade + Tailwind) for authentication scaffolding
- **MySQL 8.4** via Docker (**Laravel Sail**)
- **barryvdh/laravel-dompdf** for PDF report generation
- **Vite** for asset bundling

## Commands

All commands must be run through Sail (Docker). There is no local PHP/MySQL setup.

```bash
# Start containers
./vendor/bin/sail up -d

# Compile assets (keep running during development)
./vendor/bin/sail npm run dev

# Migrate and seed initial data
./vendor/bin/sail artisan migrate --seed

# Reset database completely
./vendor/bin/sail artisan migrate:fresh --seed

# Link storage (required for uploaded receipts to be accessible)
./vendor/bin/sail artisan storage:link

# Run all tests
./vendor/bin/sail artisan test

# Run a single test file
./vendor/bin/sail artisan test tests/Feature/ExampleTest.php

# Run tests by filter
./vendor/bin/sail artisan test --filter=SomeTestName

# Code style (Laravel Pint)
./vendor/bin/sail composer exec pint

# Tinker REPL
./vendor/bin/sail artisan tinker
```

App runs at `http://localhost`. Vite dev server proxies through Sail on port 5173.

## Architecture

### Domain Models

Four domain models form the core of the system:

- **`Aportante`** — a contributor (person who puts money in). Has `nombre`, `activo`, `nota`. Linked to both `ingresos` and `gastos` (every expense is charged to a contributor's balance).
- **`Ingreso`** — a cash-in entry. Belongs to one `Aportante`. Fields: `fecha`, `monto`, `metodo_ingreso` (EFECTIVO|QR), `referencia`, `nota`, `comprobante_path`.
- **`Gasto`** — a cash-out entry. Belongs to one `Aportante` (whose balance is debited) and one `CategoriaGasto`. Fields: `fecha`, `monto`, `metodo_pago` (EFECTIVO|QR), `descripcion`, `proveedor`, `referencia`, `comprobante_path`.
- **`CategoriaGasto`** — expense category (table `categorias_gasto`). Has `nombre`, `descripcion`, `activo`.

**Balance calculation:** `saldo = SUM(ingresos.monto) - SUM(gastos.monto)`, computed on-the-fly per aportante or globally — there is no stored balance column.

### Livewire Components

All business logic lives in `app/Livewire/CajaChica/`. Each component handles a full CRUD view without traditional controllers:

| Component | Route | Responsibility |
|-----------|-------|----------------|
| `Dashboard` | `/dashboard` | Summary stats, recent movements, monthly spend by category |
| `Ingresos` | `/ingresos` | CRUD for income entries, file upload, filtering/pagination |
| `Gastos` | `/gastos` | CRUD for expense entries, file upload, filtering/pagination |
| `Categorias` | `/categorias` | CRUD for expense categories |
| `Aportantes` | `/aportantes` | CRUD for contributors |
| `Reportes` | `/reportes` | Date-range PDF report generation |

Each component renders using `->layout('layouts.app', ['header' => ...])`. All routes require `auth` middleware.

### File Uploads

Receipts (`comprobante_path`) are stored in the `public` disk under `comprobantes/ingresos/` and `comprobantes/gastos/`. `storage:link` must be run for them to be web-accessible. On record update, the old file is deleted before storing the new one.

### PDF Reports (`Reportes` component)

`Reportes::generarPdf()` builds and streams a PDF using dompdf. The Blade template is at `resources/views/reports/caja-chica.blade.php`. Rate-limited to 10 attempts per 10 minutes per user/IP using `RateLimiter` directly inside the component (not middleware).

### Rate Limiting

Defined in `AppServiceProvider::boot()`:
- Guests: 120 req/min by IP
- Authenticated users: 600 req/min by user ID and by IP
- PDF generation: 10 req/10 min (handled inside `Reportes` component)

For better cache/rate-limit performance, set `CACHE_STORE=redis` in `.env`.

### Seed Data

Running `migrate --seed` creates:
- **Aportantes:** `Reina Marino Marca`, `Fermin Apolaca Marca`
- **Categorías:** Internet, Luz, Pasajes

The Dashboard component hardcodes lookups for the two seeded aportante names to display individual balances.

## Testing

Tests use an in-memory SQLite database (`DB_DATABASE=testing` as configured in `phpunit.xml`). The MySQL container is not used during tests.
