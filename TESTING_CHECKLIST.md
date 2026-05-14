# InfiniteSugar Testing Checklist

Use this before each real-user testing round.

## Command Checks

Run from the project root:

```bash
php artisan test
npm run build
php artisan route:list --except-vendor
```

Also run after deployment:

```bash
php artisan migrate:status
php artisan queue:failed
php artisan about
```

## Public Pages

- [ ] `/` loads without console errors.
- [ ] Hero video loads or poster appears cleanly.
- [ ] No horizontal scrolling at 390 px, 768 px, 1024 px, or desktop widths.
- [ ] Nav remains usable when links wrap.
- [ ] `/pricing` Spark and Forge buttons route correctly for guest and logged-in users.
- [ ] `/spark`, `/forge`, and `/reports` preserve the luxury/executive visual tone.
- [ ] Legal pages render readable long-form copy on mobile.
- [ ] Footer links work.

## Auth

- [ ] Login with valid Firebase email/password.
- [ ] Signup creates Firebase account and Laravel user.
- [ ] Invalid token returns friendly error.
- [ ] Existing email collision returns conflict behavior.
- [ ] Logout invalidates Laravel session.
- [ ] Guest checkout stores desired plan and resumes after login.
- [ ] Admin login works only for admin role.
- [ ] Non-admin user cannot enter `/admin`.

## Dashboard

- [ ] Free user sees free call allowance.
- [ ] Free exhausted user sees upgrade-required state.
- [ ] Spark active user sees live insights unlocked and reports locked.
- [ ] Forge active user sees reports, charts, and badges unlocked.
- [ ] Tester/admin role receives full access.
- [ ] Empty report/chart/badge lists show safe empty states.
- [ ] Structured report links open correct detail pages.
- [ ] Standard file report links open stored files.
- [ ] Locked users cannot access direct report URLs.
- [ ] Missing structured report fields show unavailable fallback instead of crashing.

## Reports And Charts

- [ ] Forge Sunday brief renders valid payload.
- [ ] Forge weekly timeline renders valid payload.
- [ ] Forge weekly heatmap renders valid payload.
- [ ] Forge monthly badge renders valid payload.
- [ ] Heatmap scrolls horizontally on mobile rather than clipping.
- [ ] Timeline SVG scales to container.
- [ ] Long report text wraps without overflow.
- [ ] Badge names render for all allowed badge types.

## Admin

- [ ] Admin dashboard counts users, active users, plans, reports, and webhooks.
- [ ] User search works.
- [ ] Admin can update plan, status, role, call minutes, and free-call flag.
- [ ] Admin can upload standard report file.
- [ ] Admin can upload structured Sunday brief JSON.
- [ ] Admin can upload structured timeline JSON.
- [ ] Admin can upload structured heatmap JSON.
- [ ] Admin can upload structured monthly badge JSON.
- [ ] Invalid JSON returns validation error.
- [ ] Deleting report removes stored file.
- [ ] Admin report tables remain usable on mobile.

## Extension

- [ ] Extension installs from the dashboard ZIP.
- [ ] Popup opens without blank screen.
- [ ] Popup branding/title are production-ready.
- [ ] Google/Firebase login succeeds.
- [ ] Token persists after browser restart.
- [ ] Logout clears extension storage and backend cache state.
- [ ] Popup syncs `/api/me` and `/api/access/check`.
- [ ] Zoom page injects overlay only on intended Zoom URLs.
- [ ] Manual start guidance works.
- [ ] Analyze sends `data:image/*` payload and receives guidance.
- [ ] 401 forces login prompt.
- [ ] 403/402 shows upgrade messaging.
- [ ] Backend outage shows retry/error copy and recovers after refresh.
- [ ] Extension console has no unexpected production errors.

## Stripe

- [ ] Guest checkout redirects to login and resumes checkout.
- [ ] Authenticated Spark checkout redirects to Stripe.
- [ ] Authenticated Forge checkout redirects to Stripe.
- [ ] Cancel flow returns to `/pricing?checkout=cancelled`.
- [ ] Success flow returns to `/dashboard?checkout=success`.
- [ ] Webhook signature verification rejects unsigned/invalid requests.
- [ ] Duplicate webhook events do not reprocess.
- [ ] Payment success marks user active.
- [ ] Payment failure marks user past_due.
- [ ] Subscription delete marks cancelled.
- [ ] Cancelled/past_due access expires after current period end.

## Firebase

- [ ] Authorized production domain is configured.
- [ ] Email/password provider is enabled.
- [ ] Wrong Firebase project token is rejected.
- [ ] Expired token is rejected.
- [ ] Missing email is rejected.
- [ ] Password policy and email enumeration protection are reviewed.
- [ ] Blaze upgrade decision is documented. Current auth-only flow does not require it for soft launch unless quotas/features demand it.

## Production Smoke Test

- [ ] Fresh user signs up.
- [ ] User downloads and installs extension.
- [ ] User starts Zoom web call and sees private overlay.
- [ ] First guidance response arrives.
- [ ] Dashboard usage minutes update.
- [ ] User upgrades to Spark.
- [ ] Spark live insight access remains unlocked.
- [ ] User upgrades to Forge or is manually set to Forge.
- [ ] Forge reports become visible.
- [ ] Admin can upload a test Forge report.
- [ ] User can open the report.

## Regression Notes

Record each test run:

```text
Date:
Environment:
Commit:
Tester:
Browser:
Extension version:
Stripe mode:
Firebase project:
Result:
Issues found:
```
