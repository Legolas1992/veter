## 2024-10-24 - Missing Authentication in Report Generation
**Vulnerability:** PDF export scripts (`veterinaria/reportes/exportar_historial.php` and `exportar_stock.php`) were accessible without authentication. This exposed sensitive patient medical history (PII) and stock data to anyone with the URL.
**Learning:** In native PHP apps without a framework router, every entry point (file) must manually verify the session. It's common to miss this in "utility" scripts like PDF generators that don't include the main UI header.
**Prevention:** Implement a dedicated `auth_check.php` include that is required at the top of every accessible PHP file, or ensure the project structure routes all requests through a single front controller (index.php) that handles auth.

## 2026-01-23 - Unauthenticated Admin Reset Script
**Vulnerability:** A leftover setup file `veterinaria/reset_admin.php` allowed unauthenticated users to reset the administrator password and create new admin accounts. This is a critical backdoor.
**Learning:** Development and setup scripts (e.g., database seeders, password resetters) are often left in the web root after deployment. These are prime targets for attackers.
**Prevention:** Never deploy setup scripts to production. If they are needed, protect them with strong authentication (e.g., basic auth at server level) or, better yet, make them run only via CLI outside the web root.

## 2026-01-25 - Unauthenticated License Extension Script
**Vulnerability:** The script `veterinaria/extender_licencia.php` was accessible without authentication, allowing any user to extend the system license validity indefinitely and modify the database.
**Learning:** Business-critical administrative scripts are sometimes left unprotected if they are considered "internal" or "temporary," but security through obscurity is not security.
**Prevention:** Ensure all PHP files that perform database operations or sensitive actions include the session verification logic or include the main authenticated header.
