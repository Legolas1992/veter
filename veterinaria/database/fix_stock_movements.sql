USE veterinaria_db;

-- Add referencia_id column to movimientos_stock if it doesn't exist
ALTER TABLE movimientos_stock 
ADD COLUMN IF NOT EXISTS referencia_id INT(11) DEFAULT NULL AFTER motivo;
