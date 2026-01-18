USE veterinaria_db;

-- Ensure factura_detalles exists
CREATE TABLE IF NOT EXISTS `factura_detalles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `factura_id` int(11) NOT NULL,
  `tipo` enum('servicio','producto') NOT NULL,
  `referencia_id` int(11) DEFAULT NULL,
  `descripcion` varchar(255) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add 'concepto' column to 'facturacion' if it doesn't exist
-- MySQL doesn't have IF NOT EXISTS for columns, so we use a procedure or ignore error.
-- Easiest way in pure SQL script without procedure is to just try adding it. 
-- However, running duplicate ADD COLUMN throws error.
-- We will assume it might fail if exists, but we want to ensure it exists.
-- A workaround is to check empty table or just run it and ignore specific error, but correct way:

SET @dbname = DATABASE();
SET @tablename = "facturacion";
SET @columnname = "concepto";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE facturacion ADD COLUMN concepto VARCHAR(255) DEFAULT 'Servicios Varios' AFTER cliente_id"
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
