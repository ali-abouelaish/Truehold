---
name: verify
description: Build/launch/drive recipe for verifying changes to the Truehold Laravel app locally.
---

# Verifying Truehold locally

Laravel 12, sqlite (`database/database.sqlite`), `CACHE_STORE=database`. No build step needed for backend changes; frontend assets are prebuilt (run `npm run build` only if you touched resources/js|css and need fresh assets).

## Launch

```bash
cd d:/Truehold
php artisan serve --host=127.0.0.1 --port=8010   # inherits env vars from the shell
```

Public pages need no login: `/properties`, `/properties/map`, `/properties/{id}`.
Auth-gated UI (agent card, "Original"/"Landlord" quick links, agent filters) won't render via anonymous curl.

## Property feed

Feed priority: Harbor Ops scraped-listings API → Google Sheets → database. Selection happens in `PropertyController::getFeedService()` based on `services.harborops.*` / `services.google.properties.*` config.

To drive the API feed without real credentials, run a stub and set env vars on the serve process:

```bash
php -S 127.0.0.1:8099 router.php &   # stub returning {data, pagination{has_more}, landlord_count}
HARBOROPS_API_URL=http://127.0.0.1:8099 HARBOROPS_API_KEY=test-key-123 php artisan serve --port=8010
```

A working stub router (auth check, 2-page pagination, fail-mode toggle) exists from a past session; recreate along those lines if needed.

## Gotchas

- The feed result is cached (`properties_harborops_all` / `properties_google_sheets_all`) in the database cache. Append `?clear_cache=1` to `/properties` or run `php artisan properties:clear-cache` between scenarios, and clear once more when done so stale stub data doesn't linger.
- `php artisan config:clear` before serving if config was ever cached; env vars are read at boot.
- Feed failures fall back silently to the DB — check `storage/logs/laravel.log` for "Error loading properties from feed" to confirm which source actually served the page.
