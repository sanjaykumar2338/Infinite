# InfiniteSugar Chrome Extension Launch Audit

## Canonical Extension Artifact

- The backend repository does not contain a separate Chrome extension source project or an extension-specific webpack/vite source build.
- The Laravel `npm run build` command builds website assets only.
- The installable extension artifact in this repository is the compiled folder at `public/extension-build/infinite-sugar`.
- The production package command is `npm run package:extension`.
- The validation command is `npm run validate:extension`.

## Final Download

- Version: `1.6.2`
- Download filename: `InfiniteSugar-Chrome-Extension-v1.6.2.zip`
- Authenticated download page: `/extension`
- Download endpoint: `/extension/download`
- Package output location: `public/downloads/InfiniteSugar-Chrome-Extension-v1.6.2.zip`

The ZIP is built from the contents of `public/extension-build/infinite-sugar`, so `manifest.json` is directly at the extracted folder root. Users should extract the ZIP once and select that extracted folder in Chrome's Load unpacked flow.

## MV3 And Production Host Notes

- `manifest_version` is `3`.
- Background service worker is `service-worker.js`, which loads production config before `background.js`.
- Production API base URL is `https://www.infinitesugar.com/api`.
- Host permissions include `https://www.infinitesugar.com/*` and supported Zoom web meeting URL patterns.
- Old backend hosts, localhost, duplicate nested extension folders, source maps, nested ZIP files, and `node_modules` are excluded from the final build.

## Supported Zoom Surface

InfiniteSugar is a Chrome desktop extension for supported Zoom web meeting pages, including Zoom web client paths such as `/wc`, `/j`, and `/my` on `zoom.us`, subdomains of `zoom.us`, and `app.zoom.us`.

It does not claim support for the native Zoom desktop application.

## Manual Activation

The current user flow is manual activation:

1. Open a supported Zoom web meeting page in Chrome.
2. Open the InfiniteSugar extension popup.
3. Confirm subscription status is verified.
4. Click **Start Guidance**.

The content script can detect meeting pages and the popup can inject/start the overlay when needed.
