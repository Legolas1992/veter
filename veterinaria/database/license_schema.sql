-- License Table
USE veterinaria_db;

CREATE TABLE IF NOT EXISTS `sistema_licencia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_limite` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Initialize with 30 days from now
INSERT INTO `sistema_licencia` (`fecha_limite`) 
SELECT DATE_ADD(CURDATE(), INTERVAL 30 DAY)
WHERE NOT EXISTS (SELECT * FROM sistema_licencia);
