## 2024-10-24 - Missing Authentication in Report Generation
**Vulnerability:** PDF export scripts (`veterinaria/reportes/exportar_historial.php` and `exportar_stock.php`) were accessible without authentication. This exposed sensitive patient medical history (PII) and stock data to anyone with the URL.
**Learning:** In native PHP apps without a framework router, every entry point (file) must manually verify the session. It's common to miss this in "utility" scripts like PDF generators that don't include the main UI header.
**Prevention:** Implement a dedicated `auth_check.php` include that is required at the top of every accessible PHP file, or ensure the project structure routes all requests through a single front controller (index.php) that handles auth.

## 2026-01-23 - Unauthenticated Admin Reset Script
**Vulnerability:** A leftover setup file `veterinaria/reset_admin.php` allowed unauthenticated users to reset the administrator password and create new admin accounts. This is a critical backdoor.
**Learning:** Development and setup scripts (e.g., database seeders, password resetters) are often left in the web root after deployment. These are prime targets for attackers.
**Prevention:** Never deploy setup scripts to production. If they are needed, protect them with strong authentication (e.g., basic auth at server level) or, better yet, make them run only via CLI outside the web root.

## 2026-07-20 - Unprotected Backend Action Scripts
**Vulnerability:** Backend form handlers (`veterinaria/clientes/guardar.php`, `eliminar.php`, etc.) performed database operations without checking for authentication. Attackers could manipulate data by sending POST requests directly to these files.
**Learning:** In native PHP apps, files that don't output HTML (like AJAX handlers or form processors) often miss the 'header.php' include where auth logic usually lives.
**Prevention:** Created a centralized `veterinaria/includes/auth_check.php` and enforced its inclusion in all action scripts.
