# InfiniteSugar Production Deployment

This guide targets a Bluehost-style shared hosting deployment first, with notes for stronger VPS-style hosting where relevant.

## Recommended Production Environment

Set these values in production:

```dotenv
APP_NAME=InfiniteSugar
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-production-domain.com
LOG_CHANNEL=stack
LOG_STACK=daily
LOG_LEVEL=warning

DB_CONNECTION=mysql
QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=database
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

FILESYSTEM_DISK=local
```

Required secrets:

```dotenv
APP_KEY=
FIREBASE_PROJECT_ID=
FIREBASE_API_KEY=
FIREBASE_AUTH_DOMAIN=
FIREBASE_APP_ID=
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=
STRIPE_PRICE_SPARK=
STRIPE_PRICE_FORGE=
STRIPE_SUCCESS_URL="${APP_URL}/dashboard?checkout=success"
STRIPE_CANCEL_URL="${APP_URL}/pricing?checkout=cancelled"
ADMIN_EMAIL=
ADMIN_PASSWORD=
ADMIN_NAME=
```

Do not commit real secrets. Rotate any secret that has been shared in chat, screenshots, ZIPs, or public repos.

## Bluehost Deployment Steps

1. Upload the Laravel project outside `public_html` if Bluehost allows it.
2. Point the domain document root to the Laravel `public/` directory.
3. If Bluehost cannot point to `public/`, keep the application outside `public_html` and place only the public entry files/assets in `public_html`, adjusting `index.php` paths carefully.
4. Create a MySQL database and user in Bluehost cPanel.
5. Configure `.env` with production values and MySQL credentials.
6. Install dependencies locally or on the server:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan key:generate --force
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

7. Set writable permissions for:

```text
storage/
bootstrap/cache/
storage/app/public/
```

8. Upload `public/downloads/infinite-sugar-extension.zip` after rebuilding the production extension.
9. Verify `/`, `/pricing`, `/login`, `/dashboard`, `/extension/download`, and `/api/stripe/webhook`.

## Cron Jobs

Bluehost usually provides cPanel cron. Add one scheduler entry:

```bash
* * * * * cd /home/ACCOUNT/path/to/infinite-backend && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

The current app does not yet define Sunday/Monday scheduled commands. Add scheduled jobs before launch automation:

- Sunday 9:00 PM app timezone: generate Forge weekly brief, weekly heatmap, and weekly timeline.
- Monday 9:00 AM app timezone: generate monthly/weekly progress summary or badge report when eligible.
- Daily: prune stale sessions, check failed jobs, and send admin health summary.

If Bluehost cannot run a queue worker continuously, use database queue with cron-driven workers:

```bash
* * * * * cd /home/ACCOUNT/path/to/infinite-backend && /usr/bin/php artisan queue:work database --stop-when-empty --tries=3 --timeout=90 >> storage/logs/queue.log 2>&1
```

For a VPS, prefer Supervisor or systemd for a long-running queue worker.

## Queue And Cache Recommendations

- Soft launch on Bluehost: `QUEUE_CONNECTION=database`, `CACHE_STORE=database`.
- Higher traffic: move cache and queue to Redis.
- Keep report generation and email delivery out of web requests.
- Set failed job retention and review failed jobs daily during beta.

## Stripe Setup

1. Create live Stripe products and recurring prices for Spark and Forge.
2. Set live `STRIPE_PRICE_SPARK` and `STRIPE_PRICE_FORGE`.
3. Set live `STRIPE_SECRET` and publishable key if the frontend later needs it.
4. Add webhook endpoint:

```text
https://your-production-domain.com/api/stripe/webhook
```

5. Subscribe to these events:

```text
checkout.session.completed
invoice.payment_succeeded
invoice.payment_failed
customer.subscription.created
customer.subscription.updated
customer.subscription.deleted
```

6. Set `STRIPE_WEBHOOK_SECRET` from the endpoint signing secret.
7. Test duplicate event delivery. The app stores `stripe_event_id` and returns `duplicate` for repeats.

Current behavior:

- `checkout.session.completed` marks the user active and stores customer/subscription IDs.
- `invoice.payment_succeeded` marks active and updates period end from invoice lines.
- `invoice.payment_failed` marks `past_due`.
- subscription created/updated maps Stripe status to app status.
- subscription deleted marks `cancelled` and keeps plan only when `current_period_end` is still future.

Production caution: `checkout.session.completed` does not set `current_period_end` by itself. Make sure subscription or invoice webhooks arrive and update the period before relying on grace-period logic.

## Firebase Setup

1. Enable Email/Password sign-in.
2. Add production domain under Firebase Authentication authorized domains.
3. Match `FIREBASE_AUTH_DOMAIN` to the Firebase project.
4. Keep `FIREBASE_PROJECT_ID` identical to the backend verifier project.
5. Use HTTPS only in production.
6. Consider email verification enforcement for paid access.
7. Enable password policy and email enumeration protection if supported in the project.

Blaze assessment:

- Not required yet for the current Laravel-backed Auth usage at low volume.
- Spark/no-cost plan can support basic Firebase Authentication subject to quotas.
- Upgrade to Blaze if you need paid Firebase services, exceed no-cost quotas, add Cloud Functions, use significant Firestore/Storage, use phone auth heavily, or need Identity Platform features.

## Backups

Minimum:

- Nightly MySQL dump.
- Keep 7 daily, 4 weekly, and 3 monthly backups.
- Store at least one copy outside Bluehost.
- Back up `storage/app/public/reports` if file uploads remain on local disk.
- Test one restore before launch.

Example:

```bash
mysqldump -u DB_USER -p'DB_PASS' DB_NAME | gzip > backups/infinitesugar-$(date +%F).sql.gz
```

Prefer cPanel backup tools or a managed backup provider if shell access is limited.

## Monitoring And Logging

- Use `LOG_STACK=daily` and `LOG_LEVEL=warning` for launch.
- Add uptime monitoring for `/` and `/login`.
- Add error monitoring such as Sentry, Bugsnag, Flare, or a log shipping service.
- Add Stripe webhook failure alerts in Stripe dashboard.
- Review `storage/logs/laravel.log` daily during the first week.
- Track these metrics manually or in an admin health page:
  - signups
  - active Spark users
  - active Forge users
  - failed checkouts
  - webhook failures
  - analyze endpoint 4xx/5xx
  - queue failed jobs

## Post-Deploy Commands

```bash
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
php artisan queue:failed
```

## Rollback Notes

- Keep the previous deployment ZIP or release directory.
- Take a database backup before every migration.
- If a deploy fails after code upload but before migration, restore previous code and run `php artisan optimize:clear`.
- If a migration fails, do not guess. Restore from the pre-deploy database backup or write a targeted corrective migration.

## References

- Firebase pricing: https://firebase.google.com/pricing
- Firebase Authentication docs: https://firebase.google.com/docs/auth
