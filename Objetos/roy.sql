-- --------------------------------------------------------
-- Host:                         localhost
-- Versión del servidor:         10.5.5-MariaDB-log - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             10.2.0.5599
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Volcando estructura de base de datos para roy
CREATE DATABASE IF NOT EXISTS `roy` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish2_ci */;
USE `roy`;

-- Volcando estructura para tabla roy.acceso
CREATE TABLE IF NOT EXISTS `acceso` (
  `perfil_id` int(11) NOT NULL,
  `pagina_id` int(11) NOT NULL,
  PRIMARY KEY (`perfil_id`,`pagina_id`),
  KEY `fk_perfil_has_pagina_pagina1_idx` (`pagina_id`),
  KEY `fk_perfil_has_pagina_perfil1_idx` (`perfil_id`),
  CONSTRAINT `fk_perfil_has_pagina_pagina1` FOREIGN KEY (`pagina_id`) REFERENCES `pagina` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_perfil_has_pagina_perfil1` FOREIGN KEY (`perfil_id`) REFERENCES `perfil` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla roy.asignacion
CREATE TABLE IF NOT EXISTS `asignacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asigno` int(11) DEFAULT NULL,
  `hora` decimal(10,1) DEFAULT NULL,
  `participacion` decimal(10,1) DEFAULT NULL,
  `meta` decimal(10,2) DEFAULT NULL,
  `semana` int(11) DEFAULT NULL,
  `anio` int(11) DEFAULT NULL,
  `fechaCreacion` timestamp NULL DEFAULT current_timestamp(),
  `ultimaMoficacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_asignacion_user1_idx` (`user_id`),
  CONSTRAINT `fk_asignacion_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla roy.banco
CREATE TABLE IF NOT EXISTS `banco` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `banco` varchar(80) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `cuenta` varchar(45) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `cuentaSap` varchar(45) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `cuentaOtra` varchar(45) COLLATE utf8_spanish2_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla roy.corte
CREATE TABLE IF NOT EXISTS `corte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaccion` varchar(20) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `fechaCorte` date DEFAULT NULL,
  `observacion` text COLLATE utf8_spanish2_ci DEFAULT NULL,
  `fechaCreacion` timestamp NULL DEFAULT current_timestamp(),
  `ultimaModificacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `banco_id` int(11) NOT NULL,
  `tipopago_id` int(11) NOT NULL,
  `departamento_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_corte_banco1_idx` (`banco_id`),
  KEY `fk_corte_tipopago1_idx` (`tipopago_id`),
  KEY `fk_corte_departamento1_idx` (`departamento_id`),
  CONSTRAINT `fk_corte_banco1` FOREIGN KEY (`banco_id`) REFERENCES `banco` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_corte_departamento1` FOREIGN KEY (`departamento_id`) REFERENCES `departamento` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_corte_tipopago1` FOREIGN KEY (`tipopago_id`) REFERENCES `tipopago` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla roy.departamento
CREATE TABLE IF NOT EXISTS `departamento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `departamento` varchar(80) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla roy.log
CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fechaCreacion` timestamp NULL DEFAULT current_timestamp(),
  `tipo` varchar(45) COLLATE utf8_spanish2_ci NOT NULL,
  `tabla` varchar(45) COLLATE utf8_spanish2_ci NOT NULL,
  `explicacion` text COLLATE utf8_spanish2_ci NOT NULL,
  `query` text COLLATE utf8_spanish2_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla roy.meta
CREATE TABLE IF NOT EXISTS `meta` (
  `id` int(11) NOT NULL,
  `meta` decimal(10,2) DEFAULT NULL,
  `semana` int(11) DEFAULT NULL,
  `mes` int(11) DEFAULT NULL,
  `anio` int(11) DEFAULT NULL,
  `fechaCreacion` timestamp NULL DEFAULT current_timestamp(),
  `ultimaModificacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `departamento_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_meta_departamento1_idx` (`departamento_id`),
  CONSTRAINT `fk_meta_departamento1` FOREIGN KEY (`departamento_id`) REFERENCES `departamento` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla roy.modulo
CREATE TABLE IF NOT EXISTS `modulo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modulo` varchar(45) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `estado` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla roy.pagina
CREATE TABLE IF NOT EXISTS `pagina` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` int(11) DEFAULT NULL,
  `pagina` varchar(60) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `estado` tinyint(4) DEFAULT NULL,
  `modulo_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pagina_modulo1_idx` (`modulo_id`),
  CONSTRAINT `fk_pagina_modulo1` FOREIGN KEY (`modulo_id`) REFERENCES `modulo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla roy.pais
CREATE TABLE IF NOT EXISTS `pais` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pais` varchar(45) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `impuesto` decimal(10,2) DEFAULT NULL,
  `simbolo` char(1) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `subsidiaria` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla roy.perfil
CREATE TABLE IF NOT EXISTS `perfil` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `perfil` varchar(45) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8_spanish2_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla roy.tipopago
CREATE TABLE IF NOT EXISTS `tipopago` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(20) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `descripcion` varchar(45) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `porcentaje` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla roy.user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pnombre` varchar(45) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `snombre` varchar(45) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `papellido` varchar(45) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `sapellido` varchar(45) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `email` varchar(80) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `user` int(11) DEFAULT NULL,
  `pass` varchar(254) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `estado` tinyint(4) DEFAULT NULL,
  `imagen` varchar(45) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `fechaCreacion` timestamp NULL DEFAULT current_timestamp(),
  `ultimaModificacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `pais_id` int(11) NOT NULL,
  `perfil_id` int(11) NOT NULL,
  `departamento_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  UNIQUE KEY `user_UNIQUE` (`user`),
  KEY `fk_user_pais_idx` (`pais_id`),
  KEY `fk_user_perfil1_idx` (`perfil_id`),
  KEY `fk_user_departamento1_idx` (`departamento_id`),
  CONSTRAINT `fk_user_departamento1` FOREIGN KEY (`departamento_id`) REFERENCES `departamento` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_pais` FOREIGN KEY (`pais_id`) REFERENCES `pais` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_perfil1` FOREIGN KEY (`perfil_id`) REFERENCES `perfil` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- La exportación de datos fue deseleccionada.

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
