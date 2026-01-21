## 2024-10-26 - Missing Authentication on Delete Actions
**Vulnerability:** Several `eliminar.php` scripts (mascotas, clientes, turnos, facturacion) lack authentication checks, allowing unauthenticated users to delete data.
**Learning:** These files do not include the main `header.php` which contains the global auth check. They only include `config.php` and `db.php`.
**Prevention:** Ensure all action scripts (CRUD) either include the protected header or implement their own session validation at the top.
