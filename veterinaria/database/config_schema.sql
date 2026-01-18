-- Configuration Table
USE veterinaria_db;

CREATE TABLE IF NOT EXISTS `configuracion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_negocio` varchar(100) NOT NULL DEFAULT 'Veterinaria',
  `razon_social` varchar(150) DEFAULT NULL,
  `cuit` varchar(20) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT 'assets/resources/logo.jpg',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Default Config
INSERT INTO `configuracion` (`nombre_negocio`, `direccion`, `email`) 
SELECT 'Veterinaria Demo', 'Calle Falsa 123', 'contacto@vet.com'
WHERE NOT EXISTS (SELECT * FROM configuracion);
