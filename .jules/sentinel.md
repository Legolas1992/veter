## 2024-10-24 - Missing Authentication in Report Generation
**Vulnerability:** PDF export scripts (`veterinaria/reportes/exportar_historial.php` and `exportar_stock.php`) were accessible without authentication. This exposed sensitive patient medical history (PII) and stock data to anyone with the URL.
**Learning:** In native PHP apps without a framework router, every entry point (file) must manually verify the session. It's common to miss this in "utility" scripts like PDF generators that don't include the main UI header.
**Prevention:** Implement a dedicated `auth_check.php` include that is required at the top of every accessible PHP file, or ensure the project structure routes all requests through a single front controller (index.php) that handles auth.

## 2026-01-23 - Unauthenticated Admin Reset Script
**Vulnerability:** A leftover setup file `veterinaria/reset_admin.php` allowed unauthenticated users to reset the administrator password and create new admin accounts. This is a critical backdoor.
**Learning:** Development and setup scripts (e.g., database seeders, password resetters) are often left in the web root after deployment. These are prime targets for attackers.
**Prevention:** Never deploy setup scripts to production. If they are needed, protect them with strong authentication (e.g., basic auth at server level) or, better yet, make them run only via CLI outside the web root.

## 2026-01-29 - Unauthenticated Form Handlers and Insecure Logging
**Vulnerability:** Multiple backend form handlers (`clientes/guardar.php`, `facturacion/guardar.php`, etc.) lacked authentication checks, allowing unauthenticated data manipulation. Additionally, `facturacion/guardar.php` was logging sensitive transaction data and SQL errors to a public text file.
**Learning:** "Invisible" backend scripts (redirectors) often get missed during security reviews because they don't have a UI. Developers sometimes leave debug logging enabled in production code.
**Prevention:** Enforce a strict "secure by default" policy where a middleware or base include (`auth_check.php`) is mandatory for *all* PHP files. Use a proper logging library that writes to a secure location outside the web root, not `file_put_contents`.

## 2026-05-21 - Unauthenticated API Endpoint (IDOR Risk)
**Vulnerability:** The `veterinaria/facturacion/api_check_status.php` endpoint was accessible without authentication and exposed database error details. This allowed unauthenticated users to query invoice statuses (IDOR potential).
**Learning:** API endpoints returning JSON often escape notice during manual browsing but are critical attack surfaces. They must include the same authentication checks as UI pages.
**Prevention:** Audit all `header('Content-Type: application/json')` files to ensure they include `auth_check.php` or equivalent validation before processing any input.

## 2026-05-24 - Unauthenticated External Dependency Script
**Vulnerability:** `veterinaria/download_fpdf.php` was accessible without authentication, allowing any user to trigger an external file download and overwrite `includes/fpdf/fpdf.php`.
**Learning:** Utility scripts for setup or maintenance often bypass standard authentication because they are "just for admins," but if they are in the web root, they are public.
**Prevention:** Always wrap utility scripts with `require_admin()` or move them outside the web root (e.g., to a `bin/` directory) and run them via CLI.

## 2026-05-25 - Unauthenticated IDOR in Payment Simulation
**Vulnerability:** `veterinaria/facturacion/pago_simulado.php` allowed unauthenticated users to mark any invoice as paid by manipulating the `id` parameter (IDOR).
**Learning:** Public-facing features (like QR codes) cannot use session auth but must still be secured. ID enumeration is trivial for integers.
**Prevention:** Use HMAC-SHA256 signed URLs for all stateless, public-facing actions. Verify the signature before processing any state changes.

## 2026-05-26 - Unauthenticated Deletion Scripts
**Vulnerability:** `eliminar.php` scripts in `clientes`, `mascotas`, and `turnos` were accessible without authentication. Attackers could delete arbitrary records by ID enumeration.
**Learning:** In flat PHP architectures, every file is a route. It's easy to copy-paste a CRUD file (like `eliminar.php`) and forget to include the auth check header, especially if the dev tests it while logged in (where it "just works").
**Prevention:** Use a router or front-controller pattern. If that's not possible, write a linter or pre-commit hook that greps for `auth_check.php` in every `.php` file that performs write operations.

## 2026-05-27 - Persisting Unauthenticated Handlers
**Vulnerability:** Despite previous fixes, `veterinaria/turnos/guardar.php` and `veterinaria/historial/guardar.php` remained unauthenticated. This allowed unauthorized appointment creation and medical record tampering/stock deduction.
**Learning:** Fixing a class of bugs (like "missing auth in form handlers") requires exhaustive search, not just fixing the ones found in the initial report. `grep` is your friend.
**Prevention:** Implement a CI check that lists all `.php` files receiving POST requests and verifies they include `auth_check.php` or have manual `$_SESSION` checks.

## 2026-05-30 - Information Disclosure via Exception Messages
**Vulnerability:** Database connection errors and system exceptions were being echoed directly to the user or passed via URL parameters in `db.php` and `auth_action.php`. This exposes database credentials, hostnames, and internal path structures to attackers (CWE-209).
**Learning:** Developers often output raw exception messages to debug issues during development but forget to switch to secure logging in production. In PHP, `catch (Exception $e) { echo $e->getMessage(); }` is a dangerous default pattern.
**Prevention:** Always use `error_log()` for technical details and show generic, user-friendly messages to the end user. Configure `display_errors = Off` in production `php.ini`.

## 2026-06-05 - Hardcoded Secrets and Insecure Configuration
**Vulnerability:** The application used a hardcoded fallback for `APP_SECRET` in `config.php`, exposing the signing key for critical operations. Additionally, `display_errors` was explicitly enabled in code, leaking path information.
**Learning:** Hardcoding fallback secrets "for convenience" or "for lack of .env support" is a security anti-pattern. If the environment is not configured, the application should fail securely or generate a secure local secret, never fallback to a public constant.
**Prevention:** Remove all hardcoded secret fallbacks. Implement a dynamic secret generation mechanism (e.g., `random_bytes`) for unconfigured environments and persist it securely outside the webroot or in a protected config directory.
