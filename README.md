# Booking Prototype API

A Symfony 7 REST prototype for a short-term vehicle rental flow. It exposes a thin slice of the future rental platform so that product and operations teams can validate availability checks, reservation capture, and manual status management before committing to a full build.

## Vision & Scope

| Stage | Focus | Scope |
| --- | --- | --- |
| **Prototype (this repo)** | Validate critical booking happy-paths | \- Availability search that merges live reservations with fixed subscription blocks.<br>\- Reservation capture with lead-time, date-order, and length validation plus overlap guards.<br>\- Manual admin confirmation/decline flows for newly created reservations.<br>\- Occupancy reporting for vehicle calendars. |
| **MVP** | Ship end-to-end self-service rentals | \- Authentication for customers and internal users.<br>\- Pricing, quotes, and payment capture.<br>\- Automated confirmation rules, cancellation support, and customer notifications.<br>\- Inventory management for fleets and subscription imports. |
| **Later Iterations** | Optimize operations & growth | \- Dynamic pricing and yield management.<br>\- Partner integrations (insurance, identity, telematics).<br>\- Advanced analytics, loyalty, and upsell flows.<br>\- Self-service modifications and extensions. |

## Architecture Overview

- **Framework:** Symfony 7 with attribute-based routing and DTO-driven request mapping.
- **Persistence:** MySQL 8 accessed via Doctrine DBAL table gateways; a single `reservation` table backs the flow.
- **Documentation:** NelmioApiDoc generates OpenAPI docs for `/v1` endpoints.
- **Containers:** `docker-compose.yaml` starts MySQL, PHP-FPM, and Nginx services for local development.

## API Surface

All routes are prefixed with `/v1`.

| Endpoint | Method | Purpose |
| --- | --- | --- |
| `/v1/availability` | `POST` | Check whether a vehicle is free for a requested window (validates input, normalizes to UTC, blocks overlaps). |
| `/v1/reservations` | `POST` | Create a reservation if validation passes and no overlap exists; returns reservation metadata. |
| `/v1/vehicles/{vehicleId}/occupied-ranges` | `GET` | Merge reservations and long-term subscription holds into calendar ranges for the vehicle. |
| `/v1/reservations/{id}/confirm` | `PATCH` | Mark a pending reservation as confirmed, enforcing allowed status transitions. |
| `/v1/reservations/{id}/decline` | `PATCH` | Decline a pending reservation with the same transition guardrails as confirm. |

Visit `/api/doc` (default Nelmio route) once the stack is running to explore schema details.

## Local Development

### Prerequisites

- Docker Desktop (or Docker Engine) with Compose V2.
- Make sure TCP ports `3306` and `8080` are available.

### One-time setup

1. Copy the environment template and switch the database to the Compose MySQL service:
   ```bash
   cd app
   cp .env .env.local
   echo 'DATABASE_URL="mysql://symfony:symfony@db:3306/symfony?serverVersion=8.0&charset=utf8mb4"' >> .env.local
   ```
2. Boot the containers and install dependencies:
   ```bash
   cd ..
   docker compose up --build -d
   docker compose exec php composer install
   ```
3. Run database migrations inside the PHP container:
   ```bash
   docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
   ```

### Iterating

- Application URL: `http://localhost:8080` (Symfony front controller).
- API base URL: `http://localhost:8080/v1`.
- To stop services: `docker compose down`.
- To inspect logs: `docker compose logs -f php` (replace `php` with `web` or `db` as needed).

## Data Model & Business Rules

- `reservation` table columns and constraints are defined in `app/migrations/Version20251015092411.php`. Lead time, pickup-before-return, and maximum duration rules are also reinforced in code-level validators.
- The prototype hardcodes subscription blackout ranges so that availability checks combine ad-hoc reservations with fleet commitments.
- Reservation status transitions are limited to `REQUESTED → CONFIRMED|DECLINED`; subsequent transitions and automation are deferred to the MVP.

## Next Steps

- Replace the static subscription DAO with integrations to real subscription/long-term lease data.
- Add authentication, audit trails, and eventing around reservation lifecycle changes.
- Extend automated tests (unit and contract) to cover service and controller behaviour before growing the surface area.
