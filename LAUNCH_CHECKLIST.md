# InfiniteSugar Launch Checklist

Production readiness status: soft-launchable after blockers are closed.

## Launch Blockers

- [ ] Set production environment values. Current local examples still use `APP_ENV=local`, `APP_DEBUG=true`, `LOG_LEVEL=debug`, and unencrypted sessions. Production must use `APP_ENV=production`, `APP_DEBUG=false`, `LOG_LEVEL=warning`, `SESSION_SECURE_COOKIE=true`, and preferably `SESSION_ENCRYPT=true`.
- [ ] Rebuild and repackage the Chrome extension with the final production API host only. The shipped manifest still allows `http://127.0.0.1:8000/*` and the bundle embeds `https://mickolidia.easytechinfo.net/api`.
- [ ] Confirm Stripe live keys, live price IDs, success URL, cancel URL, and webhook signing secret before any paid user test.
- [ ] Add a real report generation/delivery job path for Sunday 9 PM reports and Monday progress summaries, or document that reports are manually uploaded during the first user test window.
- [ ] Confirm Firebase Authorized domains include the production domain and remove development domains that are not needed for launch.
- [ ] Run production smoke tests on the deployed domain: login, checkout, webhook, dashboard, extension login, Zoom overlay, analyze, logout.
- [ ] Configure database backups and restore test at least once before inviting real users.
- [ ] Verify `storage/`, `bootstrap/cache/`, and `storage/app/public` are writable on the host, and run `php artisan storage:link`.

## Frontend And Dashboard QA

- [ ] Check `/` at 390, 768, 1024, and 1440 px widths.
- [ ] Check `/pricing`, `/spark`, `/forge`, `/reports`, `/privacy-policy`, and `/terms-and-conditions` on mobile and desktop.
- [ ] Confirm long nav text wraps cleanly on mobile. The current nav is a wrapping row, not a collapsed menu.
- [ ] Confirm the home hero video loads, has a useful poster fallback, and does not cause horizontal scrolling.
- [ ] Check all CTA buttons point to the intended flow for guests and authenticated users.
- [ ] Confirm pricing cancel message appears at `/pricing?checkout=cancelled`.
- [ ] Confirm dashboard success message appears at `/dashboard?checkout=success`.
- [ ] Check dashboard empty states for free, Spark, Forge, tester, and admin users.
- [ ] Confirm Forge report, chart, and badge links render only when the user has Forge access.
- [ ] Confirm structured report fallbacks render safely when required JSON fields are missing.
- [ ] Confirm heatmap horizontal scroll works on mobile and timeline SVG scales.
- [ ] Confirm admin report upload errors are visible when structured JSON is invalid.

## Extension QA

- [ ] Install the packaged ZIP from `/extension/download` as an authenticated user.
- [ ] Confirm popup title and branding are production-polished. Current generated HTML still uses `Webpack App` as the page title.
- [ ] Log in through the extension and verify Firebase token persistence after browser restart.
- [ ] Verify logout clears `authEmail`, `authUid`, `authToken`, `backendUser`, `backendAccess`, and cached sync timestamps.
- [ ] Confirm `/api/me` and `/api/access/check` sync on popup open.
- [ ] Confirm `/api/analyze` returns guidance for free trial, Spark, Forge, tester, and admin users.
- [ ] Confirm exhausted free users see upgrade messaging, not a blank overlay.
- [ ] Confirm network retry behavior by briefly blocking the backend and restoring it.
- [ ] Confirm Chrome extension console is quiet enough for store submission. Remove noisy `console.info`, `console.warn`, and `console.error` calls or guard them behind a debug flag.
- [ ] Remove unused host permissions where possible: localhost, Firestore, Storage, EmailJS, or Zoom variants that are not required.

## Stripe QA

- [ ] Use Stripe CLI or dashboard test webhooks to send:
  - `checkout.session.completed`
  - `invoice.payment_succeeded`
  - `invoice.payment_failed`
  - `customer.subscription.created`
  - `customer.subscription.updated`
  - `customer.subscription.deleted`
- [ ] Confirm duplicate webhook event IDs return `duplicate` and do not mutate users twice.
- [ ] Confirm Spark subscription unlocks live insights but not reports.
- [ ] Confirm Forge subscription unlocks reports, charts, and badges.
- [ ] Confirm `past_due` keeps access only until `current_period_end`.
- [ ] Confirm `cancelled` keeps access only until `current_period_end`; after that, plan should become free.
- [ ] Confirm checkout failure surfaces a friendly message.

## Firebase QA

- [ ] Enable Email/Password auth in Firebase.
- [ ] Add production authorized domain.
- [ ] Confirm `FIREBASE_PROJECT_ID`, `FIREBASE_API_KEY`, `FIREBASE_AUTH_DOMAIN`, and `FIREBASE_APP_ID` are set.
- [ ] Confirm backend rejects missing, invalid, wrong-audience, and wrong-issuer ID tokens.
- [ ] Consider requiring verified email before paid access or report access.
- [ ] Enable email enumeration protection and password policy enforcement if available for the project.
- [ ] Blaze is not required for the current basic Firebase Auth email/password flow at low volume. Upgrade only if you need paid Firebase services, higher quotas, Cloud Functions, heavy Firestore/Storage use, or Identity Platform features.

## Go/No-Go

- [ ] All launch blockers closed.
- [ ] `php artisan test` passes.
- [ ] `npm run build` passes.
- [ ] `php artisan route:list --except-vendor` reviewed.
- [ ] Production env checked without exposing secrets.
- [ ] A real user can sign up, download extension, receive guidance, upgrade, and see dashboard access change.

## References

- Firebase pricing: https://firebase.google.com/pricing
- Firebase Authentication docs: https://firebase.google.com/docs/auth
- Firebase password auth docs: https://firebase.google.com/docs/auth/web/password-auth
