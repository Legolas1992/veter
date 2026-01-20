## 2024-05-23 - [Missing Auth in Standalone Scripts]
**Vulnerability:** Found `reportes/exportar_historial.php` and `reportes/exportar_stock.php` accessible without authentication, exposing sensitive data.
**Learning:** Standalone scripts (like PDF generators) that don't include the main layout (header/footer) often miss the global auth check present in those layout files.
**Prevention:** Ensure all standalone processing scripts include a dedicated `auth_check.php` or manually verify session existence at the very top.
