## 2024-10-24 - Missing Authentication in Report Generation
**Vulnerability:** PDF export scripts (`veterinaria/reportes/exportar_historial.php` and `exportar_stock.php`) were accessible without authentication. This exposed sensitive patient medical history (PII) and stock data to anyone with the URL.
**Learning:** In native PHP apps without a framework router, every entry point (file) must manually verify the session. It's common to miss this in "utility" scripts like PDF generators that don't include the main UI header.
**Prevention:** Implement a dedicated `auth_check.php` include that is required at the top of every accessible PHP file, or ensure the project structure routes all requests through a single front controller (index.php) that handles auth.
