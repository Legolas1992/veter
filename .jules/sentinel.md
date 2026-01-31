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
