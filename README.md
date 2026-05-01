# Infinite Sugar SaaS Backend

Laravel backend API and Blade admin panel for the Infinite Sugar Chrome extension SaaS.

## Stack

- Laravel 12.x, PHP 8.2+
- MySQL, PostgreSQL, or SQLite for local development
- Firebase Auth ID token verification
- Stripe hosted Checkout and Stripe Webhooks
- Blade + Bootstrap admin panel

## Setup

```bash
php composer.phar install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Set `ADMIN_EMAIL` and `ADMIN_PASSWORD` before seeding if you want the seeder to create an admin login.

## Environment Variables

```env
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=infinite_sugar
DB_USERNAME=root
DB_PASSWORD=

ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=change-me
ADMIN_NAME="Infinite Sugar Admin"

FIREBASE_PROJECT_ID=your-firebase-project-id
FIREBASE_API_KEY=your-browser-api-key
FIREBASE_AUTH_DOMAIN=your-project.firebaseapp.com
FIREBASE_APP_ID=your-firebase-app-id

STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
STRIPE_SPARK_PRICE_ID=price_...
STRIPE_FORGE_PRICE_ID=price_...
STRIPE_SUCCESS_URL="${APP_URL}/dashboard?checkout=success"
STRIPE_CANCEL_URL="${APP_URL}/pricing?checkout=cancelled"
```

## Firebase

Chrome extension users authenticate with Firebase. Protected API routes expect:

```http
Authorization: Bearer <firebase_id_token>
```

Laravel verifies the ID token against Firebase public keys, checks the configured `FIREBASE_PROJECT_ID`, then finds or creates the local user by `firebase_uid`. Firebase remains the login identity source; Laravel owns plan, status, Stripe IDs, call usage, roles, and reports.

The public website user login at `/login` and `/signup` uses Firebase email/password auth in the browser. After Firebase returns an ID token, Laravel verifies it at `POST /login/firebase` and creates a normal web session for `/dashboard`.

## Stripe

Checkout is hosted by Stripe only:

- `POST /api/billing/checkout/spark`
- `POST /api/billing/checkout/forge`

Webhook endpoint:

- `POST /api/stripe/webhook`

Configure the Stripe webhook URL as:

```text
https://your-domain.com/api/stripe/webhook
```

Send these events:

- `checkout.session.completed`
- `invoice.payment_succeeded`
- `invoice.payment_failed`
- `customer.subscription.updated`
- `customer.subscription.deleted`

Webhook idempotency is handled by the `stripe_events` table using Stripe event IDs.

## Chrome Extension API

All routes below require a Firebase bearer token except the Stripe webhook.

- `GET /api/me`
- `GET /api/access/check`
- `POST /api/call/start`
- `POST /api/call/usage`
- `POST /api/billing/checkout/spark`
- `POST /api/billing/checkout/forge`

## User Website

- `/login`
- `/signup`
- `/dashboard`
- `POST /login/firebase`
- `POST /logout`

The user dashboard is protected by Laravel's web auth session. Guests are redirected to `/login`. Admin login remains separate at `/adnin`.

`GET /api/access/check` returns plan/status, Forge feature access, free trial usage, and remaining free minutes. The extension must never send plan/status updates; those only change through Stripe webhooks or admin actions.

## Access Rules

- Free users get 30 Spark call minutes.
- Active Spark users can continue Spark call access.
- Active Forge users get live insights and reports.
- `tester` and `admin` roles get full access for testing.
- Failed payments mark `past_due` without deleting the user.
- Cancelled subscriptions keep the plan until `current_period_end` when Stripe provides a future period end.

## Admin Panel

Admin routes:

- `/adnin`
- `/admin`
- `/admin/users`
- `/admin/reports`

Admins can search users, assign plan/status/role, view Stripe IDs, view usage, upload/manage reports, charts, badge reports, and inspect recent Stripe webhook events.

## Website Pages

Basic backend-supported pages are available:

- `/`
- `/pricing`
- `/spark`
- `/forge`
- `/reports`
- `/privacy-policy`
- `/terms-and-conditions`
- `/dashboard`

## Tests

```bash
php artisan test
```

Feature tests cover Firebase user creation, free/Spark/Forge access, the 30-minute trial cap, Stripe Checkout creation, webhook idempotency, payment success/failure, subscription cancellation, and tester full access.

## Deployment Notes

- Use HTTPS in production.
- Set real Stripe and Firebase environment variables.
- Run `php artisan migrate --force`.
- Run `php artisan storage:link` for uploaded report files.
- Configure a queue worker if automated report generation is added later.
- Keep `STRIPE_WEBHOOK_SECRET` private and rotate it if exposed.
