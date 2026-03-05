# Event Booking System API

Backend API built with Laravel + Sanctum.

---

## Requirements

- PHP 8.2+
- Composer
- MySQL

---

## Setup

```bash
# 1. Clone the project
git clone https://github.com/yourname/event-booking-system.git
cd event-booking-system

# 2. Install dependencies
composer install

# 3. Copy env file
cp .env.example .env

# 4. Generate app key
php artisan key:generate

# 5. Configure database in .env
DB_DATABASE=booking_db
DB_USERNAME=root
DB_PASSWORD=

# 6. Run migrations and seed
php artisan migrate:fresh --seed

# 7. Start queue worker (for notifications)
php artisan queue:work

# 8. Start server
php artisan serve
```

Server runs at: `http://127.0.0.1:8000`

---

## Seeded Accounts

| Role      | Count | Password   |
| --------- | ----- | ---------- |
| Admin     | 2     | `password` |
| Organizer | 3     | `password` |
| Customer  | 10    | `password` |

Also seeded: **5 events**, **15 tickets**, **20 bookings**

---

## Authentication

Uses **Laravel Sanctum**. Add token to all protected requests:

```
Authorization: Bearer {token}
```

---

## API Endpoints

### Auth

```
POST   /api/register
POST   /api/login
POST   /api/logout
GET    /api/me
```

### Events

```
GET    /api/events
GET    /api/events/{id}
POST   /api/events         (organizer/admin)
PUT    /api/events/{id}    (organizer/admin)
DELETE /api/events/{id}    (organizer/admin)
```

### Tickets

```
POST   /api/events/{event_id}/tickets   (organizer/admin)
PUT    /api/tickets/{id}                (organizer/admin)
DELETE /api/tickets/{id}                (organizer/admin)
```

### Bookings

```
POST   /api/tickets/{id}/bookings   (customer)
GET    /api/bookings                (customer)
PUT    /api/bookings/{id}/cancel    (customer)
```

### Payments

```
POST   /api/bookings/{id}/payment
GET    /api/payments/{id}
```

---

## Running Tests

```bash
php artisan test
```
