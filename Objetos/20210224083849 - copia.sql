/*
MariaDB Backup
Database: roy
Backup Time: 2021-02-24 08:38:52
*/

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `roy`.`acceso`;
DROP TABLE IF EXISTS `roy`.`banco`;
DROP TABLE IF EXISTS `roy`.`corte`;
DROP TABLE IF EXISTS `roy`.`departamento`;
DROP TABLE IF EXISTS `roy`.`log`;
DROP TABLE IF EXISTS `roy`.`modulo`;
DROP TABLE IF EXISTS `roy`.`pagina`;
DROP TABLE IF EXISTS `roy`.`pais`;
DROP TABLE IF EXISTS `roy`.`perfil`;
DROP TABLE IF EXISTS `roy`.`tipopago`;
DROP TABLE IF EXISTS `roy`.`user`;
CREATE TABLE `acceso` (
  `perfil_id` int(11) NOT NULL,
  `pagina_id` int(11) NOT NULL,
  PRIMARY KEY (`perfil_id`,`pagina_id`),
  KEY `fk_perfil_has_pagina_pagina1_idx` (`pagina_id`),
  KEY `fk_perfil_has_pagina_perfil1_idx` (`perfil_id`),
  CONSTRAINT `fk_perfil_has_pagina_pagina1` FOREIGN KEY (`pagina_id`) REFERENCES `pagina` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_perfil_has_pagina_perfil1` FOREIGN KEY (`perfil_id`) REFERENCES `perfil` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
CREATE TABLE `banco` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `banco` varchar(80) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `cuenta` varchar(45) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `cuentaSap` varchar(45) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `cuentaOtra` varchar(45) COLLATE utf8_spanish2_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
CREATE TABLE `corte` (
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
) ENGINE=InnoDB AUTO_INCREMENT=170226 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
CREATE TABLE `departamento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `departamento` varchar(80) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fechaCreacion` timestamp NULL DEFAULT current_timestamp(),
  `tipo` varchar(45) COLLATE utf8_spanish2_ci NOT NULL,
  `tabla` varchar(45) COLLATE utf8_spanish2_ci NOT NULL,
  `explicacion` text COLLATE utf8_spanish2_ci NOT NULL,
  `query` text COLLATE utf8_spanish2_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
CREATE TABLE `modulo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modulo` varchar(45) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `estado` tinyint(4) DEFAULT NULL,
  `icono` varchar(80) COLLATE utf8_spanish2_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
CREATE TABLE `pagina` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` int(11) DEFAULT NULL,
  `pagina` varchar(60) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `acronimo` varchar(40) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `modulo_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pagina_modulo1_idx` (`modulo_id`),
  CONSTRAINT `fk_pagina_modulo1` FOREIGN KEY (`modulo_id`) REFERENCES `modulo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=181 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
CREATE TABLE `pais` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pais` varchar(45) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `impuesto` decimal(10,2) DEFAULT NULL,
  `simbolo` char(1) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `subsidiaria` tinyint(4) DEFAULT NULL,
  `nombre_sbs` varchar(50) COLLATE utf8_spanish2_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
CREATE TABLE `perfil` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `perfil` varchar(45) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8_spanish2_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
CREATE TABLE `tipopago` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(20) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `descripcion` varchar(45) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `porcentaje` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
CREATE TABLE `user` (
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
BEGIN;

DELETE FROM `roy`.`acceso`;
INSERT INTO `roy`.`acceso` (`perfil_id`,`pagina_id`) VALUES (1, 1),(1, 2),(1, 3),(1, 21),(1, 22),(1, 23),(1, 71),(1, 72),(1, 73),(1, 171),(1, 172);

COMMIT;
BEGIN;

DELETE FROM `roy`.`banco`;
INSERT INTO `roy`.`banco` (`id`,`banco`,`cuenta`,`cuentaSap`,`cuentaOtra`) VALUES (1, 'Banco Agromercantil', '30-4001227-9', '_SYS00000000030', '11102018-104-000-00'),(2, 'Banco G&T Continental', '00-0126951-3', '_SYS00000000033', '11102021-104-000-00'),(3, 'Banrural', '3139075381\r\n', '_SYS00000000042', '11102030-104-000-00'),(4, 'Banco Reformador', '78-090017-5', '_SYS00000000510', '11102035-104-000-00'),(5, 'Banco de America Central BAC Tarjeta', '900035387', '_SYS00000000037', '11102025-104-000-00'),(6, 'Banco Promerica', '423-320-00-000092-1\r\n', '_SYS00000000032', '11102020-104-000-00'),(7, 'Banco de America Central BAC', '90-135989-3', '_SYS00000000045', '11102033-104-000-00'),(8, 'Banco Promerica Tarjeta', '170198856017', '_SYS00000000038', '11102026-104-000-00'),(9, 'Vivibanco', '02-200-00-2432', '_SYS00000000036', '11102024-104-000-00'),(10, 'Banco Industrial', '007-549142-8', '_SYS00000000031', '11102019-104-000-00'),(11, 'Banco Inmobiliario', '17015011351', NULL, '11102022-104-000-00'),(12, 'Banco Industrial Caex', '007-549142-8', '_SYS00000000031', '11102019-104-000-00');

COMMIT;
BEGIN;

DELETE FROM `roy`.`corte`;


COMMIT;
BEGIN;

DELETE FROM `roy`.`departamento`;
INSERT INTO `roy`.`departamento` (`id`,`departamento`,`numero`) VALUES (1, 'Tienda', 1),(2, 'Tienda', 2),(3, 'Tienda', 3),(4, 'Tienda', 4),(5, 'Tienda', 5),(6, 'Tienda', 6),(7, 'Tienda', 7),(8, 'Tienda', 8),(9, 'Tienda', 9),(10, 'Tienda', 10),(11, 'Tienda', 11),(12, 'Tienda', 12),(13, 'Tienda', 13),(14, 'Tienda', 14),(15, 'Tienda', 15),(16, 'Tienda', 16),(17, 'Tienda', 17),(18, 'Tienda', 18),(19, 'Tienda', 19),(20, 'Tienda', 20),(21, 'Tienda', 21),(22, 'Tienda', 22),(23, 'Tienda', 23),(24, 'Tienda', 24),(25, 'Tienda', 25),(26, 'Tienda', 26),(27, 'Tienda', 27),(28, 'Tienda', 28),(29, 'Tienda', 29),(30, 'Tienda', 30),(31, 'Tienda', 31),(32, 'Tienda', 32),(33, 'Tienda', 33),(34, 'Tienda', 34),(35, 'Tienda', 35),(36, 'Tienda', 36),(37, 'Tienda', 37),(38, 'Tienda', 38),(39, 'Tienda', 39),(40, 'Tienda', 40),(41, 'Tienda', 41),(42, 'Tienda', 42),(43, 'Tienda', 43),(44, 'Tienda', 44),(45, 'Tienda', 45),(46, 'Tienda', 46),(47, 'Tienda', 47),(48, 'Tienda', 48),(49, 'Tienda', 49),(50, 'Tienda', 50),(51, 'Tienda', 51),(52, 'Tienda', 52),(53, 'Tienda', 53),(54, 'Tienda', 54),(55, 'Tienda', 55),(56, 'Tienda', 56),(57, 'Tienda', 57),(58, 'Tienda', 58),(59, 'Tienda', 59),(60, 'Tienda', 60),(61, 'Tienda', 61),(62, 'Tienda', 62),(63, 'Tienda', 63),(64, 'Tienda', 64),(65, 'Tienda', 65),(66, 'Tienda', 66),(67, 'Tienda', 67),(68, 'Tienda', 68),(69, 'Tienda', 69),(70, 'Tienda', 70),(71, 'Tienda', 71),(72, 'Tienda', 72),(73, 'Tienda', 73),(74, 'Tienda', 74),(75, 'Tienda', 75),(76, 'Tienda', 76),(77, 'Tienda', 77),(78, 'Tienda', 78),(79, 'Tienda', 79),(80, 'Tienda', 252),(81, 'Informatica', NULL),(82, 'RH', NULL),(83, 'Operaciones Tienda', NULL),(84, 'Opreaciones Catalogo', NULL),(85, 'Diseño', NULL),(86, 'Mercadeo', NULL),(87, 'Bodega Catalogo', NULL),(88, 'Digitacion', NULL),(89, 'Gerencia Ventas', NULL),(90, 'Gerencia Catalogo', NULL),(91, 'Call Center', NULL),(92, 'Cajas Catalogo', NULL),(93, 'Afiliaciones', NULL),(94, 'Creditos y cobros', NULL),(95, 'Contabilidad Tiendas', NULL),(96, 'Contabilidad Catalogo', NULL),(97, 'Gerencia Proyectos', NULL),(98, 'Devoluciones Catalogo', NULL),(99, 'Comunity Manager', NULL),(100, 'Gerente de zona', NULL),(101, 'Devoluciones Tiendas', NULL),(102, 'Supervision', NULL);

COMMIT;
BEGIN;

DELETE FROM `roy`.`log`;
INSERT INTO `roy`.`log` (`id`,`fechaCreacion`,`tipo`,`tabla`,`explicacion`,`query`,`user_id`) VALUES (1, '2020-11-27 14:42:33', 'UPDATE', 'user', 'Sin comentarios', 'UPDATE user\r\n                        SET pnombre = \'Henrry\',\r\n                            snombre = \'Wilmer\',\r\n                            papellido = \'Garcia\',\r\n                            sapellido = \'\',\r\n                            email = \'informatica.tiendas@calzadoroy.com\',\r\n                            user = 5202,\r\n                            pais_id = 2,\r\n                            perfil_id = 1,\r\n                            departamento_id = 81\r\n                        WHERE id = 2\r\n                    ', 1),(2, '2021-01-13 08:17:06', 'UPDATE', 'user', 'Sin comentarios', 'UPDATE user\r\n                        SET pnombre = \'Nelson\',\r\n                            snombre = \'Ovidio\',\r\n                            papellido = \'Vásquez\',\r\n                            sapellido = \'Ventura\',\r\n                            email = \'it.tiendas@calzadoroy.com\',\r\n                            user = 5254,\r\n                            pais_id = 1,\r\n                            perfil_id = 1,\r\n                            departamento_id = 81,\r\n                            imagen = \'5254.jpg\'\r\n                        WHERE id = 1\r\n                     ', 1),(3, '2021-01-13 12:04:47', 'UPDATE', 'user', 'Sin comentarios', 'UPDATE user\r\n                        SET pnombre = \'Nelson\',\r\n                            snombre = \'Ovidio\',\r\n                            papellido = \'Vásquez\',\r\n                            sapellido = \'Ventura\',\r\n                            email = \'it.tiendas@calzadoroy.com\',\r\n                            user = 5254,\r\n                            pais_id = 1,\r\n                            perfil_id = 1,\r\n                            departamento_id = 81,\r\n                            imagen = \'5254.jpg\'\r\n                        WHERE id = 1\r\n                     ', 1),(4, '2021-01-19 11:31:30', 'UPDATE', 'user', 'Sin comentarios', 'UPDATE user\r\n                        SET estado = 0\r\n                        WHERE id = 2\r\n                    ', 1),(5, '2021-01-19 11:31:51', 'UPDATE', 'user', 'Sin comentarios', 'UPDATE user\r\n                        SET estado = 1\r\n                        WHERE id = 2\r\n                    ', 1),(6, '2021-01-21 16:30:29', 'UPDATE', 'ROY_CORTE', 'Esto es una prueba', 'UPDATE ROY_CORTE\r\n                        SET TRANSACCION = \'193\',\r\n                            MONTO = 512.49,\r\n                            FECHACORTE = TO_DATE(\'2021-01-04\',\'RRRR-MM-DD\'),\r\n                            BANCO_ID = 8 ,\r\n                            TIPOPAGO_ID = 2,\r\n                            DEPARTAMENTO_ID = 65\r\n                        WHERE ID = 621196\r\n                     ', 1);

COMMIT;
BEGIN;

DELETE FROM `roy`.`modulo`;
INSERT INTO `roy`.`modulo` (`id`,`modulo`,`estado`,`icono`) VALUES (1, 'Contabilidad tiendas', 1, 'fas fa-chart-bar'),(2, 'Contabilidad catalogo', 1, 'fas fa-chart-bar'),(3, 'Informatica', 1, 'fas fa-desktop'),(4, 'Operaciones tienda', 1, 'fas fa-chart-pie'),(5, 'Operaciones catalogo', 1, 'fas fa-chart-pie'),(6, 'Mercadeo', 1, 'fas fa-bullhorn'),(7, 'Servicio al cliente', 1, 'fas fa-phone-volume'),(8, 'Tienda', 1, 'fas fa-store-alt'),(9, 'Supervision', 1, 'fas fa-hand-point-right'),(10, 'Gerente de ventas', 1, 'fas fa-chart-line'),(11, 'Gestion Humana', 1, 'fas fa-sitemap'),(12, 'Cobros', 1, 'fas fa-piggy-bank'),(13, 'Afiliaciones', 1, 'fas fa-handshake'),(14, 'Gerentes de zona', 1, 'fas fa-users'),(15, 'Diseño', 1, 'fas fa-palette'),(16, 'Tienda Online', 1, 'fas fa-shopping-cart'),(17, 'Bodega catalogo', 1, 'fas fa-people-carry'),(18, 'Digitacion', 1, 'fas fa-keyboard');

COMMIT;
BEGIN;

DELETE FROM `roy`.`pagina`;
INSERT INTO `roy`.`pagina` (`id`,`numero`,`pagina`,`acronimo`,`modulo_id`) VALUES (1, 1, 'Comiciones', NULL, 1),(2, 2, 'Horarios', NULL, 1),(3, 3, 'Asignacion de horarios', NULL, 1),(4, 4, NULL, NULL, 1),(5, 5, NULL, NULL, 1),(6, 6, NULL, NULL, 1),(7, 7, NULL, NULL, 1),(8, 8, NULL, NULL, 1),(9, 9, NULL, NULL, 1),(10, 10, NULL, NULL, 1),(11, 1, NULL, NULL, 2),(12, 2, NULL, NULL, 2),(13, 3, NULL, NULL, 2),(14, 4, NULL, NULL, 2),(15, 5, NULL, NULL, 2),(16, 6, NULL, NULL, 2),(17, 7, NULL, NULL, 2),(18, 8, NULL, NULL, 2),(19, 9, NULL, NULL, 2),(20, 10, NULL, NULL, 2),(21, 1, 'Crud usuarios', NULL, 3),(22, 2, 'Crud accesos', NULL, 3),(23, 3, 'Crud modulos paginas', NULL, 3),(24, 4, NULL, NULL, 3),(25, 5, NULL, NULL, 3),(26, 6, NULL, NULL, 3),(27, 7, NULL, NULL, 3),(28, 8, NULL, NULL, 3),(29, 9, NULL, NULL, 3),(30, 10, NULL, NULL, 3),(31, 1, NULL, NULL, 4),(32, 2, NULL, NULL, 4),(33, 3, NULL, NULL, 4),(34, 4, NULL, NULL, 4),(35, 5, NULL, NULL, 4),(36, 6, NULL, NULL, 4),(37, 7, NULL, NULL, 4),(38, 8, NULL, NULL, 4),(39, 9, NULL, NULL, 4),(40, 10, NULL, NULL, 4),(41, 1, NULL, NULL, 5),(42, 2, NULL, NULL, 5),(43, 3, NULL, NULL, 5),(44, 4, NULL, NULL, 5),(45, 5, NULL, NULL, 5),(46, 6, NULL, NULL, 5),(47, 7, NULL, NULL, 5),(48, 8, NULL, NULL, 5),(49, 9, NULL, NULL, 5),(50, 10, NULL, NULL, 5),(51, 1, NULL, NULL, 6),(52, 2, NULL, NULL, 6),(53, 3, NULL, NULL, 6),(54, 4, NULL, NULL, 6),(55, 5, NULL, NULL, 6),(56, 6, NULL, NULL, 6),(57, 7, NULL, NULL, 6),(58, 8, NULL, NULL, 6),(59, 9, NULL, NULL, 6),(60, 10, NULL, NULL, 6),(61, 1, NULL, NULL, 7),(62, 2, NULL, NULL, 7),(63, 3, NULL, NULL, 7),(64, 4, NULL, NULL, 7),(65, 5, NULL, NULL, 7),(66, 6, NULL, NULL, 7),(67, 7, NULL, NULL, 7),(68, 8, NULL, NULL, 7),(69, 9, NULL, NULL, 7),(70, 10, NULL, NULL, 7),(71, 1, 'Desempeño semanal', NULL, 8),(72, 2, 'Trimestral Tienda', NULL, 8),(73, 3, 'Ingreso de cortes', NULL, 8),(74, 4, NULL, NULL, 8),(75, 5, NULL, NULL, 8),(76, 6, NULL, NULL, 8),(77, 7, NULL, NULL, 8),(78, 8, NULL, NULL, 8),(79, 9, NULL, NULL, 8),(80, 10, NULL, NULL, 8),(81, 1, NULL, NULL, 9),(82, 2, NULL, NULL, 9),(83, 3, NULL, NULL, 9),(84, 4, NULL, NULL, 9),(85, 5, NULL, NULL, 9),(86, 6, NULL, NULL, 9),(87, 7, NULL, NULL, 9),(88, 8, NULL, NULL, 9),(89, 9, NULL, NULL, 9),(90, 10, NULL, NULL, 9),(91, 1, NULL, NULL, 10),(92, 2, NULL, NULL, 10),(93, 3, NULL, NULL, 10),(94, 4, NULL, NULL, 10),(95, 5, NULL, NULL, 10),(96, 6, NULL, NULL, 10),(97, 7, NULL, NULL, 10),(98, 8, NULL, NULL, 10),(99, 9, NULL, NULL, 10),(100, 10, NULL, NULL, 10),(101, 1, NULL, NULL, 11),(102, 2, NULL, NULL, 11),(103, 3, NULL, NULL, 11),(104, 4, NULL, NULL, 11),(105, 5, NULL, NULL, 11),(106, 6, NULL, NULL, 11),(107, 7, NULL, NULL, 11),(108, 8, NULL, NULL, 11),(109, 9, NULL, NULL, 11),(110, 10, NULL, NULL, 11),(111, 1, NULL, NULL, 12),(112, 2, NULL, NULL, 12),(113, 3, NULL, NULL, 12),(114, 4, NULL, NULL, 12),(115, 5, NULL, NULL, 12),(116, 6, NULL, NULL, 12),(117, 7, NULL, NULL, 12),(118, 8, NULL, NULL, 12),(119, 9, NULL, NULL, 12),(120, 10, NULL, NULL, 12),(121, 1, NULL, NULL, 13),(122, 2, NULL, NULL, 13),(123, 3, NULL, NULL, 13),(124, 4, NULL, NULL, 13),(125, 5, NULL, NULL, 13),(126, 6, NULL, NULL, 13),(127, 7, NULL, NULL, 13),(128, 8, NULL, NULL, 13),(129, 9, NULL, NULL, 13),(130, 10, NULL, NULL, 13),(131, 1, NULL, NULL, 14),(132, 2, NULL, NULL, 14),(133, 3, NULL, NULL, 14),(134, 4, NULL, NULL, 14),(135, 5, NULL, NULL, 14),(136, 6, NULL, NULL, 14),(137, 7, NULL, NULL, 14),(138, 8, NULL, NULL, 14),(139, 9, NULL, NULL, 14),(140, 10, NULL, NULL, 14),(141, 1, NULL, NULL, 15),(142, 2, NULL, NULL, 15),(143, 3, NULL, NULL, 15),(144, 4, NULL, NULL, 15),(145, 5, NULL, NULL, 15),(146, 6, NULL, NULL, 15),(147, 7, NULL, NULL, 15),(148, 8, NULL, NULL, 15),(149, 9, NULL, NULL, 15),(150, 10, NULL, NULL, 15),(151, 1, NULL, NULL, 16),(152, 2, NULL, NULL, 16),(153, 3, NULL, NULL, 16),(154, 4, NULL, NULL, 16),(155, 5, NULL, NULL, 16),(156, 6, NULL, NULL, 16),(157, 7, NULL, NULL, 16),(158, 8, NULL, NULL, 16),(159, 9, NULL, NULL, 16),(160, 10, NULL, NULL, 16),(161, 1, NULL, NULL, 17),(162, 2, NULL, NULL, 17),(163, 3, NULL, NULL, 17),(164, 4, NULL, NULL, 17),(165, 5, NULL, NULL, 17),(166, 6, NULL, NULL, 17),(167, 7, NULL, NULL, 17),(168, 8, NULL, NULL, 17),(169, 9, NULL, NULL, 17),(170, 10, NULL, NULL, 17),(171, 1, 'Crud depositos', NULL, 18),(172, 2, 'Depositos ventas', NULL, 18),(173, 3, NULL, NULL, 18),(174, 4, NULL, NULL, 18),(175, 5, NULL, NULL, 18),(176, 6, NULL, NULL, 18),(177, 7, NULL, NULL, 18),(178, 8, NULL, NULL, 18),(179, 9, NULL, NULL, 18),(180, 10, NULL, NULL, 18);

COMMIT;
BEGIN;

DELETE FROM `roy`.`pais`;
INSERT INTO `roy`.`pais` (`id`,`pais`,`impuesto`,`simbolo`,`subsidiaria`,`nombre_sbs`) VALUES (1, 'Guatemala', 1.12, 'Q', 1, 'Roy Guatemala'),(2, 'Honduras', 1.15, 'L', 2, 'Roy Honduras');

COMMIT;
BEGIN;

DELETE FROM `roy`.`perfil`;
INSERT INTO `roy`.`perfil` (`id`,`perfil`,`descripcion`) VALUES (1, 'Sysadmin', 'Administra completamente el sistema web');

COMMIT;
BEGIN;

DELETE FROM `roy`.`tipopago`;
INSERT INTO `roy`.`tipopago` (`id`,`tipo`,`descripcion`,`porcentaje`) VALUES (1, 'DEPOSITO', 'EFECTIVO', 4.50),(2, 'VISA', '1 VISA CUOTA', 4.25),(3, 'VISA', '3 VISA CUOTAS', 5.75),(4, 'VISA', '6 VISA CUOTAS', 7.00),(5, 'VISA', '9 VISA CUOTAS', 3.30),(6, 'VISA', '12 VISA CUOTAS', 2.20),(7, 'VISA', '15 VISA CUOTAS', 2.20),(8, 'CREDO', '1 CREDI CUOTA', 4.25),(9, 'CREDO', '3 CREDI CUOTAS', 4.75),(10, 'CREDO', '6 CREDI CUOTAS', 5.50),(11, 'CREDO', '9 CREDI CUOTAS', 2.20),(12, 'CREDO', '12 CREDI CUOTAS', 2.20),(13, 'CREDO', '15 CREDI CUOTAS', 2.20),(14, 'VISA', '10 VISA CUOTAS', 4.50),(15, 'VISA', '2 VISA CUOTAS', 1.50),(16, 'VISA', '18 VISA CUOTAS', 1.50),(17, 'CREDO', '10 CREDI CUOTAS', 1.50),(18, 'VISA', '24 VISA CUOTAS', 7.00),(19, 'CREDO', '24 CREDI CUOTAS', 7.00);

COMMIT;
BEGIN;

DELETE FROM `roy`.`user`;
INSERT INTO `roy`.`user` (`id`,`pnombre`,`snombre`,`papellido`,`sapellido`,`email`,`user`,`pass`,`estado`,`imagen`,`fechaCreacion`,`ultimaModificacion`,`pais_id`,`perfil_id`,`departamento_id`) VALUES (1, 'Nelson', 'Ovidio', 'Vásquez', 'Ventura', 'it.tiendas@calzadoroy.com', 5254, '$2y$10$o.Q7.Cx6XbGqyGJTVlOsMelqQ35S2fdFJtBq6wsnUIjmJIBYJHnEq', 1, '5254.jpg', '2020-11-18 16:11:07', '2020-11-25 12:53:23', 1, 1, 81),(2, 'Henrry', 'Wilmer', 'Garcia', '', 'informatica.tiendas@calzadoroy.com', 5202, '$2y$10$OtyK4/GiUfdl0wMz2V34ZeZ/kBg2LGir3LgcmOnX1NLUDvypJkUDC', 1, '5202.jpg', '2020-11-25 12:59:46', '2021-01-19 11:31:51', 2, 1, 81);

COMMIT;
